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
        'agent_commission',
        'own_commission',
        'status',
        'remarks',
        'created_by',
        'updated_by',
        'base_currency',
        'base_currency_rate',
        'payment_currency',
        'payment_date',
        'invoice_type',
        'agent_payment_date',
        'agent_payment_status',
        'agent_payment_status_updated_by',
        'agent_payment_remarks'


    ];

    public function getOwnCommissionInBaseCurrencyAttribute()
    {
        return $this->own_commission;
    }

    public function getAgentCommissionInBaseCurrencyAttribute()
    {
        return $this->agent_commission;
    }

    /**
     * Belongs-to relationship with Commission.
     */
    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
