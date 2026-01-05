<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Models\BufferStock;
use App\Models\Category;
use App\Models\CompanyItem;
use App\Models\Component;
use App\Models\Product;
use App\Models\Brand;
use App\Models\ComponentPhoto;
use App\Models\Department;
use App\Models\Rack;
use App\Models\Section;
use App\Models\SectionWarehouse;
use App\Models\Unit;
use App\Models\Variant;
use App\Models\VariantComponent;
use App\Models\VariantPhoto;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    /**
     * PULL: ambil data yang berubah sejak timestamp tertentu.
     * GET /api/sync/pull?since=...
     */
    public function pull(Request $request): JsonResponse
    {
        // Default: ambil semua kalau since tidak dikirim
        $since = $request->query('since');

        try {
            $sinceTime = $since ? Carbon::parse($since) : Carbon::createFromTimestamp(0);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Invalid since parameter',
                'message' => $e->getMessage(),
            ], 422);
        }

        $serverTime = Carbon::now();

        // NOTE: Kalau mau include soft-deleted, pakai withTrashed()
        $categories = Category::where('updated_at', '>', $sinceTime)->get();
        $departments = Department::where('updated_at', '>', $sinceTime)->get();
        $sections = Section::where('updated_at', '>', $sinceTime)->get();
        $warehouses = Warehouse::where('updated_at', '>', $sinceTime)->get();
        $section_warehouses = SectionWarehouse::where('updated_at', '>', $sinceTime)->get();
        $racks = Rack::where('updated_at', '>', $sinceTime)->get();
        $brands     = Brand::where('updated_at', '>', $sinceTime)->get();
        $products   = Product::where('updated_at', '>', $sinceTime)->get();

        $companyItems = CompanyItem::withTrashed()
            ->where('updated_at', '>', $sinceTime)
            ->get();

        $variants = Variant::withTrashed()
            ->where('updated_at', '>', $sinceTime)
            ->get();

        $variantPhotos = VariantPhoto::withTrashed()
            ->where('updated_at', '>', $sinceTime)
            ->get();

        $components = Component::withTrashed()
            ->where('updated_at', '>', $sinceTime)
            ->get();

        $variantComponents = VariantComponent::withTrashed()->where('updated_at', '>', $sinceTime)->get();

        // $bufferStocks = BufferStock::where('updated_at', '>', $sinceTime)->get();

        // Untuk units, kita pakai updated_at sebagai anchor.
        // Kalau mau strict pakai last_modified_at, bisa diubah di sini.
        $units = Unit::withTrashed()
            ->where('updated_at', '>', $sinceTime)
            ->get();

        return response()->json([
            'since'       => $sinceTime->toIso8601String(),
            'server_time' => $serverTime->toIso8601String(),

            'categories'        => $categories,
            'brands'            => $brands,
            'departments'       => $departments,
            'sections'          => $sections,
            'warehouses'        => $warehouses,
            'section_warehouses' => $section_warehouses,
            'racks'             => $racks,
            'products'          => $products,
            'company_items'     => $companyItems,
            'variants'          => $variants,
            'variant_photos'    => $variantPhotos,
            'components'        => $components,
            'variant_components' => $variantComponents,
            'units'             => $units,
        ]);
    }

    /**
     * PUSH: terima perubahan dari client dan upsert ke server.
     * POST /api/sync/push
     */
    public function push(Request $request): JsonResponse
    {
        $serverTime = Carbon::now();

        // Kamu bisa batasi cuma tabel tertentu yang boleh di-push dari mobile
        $payload = $request->all();

        $results = [];

        DB::beginTransaction();

        try {
            // Contoh: izinkan push untuk ini dulu:
            $results['company_items'] = $this->upsertBatch(
                CompanyItem::class,
                $payload['company_items'] ?? [],
                ['id', 'product_id', 'company_code', 'default_rack_id', 'specification',  'notes', 'created_at', 'updated_at', 'deleted_at'],
                true
            );

            $results['variants'] = $this->upsertBatch(
                Variant::class,
                $payload['variants'] ?? [],
                ['id', 'company_item_id', 'brand_id', 'name', 'uom', 'rack_id', 'specification', 'manuf_code', 'created_at', 'updated_at', 'deleted_at'],
                true
            );

            $results['variant_photos'] = $this->upsertBatch(
                VariantPhoto::class,
                $payload['variant_photos'] ?? [],
                ['id', 'variant_id', 'file_path', 'sort_order', 'is_primary', 'created_at', 'updated_at', 'deleted_at'],
                true
            );

            $results['components'] = $this->upsertBatch(
                Component::class,
                $payload['components'] ?? [],
                ['id', 'product_id', 'name', 'type', 'brand_id', 'manuf_code', 'specification', 'created_at', 'updated_at', 'deleted_at'],
                true
            );

            $results['component_photos'] = $this->upsertBatch(
                ComponentPhoto::class,
                $payload['component_photos'] ?? [],
                ['id', 'component_id', 'file_path', 'sort_order', 'is_primary', 'created_at', 'updated_at', 'deleted_at'],
                true
            );

            $results['variant_components'] = $this->upsertBatch(
                VariantComponent::class,
                $payload['variant_components'] ?? [],
                ['id', 'variant_id', 'component_id', 'created_at', 'updated_at', 'deleted_at'],
                false
            );

            $results['units'] = $this->upsertBatch(
                Unit::class,
                $payload['units'] ?? [],
                [
                    'id',
                    'variant_id',
                    'component_id',
                    'parent_unit_id',
                    'qr_value',
                    'status',
                    'rack_id',
                    'print_count',
                    'last_printed_at',
                    'last_printed_by',
                    'synced_at',
                    'created_at',
                    'created_by',
                    'updated_at',
                    'updated_by',
                    'deleted_at'
                ],
                true
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'error'       => 'SYNC_PUSH_FAILED',
                'message'     => $e->getMessage(),
                'server_time' => $serverTime->toIso8601String(),
            ], 500);
        }

        return response()->json([
            'server_time' => $serverTime->toIso8601String(),
            'results'     => $results,
        ]);
    }

    /**
     * Helper untuk upsert sekumpulan data per model.
     *
     * @param  class-string  $modelClass
     * @param  array         $items
     * @param  array         $fillableKeys
     * @param  bool          $supportsSoftDeletes
     * @return array{success_ids: array<int,string>, failed: array<int,array<string,mixed>>}
     */
    /**
     * Helper untuk upsert sekumpulan data per model.
     * (UPDATED: Menangani Duplicate Entry Error)
     */
    protected function upsertBatch(string $modelClass, array $items, array $fillableKeys, bool $supportsSoftDeletes = false): array
    {
        $successIds = [];
        $failed     = [];

        foreach ($items as $row) {
            // Wajib ada id (UUID) dari client
            if (!isset($row['id'])) {
                $failed[] = [
                    'id'    => null,
                    'error' => 'Missing id field',
                    'data'  => $row,
                ];
                continue;
            }

            $id = $row['id'];

            try {
                /** @var \Illuminate\Database\Eloquent\Model $query */
                $query = $modelClass::query();

                if ($supportsSoftDeletes && method_exists($modelClass, 'bootSoftDeletes')) {
                    $query = $query->withTrashed();
                }

                // FIX 1: Gunakan withoutGlobalScopes() untuk memastikan data benar-benar dicari
                // (kadang global scope menyembunyikan data sehingga find() return null)
                $model = $query->withoutGlobalScopes()->find($id);

                $payload = collect($row)->only($fillableKeys)->toArray();

                if ($model) {
                    // Update Existing
                    $model->fill($payload);
                    $model->save();
                } else {
                    // Create New
                    $model = new $modelClass();
                    $model->id = $id;
                    $model->fill($payload);

                    // FIX 2: Try-Catch khusus untuk menangani Duplicate Entry
                    try {
                        $model->save();
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Error Code 23000 biasanya adalah Integrity Constraint Violation (Duplicate Entry)
                        if ($e->getCode() === '23000') {
                            // Jika duplicate, berarti data sebenarnya ADA tapi tidak terdeteksi oleh find() awal.
                            // Kita coba load ulang dan paksa update.
                            $model = $query->withoutGlobalScopes()->find($id);
                            if ($model) {
                                $model->fill($payload);
                                $model->save();
                            } else {
                                // Kalau masih tidak ketemu tapi error duplicate, lempar error asli
                                throw $e;
                            }
                        } else {
                            throw $e;
                        }
                    }
                }

                // Soft delete logic (tetap sama)
                if ($supportsSoftDeletes && array_key_exists('deleted_at', $row)) {
                    if ($row['deleted_at']) {
                        if (is_null($model->deleted_at)) {
                            $model->delete();
                        }
                    } else {
                        if (method_exists($model, 'restore')) {
                            $model->restore();
                        }
                    }
                }

                $successIds[] = $id;
            } catch (\Throwable $e) {
                $failed[] = [
                    'id'    => $row['id'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success_ids' => $successIds,
            'failed'      => $failed,
        ];
    }
}
