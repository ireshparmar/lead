<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentDoc extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_name',
        'doc_org_name',
        'doc_type',
        'agent_id',
        'mime_type',
        'other_type',
        'created_by'
    ];
}
