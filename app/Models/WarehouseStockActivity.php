<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStockActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'added_by_user',
        'name',
        'description',
        'stock_number_before',
        'stock_number_after',
        'warehousestock_id'
    ];

    public function user(){
        return $this->belongsTo(User::class,'added_by_user','id');
    }

    public function warehouseStock() {
        $this->belongsTo(WarehouseStock::class,'warehousestock_id', 'id_produk');
    }
}
