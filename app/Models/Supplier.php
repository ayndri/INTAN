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
        'phone',
        'address',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean', // Status akan dikonversi menjadi boolean (true/false)
    ];

    /**
     * Relationship to Purchase model.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
