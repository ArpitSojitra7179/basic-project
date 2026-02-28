<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Reqres extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'reqres_id',
        'name',
        'job',
        'createdAt',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
