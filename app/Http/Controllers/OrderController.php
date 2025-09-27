<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Отображает форму создания заказа.
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Сохраняет новый заказ.
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
            'products'     => 'nullable|array',
        ]);

        $orderService->createOrder($validatedData);

        return redirect()->route('orders.create')->with('success', 'Заказ успешно создан!');
    }


    /**
     * Отображает список заказов с фильтрацией и статистикой.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // Упрощенная фильтрация по полям самого заказа
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_full_name', 'like', "%{$search}%")
                  ->orWhere('customer_company_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($subq) use ($search) {
                      $subq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Подсчет статистики (остается без изменений)
        $statsQuery = clone $query;
        $stats = [
            'total'       => $statsQuery->count(),
            'new'         => (clone $statsQuery)->where('status', 'новый')->count(),
            'in_progress' => (clone $statsQuery)->where('status', 'в работе')->count(),
            'completed'   => (clone $statsQuery)->where('status', 'завершён')->count(),
        ];

        // Получение списка заказов (без 'customer')
        $orders = $query->with('items')->latest()->get();

        return view('orders.index', compact('orders', 'stats'));
    }
}