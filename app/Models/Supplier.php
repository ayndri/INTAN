<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
  use HasFactory;

  protected $table = 'suppliers';

  protected $fillable = [
    'name',
    'email',
    'phone_code',
    'phone',
    'address',
    'city_id',
    'country_id',
    'avatar',
    'description',
    'code'
  ];

  /**
   * Relationship to Purchase model.
   */
  public function purchases()
  {
    return $this->hasMany(Purchase::class);
  }

  public function country()
  {
    return $this->belongsTo(Country::class, 'country_id');
  }
}
