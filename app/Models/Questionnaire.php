<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'start_date','end_date','is_active'];

    public function questions() { 
        return $this->hasMany(Question::class)->orderBy('order'); 
    }
}
