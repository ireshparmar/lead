<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StudentVisa extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'student_admission_id',
        'visa_type',
        'intakemonth_id',
        'intakeyear_id',
        'status',
        'app_submission_date',
        'visa_no',
        'visa_date',
        'expire_date',
        'visa_done',
        'travel_date',
        'ticket',
        'contact_detail',
        'address',
        'more_detail',
        'remark',
        'created_by',
        'updated_by',
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
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function studentAdmission()
    {
        return $this->belongsTo(StudentAdmission::class, 'student_admission_id')->with(['college', 'country', 'degree']);
    }

    public function intakeMonth()
    {
        return $this->belongsTo(Intakemonth::class, 'intakemonth_id');
    }

    public function intakeYear()
    {
        return $this->belongsTo(Intakeyear::class, 'intakeyear_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function visaDocuments()
    {
        return $this->hasMany(StudentVisaDocument::class);
    }
}
