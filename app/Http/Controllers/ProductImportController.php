<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Models\ProductBrand;
use App\Models\ProductBranchStock;
use App\Models\ProductFee;
use App\Models\Supplier;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductImportController extends Controller
{
    public function showForm()
    {
        $branches = auth()->user()->isSuperAdmin() ? Branch::all() : auth()->user()->branches;
        return view('master.products.import', compact('branches'));
    }

    public function downloadTemplate()
    {
        $categoryList = ProductCategory::orderBy('nama')->pluck('nama')->implode('/');
        $kategoriHeader = 'Kategori' . ($categoryList ? " ({$categoryList})" : '');

        $headers = [
            'SKU', 'Nama Produk', 'Ukuran', 'Nama Model', $kategoriHeader, 'Sub Kategori', 'Brand', 'Vendor', 'Satuan', 'Lokasi Rak',
            'Harga Modal', 'Harga Jual', 'Harga Jual Bawa', 'Harga Online', 'Harga Ojol',
            'Harga Pasang (Fee Mekanik)',
            'Garansi Aktif (Ya/Tidak)', 'Durasi Garansi (hari)',
            'Stock Awal', 'Minimum Stock',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Produk');
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:T1')->getFont()->setBold(true);

        $sheet->fromArray([
            'BAN-001', 'Swallow Razor TL Ring 12', '100/90', 'Swallow Razor TL Ring 12', 'Ban', 'Ring 12', 'Swallow', 'PT Sumber Jaya', 'pcs', 'Rak Ban A',
            280000, 338000, 348000, 335000, 330000,
            5000,
            'Tidak', 0,
            10, 3,
        ], null, 'A2');

        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'Template Import Produk.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $path = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        array_shift($rows);

        $successCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;

            if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }

            try {
                DB::transaction(function () use ($row, $validated) {
                    [
                        $sku, $nama, $ukuran, $modelName, $kategoriNama, $subkategoriNama, $brandNama, $vendorNama, $satuan, $lokasiRak,
                        $hargaModal, $hargaJual, $hargaJualBawa, $hargaOnline, $hargaOjol,
                        $hargaPasang,
                        $garansiAktifRaw, $garansiDurasi,
                        $stockAwal, $minimumStock,
                    ] = array_pad($row, 20, null);

                    if (empty($nama)) {
                        throw new \Exception('Nama Produk kosong.');
                    }

                    $isFilled = fn($v) => $v !== null && $v !== '';

                    $category = null;
                    if ($isFilled($kategoriNama)) {
                        $category = ProductCategory::firstOrCreate(['nama' => trim($kategoriNama)]);
                    }

                    $subcategory = null;
                    if ($isFilled($subkategoriNama) && $category) {
                        $subcategory = ProductSubcategory::firstOrCreate([
                            'category_id' => $category->id,
                            'nama' => trim($subkategoriNama),
                        ]);
                    }

                    $brand = null;
                    if ($isFilled($brandNama)) {
                        $brand = ProductBrand::firstOrCreate(['nama' => trim($brandNama)]);
                        if ($category && !$brand->categories->contains('id', $category->id)) {
                            $brand->categories()->attach($category->id);
                        }
                    }

                    $vendor = null;
                    if ($isFilled($vendorNama)) {
                        $vendor = Supplier::firstOrCreate(['nama' => trim($vendorNama)]);
                    }

                    $updateData = [];
                    if ($isFilled($sku)) $updateData['sku'] = $sku;
                    if ($category) $updateData['category_id'] = $category->id;
                    if ($subcategory) $updateData['subcategory_id'] = $subcategory->id;
                    if ($brand) $updateData['brand_id'] = $brand->id;
                    if ($vendor) $updateData['default_supplier_id'] = $vendor->id;
                    if ($isFilled($satuan)) $updateData['satuan'] = $satuan;
                    if ($isFilled($lokasiRak)) $updateData['lokasi_rak'] = $lokasiRak;
                    if ($isFilled($ukuran)) $updateData['ukuran'] = $ukuran;
                    if ($isFilled($modelName)) $updateData['model_name'] = $modelName;
                    if ($isFilled($hargaModal)) $updateData['harga_modal'] = (float) $hargaModal;
                    if ($isFilled($hargaJual)) $updateData['harga_jual'] = (float) $hargaJual;
                    if ($isFilled($hargaJualBawa)) $updateData['harga_jual_jasa'] = (float) $hargaJualBawa;
                    if ($isFilled($hargaOnline)) $updateData['harga_online'] = (float) $hargaOnline;
                    if ($isFilled($hargaOjol)) $updateData['harga_ojol'] = (float) $hargaOjol;
                    if ($isFilled($minimumStock)) $updateData['minimum_stock'] = (int) $minimumStock;

                    if ($isFilled($garansiAktifRaw)) {
                        $garansiAktif = in_array(strtolower(trim((string) $garansiAktifRaw)), ['ya', 'yes', '1', 'true']);
                        $updateData['garansi_aktif'] = $garansiAktif;
                        $updateData['garansi_durasi_hari'] = $garansiAktif ? (int) ($garansiDurasi ?? 0) : null;
                    }

                    $isNewProduct = !Product::where('nama', trim($nama))->exists();

                    if ($isNewProduct) {
                        $updateData = array_merge([
                            'satuan' => 'pcs',
                            'harga_modal' => 0,
                            'harga_jual' => 0,
                            'harga_jual_jasa' => 0,
                            'harga_online' => 0,
                            'harga_ojol' => 0,
                            'minimum_stock' => 0,
                            'is_jasa' => false,
                            'status' => 'active',
                        ], $updateData);
                    }

                    $product = Product::updateOrCreate(['nama' => trim($nama)], $updateData);

                    if ($isFilled($hargaPasang) && (float) $hargaPasang > 0) {
                        ProductFee::updateOrCreate(
                            ['product_id' => $product->id],
                            ['fee_type' => 'fixed', 'fee_value' => (float) $hargaPasang]
                        );
                    }

                    if ($isFilled($stockAwal)) {
                        $existingStock = ProductBranchStock::where('product_id', $product->id)
                            ->where('branch_id', $validated['branch_id'])
                            ->first();

                        if ($existingStock) {
                            $existingStock->increment('stock_qty', (int) $stockAwal);
                        } else {
                            ProductBranchStock::create([
                                'product_id' => $product->id,
                                'branch_id' => $validated['branch_id'],
                                'stock_qty' => (int) $stockAwal,
                            ]);
                        }
                    }
                });

                $successCount++;
            } catch (\Throwable $e) {
                $errors[] = "Baris {$lineNumber}: " . $e->getMessage();
            }
        }

        return back()->with('importResult', [
            'success' => $successCount,
            'errors' => $errors,
        ]);
    }
}