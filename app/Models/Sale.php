<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales'; // Nama tabel di database

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'customer_id',
        'quantity',
        'selling_price',
        'total',
        'sale_date',
        'status',
        'order_type',
        'shipping_cost',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'selling_price' => 'decimal:2',
        'total' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    /**
     * Relationship to Product model.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // public function calculateTotal()
    // {
    //     return ($this->selling_price * $this->quantity) + ($this->order_type == 'online' ? $this->shipping_cost : 0);
    // }
}
