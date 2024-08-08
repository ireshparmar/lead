<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'purpose_id',
        'service_name',
        'status',
        'created_by',
        'updated_by',
    ];

    public function purpose(){
        return $this->belongsTo(Purpose::class);
    }

    public function createdBy(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function updatedBy(){
        return $this->hasOne(User::class, 'id','updated_by');
    }
}
