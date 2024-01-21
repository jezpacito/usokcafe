<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchStockActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'added_by_user',
        'name',
        'description',
        'stock_number_before',
        'stock_number_after',
        'branch_stock_id'
    ];

    public function user(){
        return $this->belongsTo(User::class,'added_by_user','id');
    }

    public function branchstock() {
        $this->belongsTo(BranchStock::class,'branch_stock_id', 'id_produk');
    }
}
