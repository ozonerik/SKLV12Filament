<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Authenticatable implements FilamentUser
{
    use HasFactory;

    protected $fillable = ['major_id', 'school_year_id', 'name', 'pob', 'dob', 'nis', 'nisn', 'father_name', 'jenis_kelamin', 'password'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'password' => 'hashed',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $student): void {
            if (blank($student->dob)) {
                return;
            }

            // Default password siswa = tanggal lahir ddmmyyyy (akan di-hash oleh cast).
            if ($student->isDirty('dob') || blank($student->password)) {
                $student->password = $student->dob->format('dmY');
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'siswa';
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function schoolYear() { 
        return $this->belongsTo(SchoolYear::class); 
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
