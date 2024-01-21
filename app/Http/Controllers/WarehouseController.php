<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\WarehouseStock;
use App\Models\WarehouseStockActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{

    public function data()
    {
        $stocks = WarehouseStock::get();

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
                return format_uang($stock->stock);
            })
            ->addColumn('notes', function ($stock) {
                return $stock->notes;
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
        $products = Produk::all()->pluck('nama_produk', 'id_produk');

        return view('warehouse.index', compact('products'));
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
        // DB::transaction(function () use ($request) {
        $stock = WarehouseStock::where('id_produk', $request->id_produk)
            ->first();

        $product = Produk::where('id_produk', $request->id_produk)
            ->first();

        if (!$stock) {
            $stock =   WarehouseStock::create([
                'id_produk' => $request->id_produk,
                'stock' => $request->stock,
                'notes' => $request->notes,
            ]);
            $stockQuantity = $stock->stock;
        } else {
            $stockQuantity = 0;
        }

        WarehouseStockActivity::create([
            'added_by_user' => auth()->user()->id,
            'name' => 'Store',
            'description' => auth()->user()->name . ' Manage stock for product code: ' . $product->nama_produk . ': ' . $product->kode_produk,
            'stock_number_before' =>  $stockQuantity,
            'stock_number_after' => $request->stock,
            'warehousestock_id' => $stock->id
        ]);

        $stock->update($request->all());


        $product->update([
            'stok' => $request->stock
        ]);
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
