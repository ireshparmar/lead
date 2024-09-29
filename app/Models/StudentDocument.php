<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class StudentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_name',
        'doc_org_name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'verified_by',
        'verified_date',
        'remark',
        'note',
        'isVerified',
        'doc_type_id'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
    public function updatedBy()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    /**
     * Get the user who last verified the record.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function docType()
    {
        return $this->hasOne(DocumentType::class, 'id', 'doc_type_id');
    }
}
