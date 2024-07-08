<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogFile extends Model
{
    use HasFactory;
    protected $table = "log_files";
    protected $fillable = [
        'id_email',
        'name',
        'malicious',
        'harmless',
        'suspicious',
        'undetected',
        'timeout',
        'self_url'
    ];
}
