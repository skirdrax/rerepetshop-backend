<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan'; // karena PK kamu pakai id_pelanggan
    public $incrementing = true;
    protected $keyType = 'int';

    protected $guarded = [];
    public function getKodePelangganAttribute(): string
{
    return 'PLG-' . str_pad((string) $this->id_pelanggan, 4, '0', STR_PAD_LEFT);
}
}