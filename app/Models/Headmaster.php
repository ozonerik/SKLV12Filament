<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Headmaster extends Model
{
    protected $fillable = ['name', 'nip', 'rank', 'is_active'];

    public function schoolYears(): HasMany
    {
        return $this->hasMany(SchoolYear::class);
    }
}
