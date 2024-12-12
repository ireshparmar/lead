<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['currency_unit', 'currency', 'base_currency_rate', 'base_currency', 'created_by', 'updated_by'];
}
