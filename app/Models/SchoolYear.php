<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolYear extends Model
{
    use HasFactory;

    protected $fillable = ['kode', 'name','headmaster_id'];

    public function headmaster()
    {
        return $this->belongsTo(Headmaster::class);
    }
}
