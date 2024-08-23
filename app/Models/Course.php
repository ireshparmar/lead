<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id',
        'college_id',
        'campus_id',
        'stream_id',
        'eligibility_id',
        'degree_id',
        'course_description',
        'duration',
        'fees',
        'facility',
        'document',
        'remarks',
        'other',
        'broucher',
        'program_link',
        'created_by',
        'updated_by',
        'agent_comission',
        'own_comission',
        'eligibility'

    ];

    /**
     * Get the country associated with the course.
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the college associated with the course.
     */
    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    /**
     * Get the campus associated with the course.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    /**
     * Get the stream associated with the course.
     */
    public function stream()
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }

    /**
     * Get the eligibility associated with the course.
     */
    public function minEligibility()
    {
        return $this->belongsTo(Eligibility::class, 'eligibility_id');
    }

    /**
     * Get the degree associated with the course.
     */
    public function degree()
    {
        return $this->belongsTo(Degree::class, 'degree_id');
    }

    /**
     * Get the user who created the course.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the course.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
