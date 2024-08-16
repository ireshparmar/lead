<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StudentWorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'from_date',
        'to_date',
        'company_name',
        'company_address',
        'is_working',
        'job_type',
        'job_description',
        'designation',
        'isVerified',
        'remark',
        'created_by',
        'updated_by',
        'verified_by',
        'verified_date',
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

    // Define relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
