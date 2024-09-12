<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class StudentAdmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'country_id',
        'course_id',
        'degree_id',
        'college_id',
        'campus_id',
        'min_eligibility',
        'duration',
        'facility',
        'document',
        'fees',
        'fees_currency',
        'status',
        'remark',
        'reference_portal_id',
        'ref_link',
        'eligibility',
        'intakemonth_id',
        'intakeyear_id',
        'created_by',
        'updated_by',
        'is_move_to_visa',
        'allocate_to',
        'allocated_user',
        'note',
        'app_number',
        'application_id',
        'is_admission_done'


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

    public function interestedCourse()
    {
        return $this->belongsTo(StudentInterestedCourse::class, 'interested_course_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class)->with('degree');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }
    public function degree()
    {
        return $this->belongsTo(Degree::class, 'degree_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function referencePortal()
    {
        return $this->belongsTo(ReferencePortal::class, 'reference_portal_id');
    }

    public function minEligibility()
    {
        return $this->belongsTo(Eligibility::class, 'min_eligibility');
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

    public function allocatedUser()
    {
        return $this->belongsTo(User::class, 'allocated_user');
    }

    public function admissionDocuments()
    {
        return $this->hasMany(StudentAdmissionDocument::class);
    }
}
