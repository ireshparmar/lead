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
        'lead_unique_id',
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
        'amount',
        'is_imported',
        'created_date',
        'refund_amount',
        'refund_reson',
        'refund_docs'


    ];
    protected $casts = [
        'refund_docs' => 'array',
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

    public function lead_docs(){
        return $this->hasMany(LeadDoc::class);
    }

    public function payments(){
        return $this->hasMany(LeadPayment::class);
    }

    public function hasPccDocument()
    {
        return $this->lead_docs()->where('doc_type', 'pcc')->exists();
    }

    public function country(){
        return $this->belongsToMany(Country::class,'lead_country'); //actually every lead has one country
    }

    public function lead_reminders(){
        return $this->hasMany(LeadReminder::class);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereYear('created_at', $year)
                     ->whereMonth('created_at', $month);
    }

}
