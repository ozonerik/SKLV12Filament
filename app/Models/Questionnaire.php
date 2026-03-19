<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    protected $fillable = ['title', 'description', 'start_date','end_date','is_active'];

    public function questions() { 
        return $this->hasMany(Question::class)->orderBy('order'); 
    }
}
