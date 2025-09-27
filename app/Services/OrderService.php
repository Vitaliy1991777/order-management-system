<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;

class OrderService
{
    /**
     * Создает новый заказ.
     *
     * @param array $data Валидированные данные.
     * @return Order
     */
    public function createOrder(array $data): Order
    {
        $customer = Customer::updateOrCreate(
            ['phone' => $data['phone']],
            [
                'full_name' => $data['full_name'],
                'email' => $data['email'] ?? null,
                'tin' => $data['tin'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'address' => $data['address'] ?? null,
            ]
        );

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => 'новый',
        ]);

        if (isset($data['products'])) {
            foreach ($data['products'] as $productData) {
                if (!empty($productData['name'])) {
                    $order->items()->create($productData);
                }
            }
        }

        return $order->load(['customer', 'items']);
    }
}