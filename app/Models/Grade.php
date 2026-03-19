<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'subject_id', 'score'];

    public function subject() { return $this->belongsTo(Subject::class); }
    
    public function student() { return $this->belongsTo(Student::class); }
}
