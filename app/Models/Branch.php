<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
    ];
    
    /**
     * Get the phone associated with the user.
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function sale(){
        return $this->hasMany(Penjualan::class,'id_penjualan','id');
    }
}
