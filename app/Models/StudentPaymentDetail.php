<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StudentPaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'student_fee_id',
        'payment_amount',
        'payment_date',
        'payment_mode',
        'remark',
        'created_by',
        'updated_by'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentFees()
    {
        return $this->belongsTo(StudentFee::class, 'student_fee_id');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}
