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

    public function createdBy(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function updatedBy(){
        return $this->hasOne(User::class, 'id','updated_by');
    }
}
