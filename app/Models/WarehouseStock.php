<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'id_produk',
        'stock',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Produk::class,'id_produk','id');
    }

    public function warehouseActivity(){
        return $this->hasMany(WarehouseStockActivity::class,'id_produk','id');
    }
}
