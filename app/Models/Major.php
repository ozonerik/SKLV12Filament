<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = ['bidang_keahlian', 'program_keahlian', 'konsentrasi_keahlian'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
