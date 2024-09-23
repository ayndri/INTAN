<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'brands';

    // Primary key
    protected $primaryKey = 'id';

    // Fillable fields
    protected $fillable = [
        'brand_name',
        'description',
        'status',
    ];

    // Timestamps
    public $timestamps = true;

    // Casting status as a boolean
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relationship with the Product model
     * Assuming the products table has a foreign key 'brand_id'
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }
}
