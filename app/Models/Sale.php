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
  // Define the fillable fields for mass assignment
  protected $fillable = [
    'customer_id',
    'total',
    'sale_date',
    'status',
    'order_type',
    'shipping_cost',
  ];

  // Relationship with SaleProduct (One-to-Many)
  public function saleProducts()
  {
    return $this->hasMany(SaleProduct::class);
  }

  public function customer()
  {
    return $this->belongsTo(Customer::class, 'customer_id');
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
