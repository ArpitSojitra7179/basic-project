<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'body',
    ];

    public function commentable() {
        return $this->morphTo();
    }
}
