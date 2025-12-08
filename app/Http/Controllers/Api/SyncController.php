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

        $variantComponents = VariantComponent::where('updated_at', '>', $sinceTime)->get();

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
            // 'buffer_stocks'     => $bufferStocks,
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
                ['product_id', 'company_code', 'is_set', 'has_components', 'initialized_at', 'initialized_by', 'notes'],
                true
            );

            $results['variants'] = $this->upsertBatch(
                Variant::class,
                $payload['variants'] ?? [],
                ['company_item_id', 'brand_id', 'name', 'default_location', 'spec_json', 'initialized_at', 'initialized_by', 'is_active'],
                true
            );

            $results['variant_photos'] = $this->upsertBatch(
                VariantPhoto::class,
                $payload['variant_photos'] ?? [],
                ['variant_id', 'file_path', 'sort_order', 'is_primary'],
                true
            );

            $results['components'] = $this->upsertBatch(
                Component::class,
                $payload['components'] ?? [],
                ['product_id', 'name', 'brand_id', 'manuf_code', 'spec_json', 'is_active'],
                true
            );

            $results['component_photos'] = $this->upsertBatch(
                ComponentPhoto::class,
                $payload['component_photos'] ?? [],
                ['component_id', 'file_path', 'sort_order', 'is_primary'],
                true
            );

            $results['variant_components'] = $this->upsertBatch(
                VariantComponent::class,
                $payload['variant_components'] ?? [],
                ['variant_id', 'component_id', 'quantity'],
                false
            );

            // $results['buffer_stocks'] = $this->upsertBatch(
            //     BufferStock::class,
            //     $payload['buffer_stocks'] ?? [],
            //     ['company_item_id', 'brand_id', 'location', 'min_quantity'],
            //     false
            // );

            $results['units'] = $this->upsertBatch(
                Unit::class,
                $payload['units'] ?? [],
                [
                    'variant_id',
                    'component_id',
                    'parent_unit_id',
                    'qr_value',
                    'status',
                    'location',
                    'print_count',
                    'last_printed_at',
                    'synced_at',
                    'last_modified_at',
                    'created_by',
                    'updated_by',
                    'last_printed_by',
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

                /** @var \Illuminate\Database\Eloquent\Model|null $model */
                $model = $query->find($id);

                $payload = collect($row)->only($fillableKeys)->toArray();

                // Cast spec_json, dsb. kalau perlu – tp karena model sudah punya casts,
                // cukup isi raw array saja.

                if ($model) {
                    // Conflict resolution simple: kalau client kirim updated_at/last_modified_at,
                    // kamu bisa cek di sini. Untuk sekarang kita pakai last-write-wins dari client.
                    $model->fill($payload);
                } else {
                    $model = new $modelClass();
                    $model->id = $id;
                    $model->fill($payload);
                }

                // Atur updated_at secara otomatis
                // Kalau client bawa last_modified_at / updated_at bisa dipertimbangkan,
                // tapi untuk simple version kita percaya timestamp server.
                $model->save();

                // Soft delete kalau client kirim deleted_at != null
                if ($supportsSoftDeletes && array_key_exists('deleted_at', $row)) {
                    if ($row['deleted_at']) {
                        // kalau belum dihapus
                        if (is_null($model->deleted_at)) {
                            $model->delete();
                        }
                    } else {
                        // kalau ada deleted_at null, bisa di-restore
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
