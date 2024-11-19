<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use HasFactory;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'products';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'sku',
    'category_id',
    'brand_id',
    'unit_id',
    'item_code',
    'description',
    'product_type',
    'sell_price',
    'quantity',
    'quantity_alert',
  ];

  /**
   * Get the category associated with the product.
   */
  public function category()
  {
    return $this->belongsTo(Category::class, 'category_id');
  }

  /**
   * Get the brand associated with the product.
   */
  public function brand()
  {
    return $this->belongsTo(Brand::class, 'brand_id');
  }

  /**
   * Get the unit associated with the product.
   */
  public function unit()
  {
    return $this->belongsTo(Unit::class, 'unit_id');
  }

  /**
   * Get the images associated with the product.
   */
  public function images()
  {
    return $this->hasMany(ProductImage::class, 'product_id');
  }

  /**
   * Get the variant products associated with the product.
   */
  public function variantProducts()
  {
    return $this->hasMany(VariantProduct::class, 'product_id');
  }
}
