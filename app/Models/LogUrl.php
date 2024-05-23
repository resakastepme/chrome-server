<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogUrl extends Model
{
    use HasFactory;
    protected $table = 'log_urls';
    protected $fillable = [
        'id_email',
        'href',
        'harmless',
        'malicious',
        'suspicious',
        'timeout',
        'undetected',
        'self_url'
    ];
}
