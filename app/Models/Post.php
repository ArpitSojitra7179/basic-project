<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory,  HasApiTokens;

    protected $fillable = [
        'title',
        'body',
    ];

    public function comment() {
        return $this->morphOne(Comment::class, 'commentable');
    }

    public function comments() {
        return $this->morphMany(Comment::class, 'commentable');
    }
    
    public function latestComment() {
        return $this->morphOne(Comment::class, 'commentable')->latestOfMany();
    }

    public function oldestComment() {
        return $this->morphOne(Comment::class, 'commentable')->oldestOfMany();
    }

    public function tags() {
        return $this->morphToMany(Tag::class, 'taggable');
    } 

}
