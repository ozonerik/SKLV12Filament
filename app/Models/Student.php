<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['major_id', 'name', 'pob', 'dob', 'nis', 'nisn', 'father_name', 'password'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'password' => 'hashed',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function skl()
    {
        return $this->hasOne(Skl::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function getAverageGradeAttribute()
    {
        return $this->grades()->avg('score') ?? 0;
    }
}
