<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantProduct extends Model
{
  use HasFactory;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'variant_products';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'variant_id',
    'product_id',
    'value',
    'price',
    'quantity',
  ];

  /**
   * Get the variant associated with this product variant.
   */
  public function variant()
  {
    return $this->belongsTo(Variant::class, 'variant_id');
  }

  /**
   * Get the product associated with this product variant.
   */
  public function product()
  {
    return $this->belongsTo(Product::class, 'product_id');
  }
}
