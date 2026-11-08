<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAdress extends Model
{
    use SoftDeletes;
    //

    protected $fillable = [
        'name',
        'address',
        'location',
        'city',
        'isMain',
        'user_id',
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }



    public function orders(){
        return $this->hasMany(Order::class, 'user_id', 'id');
    }
}
