<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vision',
        'address',
        'city',
        'postal_code',
        'website',
        'email',
        'phone',
        'province',
        'kcd_wilayah',
        'province_logo',
        'school_logo',
        'school_stamp',
    ];
}
