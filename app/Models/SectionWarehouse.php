<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class SectionWarehouse extends Pivot
{
    // kalau anda pakai UUID sebagai PK:
    public $incrementing = false;
    protected $keyType = 'string';

    // pastikan nama tabel sesuai (biasanya plural)
    protected $table = 'section_warehouses';

    // pastikan kolom primary key bernama 'id' di migration pivot
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
