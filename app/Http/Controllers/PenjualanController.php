<?php

namespace App\Http\Controllers;

use App\Models\BranchStock;
use App\Models\Penjualan as Sale;
use App\Models\PenjualanDetail as SaleDetail;
use App\Models\Produk as Product;
use App\Models\Setting;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use PDF;

//SaleController
class PenjualanController extends Controller
{
    public function index()
    {
        return view('penjualan.index');
    }

    public function data()
    {
        $penjualan = Sale::with('member')->orderBy('id_penjualan', 'desc')->get();

        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_item', function ($penjualan) {
                return format_uang($penjualan->total_item);
            })
            //total price
            ->addColumn('total_harga', function ($penjualan) {
                return '₱ ' . format_uang($penjualan->total_harga);
            })
            //pay
            ->addColumn('bayar', function ($penjualan) {
                return '₱ ' . format_uang($penjualan->bayar);
            })
            //date
            ->addColumn('tanggal', function ($penjualan) {
                return tanggal_indonesia($penjualan->created_at, false);
            })
            //member_code
            ->addColumn('kode_member', function ($penjualan) {
                $member = $penjualan->member->kode_member ?? '';
                return '<span class="label label-success">' . $member . '</spa>';
            })
            //discount
            ->editColumn('diskon', function ($penjualan) {
                return $penjualan->diskon . '%';
            })
            //cashier
            ->editColumn('kasir', function ($penjualan) {
                return $penjualan->user->name ?? '';
            })
            ->addColumn('aksi', function ($penjualan) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`' . route('penjualan.show', $penjualan->id_penjualan) . '`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`' . route('penjualan.destroy', $penjualan->id_penjualan) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_member'])
            ->make(true);
    }
    // visit "codeastro" for more projects!
    public function create()
    {
        //sale initial value open access form
        $penjualan = new Sale();
        $penjualan->id_member = null;
        $penjualan->total_item = 0;
        $penjualan->total_harga = 0;
        $penjualan->diskon = 0;
        $penjualan->bayar = 0;
        $penjualan->diterima = 0;
        $penjualan->id_user = auth()->id();
        $penjualan->save();

        session(['id_penjualan' => $penjualan->id_penjualan]);
        return redirect()->route('transaksi.index');
    }

    public function store(Request $request)
    {
        //has initial sale value open access form
        // @todo-jez savings sales here
        $penjualan = Sale::findOrFail($request->id_penjualan);
        $penjualan->id_member = $request->id_member;
        $penjualan->total_item = $request->total_item;
        $penjualan->total_harga = $request->total;
        $penjualan->diskon = $request->diskon;
        $penjualan->bayar = $request->bayar;
        $penjualan->diterima = $request->diterima;
        $penjualan->update();

        $detail = SaleDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            //discount
            $item->diskon = $request->diskon;
            $item->update();
            $produk = Product::find($item->id_produk);

            if(auth()->user()->level === 1){
                $produk->stok -= $item->jumlah;
    
                //reduce to warehouse also
                $warehouse = WarehouseStock::where('id_produk', $produk->id_produk)
                    ->first();

                $warehouse->stock -= $item->jumlah;
                $warehouse->update();
                $produk->update();

            }
            if(auth()->user()->level === 2) {
                $branchStock = BranchStock::where('id_produk', $produk->id_produk)
                    ->where('branch_id', auth()->user()->branch->id )
                    ->first();

                    $branchStock->stocks -= $item->jumlah; //amount
                    $branchStock->update();
            }
           

            //@todo-jez update branch stock
        }

        return redirect()->route('transaksi.selesai');
    }

    public function show($id)
    {
        $detail = SaleDetail::with('produk')->where('id_penjualan', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">' . $detail->produk->kode_produk . '</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                return $detail->produk->nama_produk;
            })
            ->addColumn('harga_jual', function ($detail) {
                return '₱ ' . format_uang($detail->harga_jual);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return '₱ ' . format_uang($detail->subtotal);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function destroy($id)
    {
        $penjualan = Sale::find($id);
        $detail    = SaleDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $produk = Product::find($item->id_produk);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->update();
            }

            $item->delete();
        }

        $penjualan->delete();

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();

        return view('penjualan.selesai', compact('setting'));
    }

    public function notaKecil()
    {
        $setting = Setting::first();
        $penjualan = Sale::find(session('id_penjualan'));
        if (!$penjualan) {
            abort(404);
        }
        $detail = SaleDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        return view('penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    }

    public function notaBesar()
    {
        $setting = Setting::first();
        $penjualan = Sale::find(session('id_penjualan'));
        if (!$penjualan) {
            abort(404);
        }
        $detail = SaleDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail'));
        $pdf->setPaper(0, 0, 609, 440, 'potrait');
        return $pdf->stream('Transaction-' . date('Y-m-d-his') . '.pdf');
    }
}
