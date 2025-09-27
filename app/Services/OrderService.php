<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;

class OrderService
{
    /**
     * Создает новый заказ.
     * Данные существующего клиента не перезаписываются.
     *
     * @param array $data Валидированные данные из запроса.
     * @return Order Созданный объект заказа со связанными моделями.
     */
    public function createOrder(array $data): Order
    {
        // Шаг 1: Ищем клиента по номеру телефона.
        $customer = Customer::where('phone', $data['phone'])->first();

        // Шаг 2: Если клиент НЕ найден, ТОЛЬКО ТОГДА мы его СОЗДАЕМ.
        if (!$customer) {
            $customer = Customer::create([
                'phone'        => $data['phone'],
                'full_name'    => $data['full_name'],
                'email'        => $data['email'] ?? null,
                'tin'          => $data['tin'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'address'      => $data['address'] ?? null,
            ]);
        }
        // Если клиент был найден на Шаге 1, его данные полностью игнорируются,
        // и он не обновляется.

        // Шаг 3: Создаем заказ, используя ID найденного или только что созданного клиента.
        $order = Order::create([
            'customer_id' => $customer->id,
            'status'      => 'новый',
        ]);

        // Шаг 4: Добавляем товары к заказу.
        if (isset($data['products'])) {
            foreach ($data['products'] as $productData) {
                if (!empty($productData['name'])) {
                    $order->items()->create($productData);
                }
            }
        }

        // Шаг 5: Возвращаем созданный заказ со всеми данными.
        return $order->load(['customer', 'items']);
    }
}