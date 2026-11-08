<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'Price_before_discount',
        'total_price',
        'status',
        'user_id',
        'address_id',
        'payment',
        'code_id'
    ];


    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function adress(){
        return $this->belongsTo(UserAdress::class, 'address_id', 'id');
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function code(){
        return $this->belongsTo(DiscountCode::class, 'code_id', 'id')->withDefault();
    }

}
