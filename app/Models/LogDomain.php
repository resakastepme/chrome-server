<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogDomain extends Model
{
    use HasFactory;
    protected $table = 'log_domains';
    protected $fillable = [
        'id_email',
        'domain',
        'malicious',
        'harmless',
        'suspicious',
        'timeout',
        'undetected'
    ];
}
