<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'module',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'module' => 'array', // Cast to array for JSON handling
    ];

    // Accessor to get array of modules
    public function getModuleAttribute()
    {
        return $this->attributes['module'];
    }

    // Mutator to set module as comma-separated string
    public function setModuleAttribute($value)
    {
        $this->attributes['module'] = is_array($value) ? json_encode($value) : $value;
    }

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
