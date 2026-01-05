<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    /**
     * Relasi: Warehouse digunakan oleh banyak Section (Many-to-Many)
     */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'section_warehouses')
            ->using(SectionWarehouse::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * Relasi: Warehouse memiliki banyak Rack
     */
    public function racks(): HasMany
    {
        return $this->hasMany(Rack::class);
    }
}
