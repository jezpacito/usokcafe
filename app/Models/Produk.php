<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//Product
class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $guarded = [];

    public function warehouseStock()
    {
        return $this->hasOne(WarehouseStock::class);
    }

    public function branchStock()
    {
        return $this->hasMany(BranchStock::class,'id_produk','id');
    }

}
