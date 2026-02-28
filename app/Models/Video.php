<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'title',
        'url',
    ];

    public function comment() {
        return $this->morphOne(Comment::class, 'commentable');
    }

    public function comments() {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function tags() {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
