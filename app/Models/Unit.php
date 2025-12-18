<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'variant_id',
        'component_id',
        'parent_unit_id',
        'qr_value',
        'status',
        'rack_id',

        'print_count',
        'last_printed_by',
        'last_printed_at',

        'synced_at',
        'last_modified_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'last_printed_at' => 'datetime',
        'synced_at' => 'datetime',
        'last_modified_at' => 'datetime',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function component()
    {
        return $this->belongsTo(Component::class);
    }

    public function parent()
    {
        return $this->belongsTo(Unit::class, 'parent_unit_id');
    }

    public function children()
    {
        return $this->hasMany(Unit::class, 'parent_unit_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function lastPrinter()
    {
        return $this->belongsTo(User::class, 'last_printed_by');
    }
}
