<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'updated_by',
        'agent_id',
        'date',
        'amount',
        'category_id',
        'description',
        'doc_name',
        'doc_org_name'
    ];

    protected $casts = [
        'doc_name' => 'array',
        'doc_org_name' => 'array'

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
