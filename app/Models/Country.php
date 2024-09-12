<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'iso2',
        'currency'
    ];

    public function lead()
    {
        return $this->belongsToMany(Lead::class, 'lead_country');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('specificCountries', function (Builder $builder) {
            $specificCountries = [
                'AU',
                'CA',
                'HR',
                'FI',
                'DE',
                'HU',
                'LV',
                'LT',
                'PL',
                'RU',
                'IN'
            ];
            $builder->whereIn('iso2', $specificCountries);
        });
    }

    public function course()
    {
        return $this->hasMany(Course::class, 'country_id', 'id');
    }

    public function studentInterestedCourses()
    {
        return $this->hasMany(StudentInterestedCourse::class);
    }

    public function collegeApplication()
    {
        return $this->hasMany(StudentCollegeApplication::class, 'country_id');
    }
}
