<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Nama tabel yang terkait dengan model ini
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'sku',
        'price',
        'cost',
        'stock',
        'unit_id',
        'brand_id',
        'status',
        'product_image',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Relationships to Inventory Movements.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Relationships to Sales.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Relationships to Purchases.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Relationship to Unit.
     * Each product belongs to a single unit.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relationship to Brand.
     * Each product belongs to a single brand.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
