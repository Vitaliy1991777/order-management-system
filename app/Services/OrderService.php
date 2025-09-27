<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function createOrder(array $data): Order
    {
        $order = Order::create([
            'customer_full_name'    => $data['full_name'],
            'customer_phone'        => $data['phone'],
            'customer_email'        => $data['email'] ?? null,
            'customer_tin'          => $data['tin'] ?? null,
            'customer_company_name' => $data['company_name'] ?? null,
            'customer_address'      => $data['address'] ?? null,
        ]);

        if (isset($data['products'])) {
            foreach ($data['products'] as $productData) {
                if (!empty($productData['name'])) {
                    $order->items()->create($productData);
                }
            }
        }

        return $order->load('items');
    }
}