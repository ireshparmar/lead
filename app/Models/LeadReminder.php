<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadReminder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lead_id',
        'reminder_date_time',
        'status',
        'created_by',
        'updated_by',
        'note'


    ];

    public function scopeUpcomingReminders($query)
    {
        $query->whereBetween('reminder_date_time', [Carbon::now(), Carbon::now()->addDays(20)]);
        if(!auth()->user()->hasRole('Admin')){
             $query->where('created_by',auth()->user()->id);
        }
        return $query;
    }
}
