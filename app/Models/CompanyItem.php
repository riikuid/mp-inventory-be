<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_id',
        'company_code',
        'is_set',
        'has_components',
        'initialized_at',
        'initialized_by',
        'notes',
    ];

    protected $casts = [
        'is_set' => 'boolean',
        'has_components' => 'boolean',
        'initialized_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function bufferStocks()
    {
        return $this->hasMany(BufferStock::class);
    }
}
