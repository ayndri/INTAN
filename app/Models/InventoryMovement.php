<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'inventory_movements'; // Nama tabel di database

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'sale_id',
        'purchase_id',
        'type',
        'quantity',
        'transaction_date',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    /**
     * Relationship to Product model.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship to Sale model.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relationship to Purchase model.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
