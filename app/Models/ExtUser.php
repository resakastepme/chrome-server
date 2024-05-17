<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtUser extends Model
{
    use HasFactory;
    protected $table = 'ext_users';
    protected $fillable = [
        'user_hash',
        'device',
        'extStat',
        'autoScan'
    ];
}
