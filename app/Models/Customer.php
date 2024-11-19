<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  use HasFactory;

  protected $table = 'customers';

  protected $fillable = [
    'name',
    'email',
    'phone_code',
    'phone',
    'address',
    'status',
    'city_id',
    'country_id',
    'avatar',
    'description',
    'code'
  ];

  /**
   * Relationship to Sale model.
   */
  public function sales()
  {
    return $this->hasMany(Sale::class);
  }

  public function country()
  {
    return $this->belongsTo(Country::class, 'country_id');
  }
}
