<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    //
    protected $fillable = [
        'code', 'Percentage'
    ];


    public function orders(){
        return $this->hasMany(Order::class, 'code_id', 'id');
    }
}
