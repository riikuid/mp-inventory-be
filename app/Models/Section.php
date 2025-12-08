<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['department_id', 'department_code', 'name', 'code'];

    /**
     * Logic Otomatis (Optional): Mengisi department_code saat department_id diisi.
     * Ini menjaga konsistensi data denormalisasi.
     */
    protected static function booted()
    {
        static::saving(function ($section) {
            if ($section->isDirty('department_id') && $section->department) {
                $section->department_code = $section->department->code;
            }
        });
    }

    /**
     * Relasi: Section milik satu Department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relasi: Section bisa terhubung ke banyak Warehouse (Many-to-Many)
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'section_warehouses')
            ->using(SectionWarehouse::class)
            ->withPivot('id') // Karena pivot table punya ID sendiri
            ->withTimestamps();
    }
}
