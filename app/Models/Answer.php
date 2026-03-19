<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['student_id', 'question_id', 'question_option_id','answer_text'];
}
