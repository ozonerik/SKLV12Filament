<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Major extends Model
{
    use HasFactory;

    protected $fillable = ['bidang_keahlian', 'program_keahlian', 'konsentrasi_keahlian'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
