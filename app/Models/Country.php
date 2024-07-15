<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status'
    ];

    public function lead(){
        return $this->belongsToMany(Lead::class,'lead_country');
    }
}
