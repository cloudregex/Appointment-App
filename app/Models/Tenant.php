<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'db_host',
        'db_port',
        'db_name',
        'db_username',
        'db_password',
    ];
}
