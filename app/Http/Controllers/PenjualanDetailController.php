<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Penjualan;
use App\Models\PenjualanDetail as SalesDetail;
use App\Models\Produk;
use App\Models\Setting;
use Illuminate\Http\Request;

class PenjualanDetailController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')
        ->where('stok' , '>=' , 1)
        ->get();
        $member = Member::orderBy('nama')->get();
        $diskon = Setting::first()->diskon ?? 0;

        // Check whether there are any transactions in progress
        if ($id_penjualan = session('id_penjualan')) {
            $penjualan = Penjualan::find($id_penjualan);
            $memberSelected = $penjualan->member ?? new Member();

            return view('penjualan_detail.index', compact('produk', 'member', 'diskon', 'id_penjualan', 'penjualan', 'memberSelected'));
        } else {
            if (auth()->user()->level == 1) {
                return redirect()->route('transaksi.baru');
            } else {
                return redirect()->route('home');
            }
        }
    }

    public function data($id)
    {
    
        $detail = SalesDetail::with('produk')
            ->where('id_penjualan', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['kode_produk'] = '<span class="label label-success">'. $item->produk['kode_produk'] .'</span';
            $row['nama_produk'] = $item->produk['nama_produk'];
            $row['harga_jual']  = '₱ '. format_uang($item->harga_jual);
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->id_penjualan_detail .'" value="'. $item->jumlah .'">';
            $row['diskon']      = $item->diskon . '%';
            $row['subtotal']    = '₱ '. format_uang($item->subtotal);
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('transaksi.destroy', $item->id_penjualan_detail) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            $total += $item->harga_jual * $item->jumlah - (($item->diskon * $item->jumlah) / 100 * $item->harga_jual);;
            $total_item += $item->jumlah;
        }
        $data[] = [
            'kode_produk' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'nama_produk' => '',
            'harga_jual'  => '',
            'jumlah'      => '',
            'diskon'      => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'kode_produk', 'jumlah'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $produk = Produk::where('id_produk', $request->id_produk)->first();
        if (! $produk) {
            return response()->json('Data failed to save', 400);
        }

        // @todo-jez
        if(auth()->user()->level === 1) {
            $priceAmount =  $produk->wholesale_price;
        } else {
            $priceAmount = $produk->harga_jual;
        }

        $detail = new SalesDetail();
        $detail->id_penjualan = $request->id_penjualan;
        $detail->id_produk = $produk->id_produk;
        //user is admin (level === 1)
        $detail->harga_jual = $priceAmount;
        $detail->jumlah = 1;
        $detail->diskon = $produk->diskon; //discount is in price
        $detail->subtotal = $priceAmount - ($produk->diskon / 100 * $priceAmount);
        
        $detail->save();

        return response()->json('Data saved successfully', 200);
    }
    
    public function update(Request $request, $id)
    {
        try {
            $detail = SalesDetail::find($id);
            $product =  Produk::where('id_produk', $detail->id_produk)->first();
            $detail->jumlah = (int) $request->jumlah;
            $detail->subtotal = (int) $detail->harga_jual * (int) $request->jumlah - (($detail->diskon * (int) $request->jumlah) / 100 * (int) $detail->harga_jual);
         
            // Simulate an error condition for demonstration purposes
            if($product->stok < $request->jumlah) {
                if($product->stok <= 0) {
                    throw new \Exception("Error: No available stock for item ". $product->nama_produk);
                }
                    throw new \Exception("Error: The available stock for item ". $product->nama_produk. " is only ". $product->stok ." pc/s");
            } else {
                $detail->update();
            }
    
            return response()->json(['success' => true]);
    
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $detail = SalesDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($diskon = 0, $total = 0, $diterima = 0)
    {
        //discount = total - (discount/100 * total)
        $bayar   = $total - ($diskon / 100 * $total);
        //return = (accepted != 0) ?  accepted - pay : 0
        $kembali = ($diterima != 0) ? $diterima - $bayar : 0;
        $data    = [
            'totalrp' => format_uang($total),
            'bayar' => $bayar,
            'bayarrp' => format_uang($bayar),
            'terbilang' => ucwords(terbilang($bayar). ' PHP'),
            'kembalirp' => format_uang($kembali),
            'kembali_terbilang' => ucwords(terbilang($kembali). ' PHP'),
        ];

        return response()->json($data);
    }
}
// visit "codeastro" for more projects!