<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogText extends Model
{
    use HasFactory;
    protected $table = 'log_texts';
    protected $fillable = [
        'id_email',
        'original',
        'translated',
        'assistant'
    ];
}
