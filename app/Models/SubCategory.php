<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'name',
        'category_id'
    ];



    public function products(){
        return $this->hasMany(Product::class, 'sub_category_id', 'id');
    }


    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
