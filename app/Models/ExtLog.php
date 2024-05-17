<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtLog extends Model
{
    use HasFactory;
    protected $table = 'ext_logs';
    protected $fillable = [
        'user_hash',
        'message'
    ];
}
