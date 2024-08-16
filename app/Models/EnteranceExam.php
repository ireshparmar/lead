<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnteranceExam extends Model
{
    protected $fillable = [
        'name',
        'status',
        'type',
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}
