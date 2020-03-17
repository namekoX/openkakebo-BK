<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'user_id', 'category_kbn', 'category_name', 'category_order',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function subCategorys()
    {
        return $this->hasMany('App\SubCategory');
    }
}
