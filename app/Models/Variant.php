<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];


    public function companyItem()
    {
        return $this->belongsTo(CompanyItem::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function photos()
    {
        return $this->hasMany(VariantPhoto::class);
    }

    public function components()
    {
        return $this->belongsToMany(Component::class, 'variant_components')
            ->withPivot('quantity_needed')
            ->withTimestamps();
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
