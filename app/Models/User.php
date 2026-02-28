<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Reqres;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'credit',
        'email_verified_at',
        'password',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function car() {
        return $this->hasOne(Car::class);
    }

    public function cars() {
        return $this->hasMany(Car::class);
    }

    public function latestCar() {
        return $this->hasOne(Car::class)->latestOfMany();
    }

    public function oldestCar() {
        return $this->hasOne(Car::class)->oldestOfMany();
    }

    public function highpriceCar() {
        return $this->hasOne(Car::class)->ofMany('price', 'max');
    }

    public function hasRole($role)
    {
        return strtolower($this->role) === strtolower($role);
    }

    protected function name(): Attribute {
        return Attribute::make(
            get: fn ($value) => ucfirst($value)
        );
    }

    public function events() {
        return $this->hasMany(Event::class);
    }

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }

    public function reqres() {
        return $this->hasOne(Reqres::class);
    }

}
