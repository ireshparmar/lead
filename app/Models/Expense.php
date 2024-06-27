<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agent_id',
        'date',
        'amount',
        'category_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function agent()
    {
        return $this->belongsTo(User::class);
    }


    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }
}
