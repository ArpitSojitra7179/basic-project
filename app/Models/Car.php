<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Car extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'user_id',
        'brand_name',
        'car_name',
        'price',
        'details',
        'car_token',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    protected $touches = ['user'];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
}
