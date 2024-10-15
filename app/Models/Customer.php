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
        'phone',
    ];

    /**
     * Relationship to Sale model.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
