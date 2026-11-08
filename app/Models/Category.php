<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'name', 'slug', 'description', 'icon'
    ];



    public function products(){
        return $this->hasMany(Product::class);
    }
    public function subcategory(){
        return $this->hasMany(SubCategory::class, 'category_id', 'id');
    }

    public function users(){
        return $this->hasMany(User::class);
    }


}
