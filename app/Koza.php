<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Koza extends Model
{
    protected $fillable = [
        'user_id', 'koza_name', 'zandaka', 'is_credit', 'credit_date',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
