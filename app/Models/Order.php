<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Связь: "Один заказ принадлежит одному клиенту".
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Связь: "Один заказ имеет много товаров".
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}