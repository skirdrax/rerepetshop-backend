<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table = 'keranjang';
    protected $primaryKey = 'id_keranjang';

    protected $fillable = [
        'id_pelanggan',
        'total',
    ];

    public function items()
    {
        return $this->hasMany(KeranjangItem::class, 'id_keranjang', 'id_keranjang');
    }
}