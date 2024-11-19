<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  use HasFactory;

  protected $table = 'categories';

  protected $primaryKey = 'id';

  protected $fillable = [
    'name',
    'slug',
    'status',
  ];

  public $timestamps = true;

  protected $casts = [
    'status' => 'boolean',
  ];

  public function products()
  {
    return $this->hasMany(Product::class, 'category_id');
  }
}
