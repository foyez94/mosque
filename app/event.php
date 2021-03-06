<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class event extends Model
{
    use SoftDeletes;

    public function image(){
        return $this->belongsTo('App\image','image_id','id')->withTrashed();
    }
    public function category(){
        return $this->belongsTo('App\eventCategory','category_id','id')->withTrashed();
    }
}
