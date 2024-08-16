<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}
