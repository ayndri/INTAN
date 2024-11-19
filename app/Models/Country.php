<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
  use HasFactory;

  protected $fillable = ['code', 'name', 'phone_code'];

  /**
   * Get the cities for the country.
   */
  public function cities()
  {
    return $this->hasMany(City::class);
  }

  /**
   * Get the phone codes for the country.
   */
  public function states()
  {
    return $this->hasMany(State::class);
  }
}
