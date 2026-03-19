<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Headmaster extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'rank', 'nip', 'ttd', 'is_active'];

    public function schoolYears(): HasMany
    {
        return $this->hasMany(SchoolYear::class);
    }
}
