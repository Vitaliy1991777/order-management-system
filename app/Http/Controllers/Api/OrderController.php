<?php

namespace App\Http\Controllers\Api;

use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class OrderController extends Controller
{
    /**
     * Поиск клиента по номеру телефона.
     * GET /api/customers?phone=...
     */
    public function searchCustomer(Request $request)
    {
        if (!$request->has('phone')) {
            return response()->json(['error' => 'Phone parameter is required'], 400);
        }

        $customer = Customer::where('phone', $request->phone)->first();

        if (!$customer) {
            return response()->json(null, 200);
        }

        return response()->json($customer);
    }

    /**
     * Список заказов с фильтрацией.
     * GET /api/orders?date=...&status=...&search=...
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // Фильтр по поисковой строке
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // В ТЗ указан только один параметр 'date', будем считать, что это точная дата
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->with(['customer', 'items'])->latest()->get();

        return response()->json($orders);
    }

    /**
     * Создает новый заказ через API.
     */
    public function store(Request $request, OrderService $orderService)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',                
            'tin' => 'nullable|string|max:255',        
            'company_name' => 'nullable|string|max:255', 
            'address' => 'nullable|string',            
            'products' => 'required|array',
            'products.*.name' => 'required|string',
            'products.*.quantity' => 'required|integer|min:1', 
            'products.*.unit' => 'required|string',  
        ]);

        $order = $orderService->createOrder($validatedData);

        return response()->json($order, 201);
    }

    /**
     * Получение статистики заказов.
     * GET /api/orders/stats
     */
    public function stats()
    {
        $stats = Order::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status'); // pluck создает удобный массив [статус => количество]

        return response()->json($stats);
    }
}