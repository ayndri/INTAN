<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
{
  use HasFactory;

  protected $table = 'sale_products';

  // Define the fillable fields for mass assignment
  protected $fillable = [
    'sale_id',
    'product_id',
    'quantity',
    'selling_price',
    'total',
  ];

  // Relationship with Sale (Belongs to)
  public function sale()
  {
    return $this->belongsTo(Sale::class);
  }

  // Relationship with Product (Belongs to)
  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}
