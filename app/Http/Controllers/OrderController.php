<?php

namespace App\Http\Controllers; 

use App\Services\OrderService;
use App\Models\Customer;    
use App\Models\Order;        
use App\Models\OrderItem;  
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
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'products' => 'nullable|array',
            'products.*.name' => 'nullable|string',
            'products.*.quantity' => 'required_with:products.*.name|integer|min:1', 
            'products.*.unit' => 'required_with:products.*.name|string',        
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

        // Применение фильтров
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($subq) use ($search) {
                    $subq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('items', function($subq) use ($search) {
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

        // Подсчет статистики по отфильтрованным данным.
        // Запрос клонируется, чтобы сохранить исходные фильтры для основного списка.
        $statsQuery = clone $query;
        $stats = [
            'total' => $statsQuery->count(),
            'new' => (clone $statsQuery)->where('status', 'новый')->count(),
            'in_progress' => (clone $statsQuery)->where('status', 'в работе')->count(), // <- ВАЖНО: 'в работе', а не 'in_progress'
            'completed' => (clone $statsQuery)->where('status', 'завершён')->count(),
        ];

        // Получение итогового списка заказов
        $orders = $query->with(['customer', 'items'])->latest()->get();

        return view('orders.index', compact('orders', 'stats'));
    }
}