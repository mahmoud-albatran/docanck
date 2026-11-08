<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    //

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'status',
        'user_id',
        'category_id',
        'sub_category_id',
        'type',
        'count',
        'Wholesale_price'
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function subcategory(){
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id');
    }


    public function orders(){
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

}
