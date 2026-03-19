<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['questionnaire_id', 'question_text', 'type', 'weight','order'];

    public function options() { return $this->hasMany(QuestionOption::class); }

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }
}
