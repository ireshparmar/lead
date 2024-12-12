<?php

namespace App\Models;

use App\Filament\Resources\AgentResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'commissionable_type',
        'commissionable_id',
        'commission_type',
        'own_commission',
        'agent_commission',
        'remarks',
        'created_by',
        'updated_by',
        'status',
        'admission_by',
        'agent_id',
        'reminder_date',
        'base_currency_rate'
    ];

    protected $appends = [
        'own_commission_in_base_currency',
        'agent_commission_in_base_currency'
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
     * Polymorphic relationship.
     */
    public function commissionable()
    {
        return $this->morphTo();
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'commissionable_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'commissionable_id');
    }

    /**
     * One-to-Many relationship with CommissionSemester.
     */
    public function semesters()
    {
        return $this->hasMany(CommissionSemester::class);
    }

    /**
     * One-to-One relationship with Invoice.
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public  function agent()
    {
        return $this->hasOne(User::class, 'id', 'agent_id');
    }
}
