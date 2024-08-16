<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class StudentEducationLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'education_level_id',
        'duration_id',
        'status',
        'isVerified',
        'school_or_uni',
        'degree_or_dept',
        'start_date',
        'end_date',
        'gpa_or_percentage',
        'note',
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

    public function educationLevel()
    {
        return  $this->hasOne(EducationLevel::class, 'id', 'education_level_id');
    }

    public function duration()
    {
        return  $this->hasOne(Duration::class, 'id', 'duration_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    /**
     * Get the user who last verified the record.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
