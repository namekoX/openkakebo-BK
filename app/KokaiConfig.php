<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KokaiConfig extends Model
{
    protected $fillable = [
        'is_open','is_shunyu','is_shunyu_category','is_shishutu','is_shishutu_category','is_togetu','is_zandaka'
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
