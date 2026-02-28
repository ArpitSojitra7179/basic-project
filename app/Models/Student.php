<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Student extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'is_active',
        'is_scholarship',
    ];

    protected $hidden = [
        'is_scholarship',
    ];

    public function courses() {
        return $this->belongsToMany(Course::class)->withPivot('created_at');
    }

    // For append method
    public function getFullNameAttribute() {

        return "{$this->first_name} - {$this->is_active} - {$this->email}";
    }
}
