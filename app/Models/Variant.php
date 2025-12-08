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

    protected $fillable = [
        'company_item_id',
        'brand_id',
        'name',
        'default_location',
        'spec_json',
        'initialized_at',
        'initialized_by',
        'is_active',
    ];

    protected $casts = [
        'spec_json' => 'array',
        'is_active' => 'boolean',
        'initialized_at' => 'datetime',
    ];

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
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
