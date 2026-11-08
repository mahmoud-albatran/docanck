<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
     protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'login_code',
        'email_verified_at',
        'profile_photo_path',
        'login_code_expires_at',
        'role',
        'store_name',
        'category_id',
        'description',
        'location',
        'location_desc'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'login_code_expires_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }




    public function category(){
        return $this->belongsTo(Category::class)->withDefault();
    }


    public function subCategories(){
        return $this->belongsToMany(
            SubCategory::class,
            'user_sub_category',
            'user_id',
            'sub_category_id',
            'id',
            'id'
        );
    }


    public function syncSubCategories(array $subcategories){
        $this->subCategories()->sync($subcategories);
    }


    public function products(){
        return $this->hasMany(Product::class);
    }

    public function addresses(){
        return $this->hasMany(UserAdress::class);
    }

    public function orders(){
        return $this->hasMany(Order::class, 'user_id', 'id');
    }


    public function deviceTokens(){
        return $this->hasMany(DeviceToken::class, 'user_id', 'id');
    }


    public function routeNotificationForFcm($notification = null)
    {
        return $this->deviceTokens()->pluck('token')->toArray();
    }

    public function role()
    {
        return $this->roles()->latest()->limit(1);
    }






}
