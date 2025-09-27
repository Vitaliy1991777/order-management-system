<?php

namespace App\Http\Controllers\Api;

use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Ищет данные клиента в последнем заказе по номеру телефона.
     */
    public function searchCustomer(Request $request)
    {
        $request->validate(['phone' => 'required']);

        $lastOrder = Order::where('customer_phone', $request->phone)->latest()->first();

        if (!$lastOrder) {
            return response()->json(null);
        }

        // Возвращаем "карточку клиента" из данных последнего заказа
        $customerData = [
            'full_name'    => $lastOrder->customer_full_name,
            'phone'        => $lastOrder->customer_phone,
            'email'        => $lastOrder->customer_email,
            'tin'          => $lastOrder->customer_tin,
            'company_name' => $lastOrder->customer_company_name,
            'address'      => $lastOrder->customer_address,
        ];

        return response()->json($customerData);
    }

    /**
     * Возвращает список заказов с фильтрацией.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // Упрощенная фильтрация
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_full_name', 'like', "%{$search}%")
                  ->orWhere('customer_company_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->with('items')->latest()->get();

        return response()->json($orders);
    }

    /**
     * Создает новый заказ через API.
     */
    public function store(Request $request, OrderService $orderService)
    {
        $validatedData = $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'email'        => 'nullable|email',
            'tin'          => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'address'      => 'nullable|string',
            'products'     => 'required|array',
            'products.*.name'     => 'required|string',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit'     => 'required|string',
        ]);

        $order = $orderService->createOrder($validatedData);

        return response()->json($order, 201);
    }

    /**
     * Возвращает статистику по заказам.
     */
    public function stats()
    {
        $stats = Order::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json($stats);
    }
}