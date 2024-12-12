<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_id',
        'invoice_number',
        'total_amount',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * Belongs-to relationship with Commission.
     */
    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }
}
