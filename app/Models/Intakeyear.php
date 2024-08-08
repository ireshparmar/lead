<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intakeyear extends Model
{
    use HasFactory;

    protected $fillable = [
        'inyear_name',
        'status',
        'created_by',
        'updated_by',
    ];

    public function createdBy(){
        return $this->hasOne(User::class,'id','created_by');
    }

    public function updatedBy(){
        return $this->hasOne(User::class, 'id','updated_by');
    }
}
