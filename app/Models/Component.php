<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Component extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'name',
        'brand_id',
        'manuf_code',
        'spec_json',
        'is_active',
    ];

    protected $casts = [
        'spec_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->belongsToMany(Variant::class, 'variant_components')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
