<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentAdmissionDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'student_admission_id',
        'doc_type_id',
        'doc_name',
        'doc_org_name',
        'remark',
        'created_by',
        'updated_by'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($model) {
            $filePath = $model->doc_name; // Adjust this to your actual file path attribute
            // Check if the file exists and delete it
            if (Storage::disk(config('app.FILE_DISK'))->exists($filePath)) {
                Storage::disk(config('app.FILE_DISK'))->delete($filePath);
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentAdmission()
    {
        return $this->belongsTo(StudentAdmission::class);
    }

    public function docType()
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id');
    }
}
