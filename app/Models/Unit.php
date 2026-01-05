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

    protected $guarded = [];

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

    // public function updater()
    // {
    //     return $this->belongsTo(User::class, 'updated_by');
    // }

    public function lastPrinter()
    {
        return $this->belongsTo(User::class, 'last_printed_by');
    }
}
