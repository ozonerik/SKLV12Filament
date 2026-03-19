<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'category']; // Category: Umum, Kejuruan, Pilihan, Mulok
}
