<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VariantComponent extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'variant_components';

    protected $fillable = [
        'variant_id',
        'component_id',
        'quantity',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }
}
