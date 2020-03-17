<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Koza;

class Shushi extends Model
{
    protected $fillable = [
        'user_id', 'shushi_name', 'kingaku', 'hiduke', 'shushi_kbn', 'koza_id', 'category_id', 'sub_category_id', 'before_koza_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'koza_id', 'category_id', 'sub_category_id', 'before_koza_id'
    ];

    public function koza()
    {
        return $this->belongsTo('App\Koza');
    }

    public function beforeKoza()
    {
        return $this->belongsTo('App\Koza', 'before_koza_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function subCategory()
    {
        return $this->belongsTo('App\SubCategory');
    }
}
