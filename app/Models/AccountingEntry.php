<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingEntry extends Model
{
    use HasFactory;

    protected $table = 'accounting_entries'; // Nama tabel di database

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'amount',
        'type',
        'entry_date',
        'sale_id',
        'purchase_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'datetime',
    ];

    /**
     * Relationship to Sale model.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relationship to Purchase model.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
