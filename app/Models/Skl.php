<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skl extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'major_id','school_year_id', 'letter_number', 'status', 
        'letter_date', 'published_at', 'is_questionnaire_completed'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'letter_date' => 'date',
        'is_questionnaire_completed' => 'boolean',
    ];

    public function student() { return $this->belongsTo(Student::class); }

    public function schoolYear() { return $this->belongsTo(SchoolYear::class); }
    
    // Helper untuk cek apakah sudah bisa diakses
    public function isPublished(): bool
    {
        return now()->greaterThanOrEqualTo($this->published_at);
    }
}
