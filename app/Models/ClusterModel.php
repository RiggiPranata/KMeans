<?php

namespace App\Models;

use CodeIgniter\Model;

class ClusterModel extends Model
{
    protected $table = 'clusters';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_barang', 'file_id', 'jumlah_transaksi', 'volume_penjualan', 'cluster'];
}
