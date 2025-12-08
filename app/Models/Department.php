<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'code'];

    /**
     * Relasi: Satu Department memiliki banyak Section
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
