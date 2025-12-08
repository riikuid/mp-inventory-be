<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BufferStock extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'company_item_id',
        'brand_id',
        'location',
        'min_quantity',
    ];

    public function companyItem()
    {
        return $this->belongsTo(CompanyItem::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
