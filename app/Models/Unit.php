<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $primaryKey = 'id';

    protected $fillable = [
        'unit_name',
        'description',
        'status',
    ];

    public $timestamps = true;

    protected $casts = [
        'status' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}
