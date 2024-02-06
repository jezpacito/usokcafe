<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;

class RestockController extends Controller
{
    public function downloadTemplate()
    {
        $products = Produk::get();

        $writer = SimpleExcelWriter::streamDownload('restock-template.csv');

        foreach ($products as $product) {
            $writer->addRow([
                'product_code' => $product->kode_produk,
                'product_name' => $product->nama_produk,
                'brand' => $product->merk,
                'stock_number' => 0,
            ]);
        }
        $writer->toBrowser();
    }

    public function uploadRestore(Request $request)
    {
        //@todo success message
        if($request->hasFile('restock_csv')){
            $file = $request->file('restock_csv');
            $name = 'restock-template.'. $file->getClientOriginalExtension();

            $file->move(public_path('/'), $name);
            $filePath = "/$name";
                   SimpleExcelReader::create(public_path($filePath))->getRows()
                   ->each(function (array $rowProperties) {
                    DB::transaction(function () use ($rowProperties) {
                        $product = Produk::where('kode_produk', $rowProperties['product_code'])->first();
        
                        if ($product) {
                            // Product exists, update its stock
                            $newStock = (int)$product->stok + (int)$rowProperties['stock_number'];
                            $product->stok = $newStock;
                            $product->update();
        
                            // Check if WarehouseStock exists
                            $warehouseStock = WarehouseStock::where('id_produk', $product->id_produk)->first();
        
                            if ($warehouseStock) {
                                // WarehouseStock exists, update its stock
                                $warehouseStock->stock = $newStock;
                                $warehouseStock->update();
                            } else {
                                // WarehouseStock does not exist, create a new one
                                WarehouseStock::create([
                                    'id_produk' => $product->id_produk,
                                    'stock' => $newStock,
                                    // Add any other necessary fields
                                ]);
                            }
                        } else {
                            return redirect()->back()->with('error', 'An error occurred!');
                        }
                    });
                   });
                   return redirect()->back()->with('success', 'Restock successful!');

        }
    }
}
