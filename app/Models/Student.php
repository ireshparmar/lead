<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'enrollment_number',
        'enrollment_date',
        'birth_date',
        'country_code',
        'mobile',
        'gender',
        'email',
        'inquiry_source_id',
        'address',
        'postal_code',
        'country_id',
        'state_id',
        'city_id',
        'reference_by',
        'purpose_id',
        'service_id',
        'pref_country_id',
        'remark',
        'agent_id',
        'emergency_name',
        'emergency_relation',
        'emergency_contact_code',
        'emergency_contact_no',
        'emergency_detail',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function agent()
    {
        return $this->hasOne(User::class, 'id', 'agent_id');
    }

    public function purpose()
    {
        return $this->hasOne(Purpose::class, 'id', 'purpose_id');
    }

    public function inquirySource()
    {
        return $this->hasOne(InquirySource::class, 'id', 'inquiry_source_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'state_id');
    }

    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function reference()
    {
        return $this->hasOne(Student::class, 'id', 'reference_by');
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function preferredCountry()
    {
        return $this->hasOne(Country::class, 'id', 'pref_country_id');
    }

    public function studentEducationLevels()
    {
        return $this->hasMany(StudentEducationLevel::class);
    }

    public function languageEntranceTest()
    {
        return $this->hasMany(StudentLanguageEntranceTest::class);
    }

    public function aptitudeEntranceTest()
    {
        return $this->hasMany(StudentAptitudeEntranceTest::class);
    }

    public function workExperience()
    {
        return $this->hasMany(StudentWorkExperience::class);
    }

    public function studentDocuments()
    {
        return $this->hasMany(StudentDocument::class);
    }


    public function interestedCourse()
    {
        return $this->hasMany(StudentInterestedCourse::class);
    }

    public function collegeApplication()
    {
        return $this->hasMany(StudentCollegeApplication::class);
    }

    public function studentAdmissions()
    {
        return $this->hasMany(StudentAdmission::class);
    }

    public function studentVisas()
    {
        return $this->hasMany(StudentVisa::class, 'student_id');
    }
}
