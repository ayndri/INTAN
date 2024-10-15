<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases'; // Nama tabel di database

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'supplier_id',
        'quantity',
        'cost_price',
        'total',
        'purchase_date',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'cost_price' => 'decimal:2',
        'total' => 'decimal:2',
        'purchase_date' => 'datetime',
    ];

    /**
     * Relationship to Product model.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship to Supplier model.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relationship to InventoryMovement model.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Relationship to AccountingEntry model.
     */
    public function accountingEntries()
    {
        return $this->hasMany(AccountingEntry::class);
    }
}
