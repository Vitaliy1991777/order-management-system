<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Связь: "Один товар принадлежит одному заказу".
     * (Эта связь нам может не понадобиться, но ее полезно иметь)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}