<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
        'category_id', 'subcategory_name', 'subcategory_order',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
