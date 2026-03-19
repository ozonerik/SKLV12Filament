<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Headmaster extends Model
{
    protected $fillable = ['name', 'rank', 'nip', 'is_active'];

    public function schoolYears(): HasMany
    {
        return $this->hasMany(SchoolYear::class);
    }
}
