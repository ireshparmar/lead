<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'passport_no',
        'address',
        'status',
        'job_offer',
        'pcc',
        'visa_type_id',
        'agent_id',
        'assigned_to',
        'created_by',
        'updated_by',
        'amount'


    ];

    public function visaType(){
        return $this->belongsTo(VisaType::class);
    }

    public function agent(){
        return $this->belongsTo(User::class);
    }

    public function assignedTo(){
        return $this->belongsTo(User::class);
    }

    public function documents(){
        return $this->hasMany(LeadDoc::class);
    }

    public function payments(){
        return $this->hasMany(LeadPayment::class);
    }

    public function hasPccDocument()
    {
        return $this->documents()->where('doc_type', 'pcc')->exists();
    }

}
