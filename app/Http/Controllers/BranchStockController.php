<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\BranchStockActivity;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\WarehouseStock;
use App\Models\WarehouseStockActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchStockController extends Controller
{
    //@todo-jez last stop here

    public function data()
    {
        $stocks = BranchStock::get();

        return datatables()
            ->of($stocks)
            ->addIndexColumn()
            ->addColumn('product_code', function ($stock) {
                return '<span class="label label-success">' .
                    Produk::where('id_produk', $stock->id_produk)->first()->kode_produk .
                    '</span>';
            })
            ->addColumn('product_name', function ($stock) {
                return Produk::where('id_produk', $stock->id_produk)->first()->nama_produk;
            })
            ->addColumn('stocks', function ($stock) {
                return format_uang($stock->stocks);
            })
            ->addColumn('branch_name', function ($stock) {
                return Branch::where('id', $stock->branch_id)->first()->name;
            })
            ->addColumn('created_at', function ($stock) {
                return $stock->created_at;
            })
            ->rawColumns(['product_code'])
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Produk::where('stok', '>', 0)
            ->get()
            ->pluck('nama_produk', 'id_produk');
        $branches = Branch::all()->pluck('name', 'id');

        return view('branchstock.index', compact('products', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $product = Produk::where('id_produk', (int) $request->id_produk)
                ->first();

            $warehouse = WarehouseStock::where('id_produk', $request->id_produk)
                ->first();
            // minus the stock to the product and warehouse stocks
            // $productStockDeduction =  (int) $product->stok - $request->stock;
            $stock = $product->stok - (int) $request->stock;

            // Run a raw SQL update query
            DB::table('warehouse_stocks')
                ->where('id_produk', $request->id_produk)
                ->update(['stock' => $stock]);

            $product->update([
                'stok' => $stock
            ]);

            if ($product->stok && $warehouse->stock < $request->stock) {
                throw new \Exception('Insufficient stock; the available stock is: ' . $product->stok);
            }

            BranchStock::create([
                'id_produk' => $request->id_produk,
                'branch_id' => $request->branch_id,
                'stocks' => $request->stock,
            ]);
        });

        //   BranchStockActivity::create([
        //     'added_by_user' => auth()->user()->id,
        //     'name' => 'Store',
        //     'description' => auth()->user()->name . 'Added stock ( '. $request->stock . ' pc/s) to branch: ' . $branch->anme ,
        //     'stock_number_before' => $branchStock->stock ? 0:,
        //     'stock_number_after' => $request->stock,
        //     'branch_stock_id' => $branchStock->id
        // ]);


        // dd($request->all());
        // DB::transaction(function () use ($request) {
        //   $branchStock  = BranchStock::where('id_produk' , $request->id_produk)
        //         ->where('branch_id', $request->branch_id)
        //         ->first();

        //         $branchStock = BranchStock::create([
        //             'id_produk' => $request->id_produk,
        //             'branch_id' => $request->branch_id,
        //             'stocks' => $request->stock,
        //         ]);


        //     if (!$branchStock) {
        //        $stock_number_before = $branchStock->stock;
        //     } else {
        //         $stock_number_before = 0;
        //     }


        //     $branch = Branch::where('id', $request->branch_id)
        //         ->first();

        //     BranchStockActivity::create([
        //         'added_by_user' => auth()->user()->id,
        //         'name' => 'Store',
        //         'description' => auth()->user()->name . 'Added stock ( '. $request->stock . ' pc/s) to branch: ' . $branch->anme ,
        //         'stock_number_before' =>  $stock_number_before,
        //         'stock_number_after' => $request->stock,
        //         'branch_stock_id' => $branchStock->id
        //     ]);


        // });

        return response()->json('Data saved successfully', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $produk = Produk::find($id);

        return response()->json($produk);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        $produk->update($request->all());

        return response()->json('Data saved successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);
        $produk->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $produk->delete();
        }

        return response(null, 204);
    }
}
