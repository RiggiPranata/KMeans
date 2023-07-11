<?php

namespace App\Models;

use CodeIgniter\Model;

class ClusterModel extends Model
{
    protected $table = 'clusters';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_barang', 'file_id', 'jumlah_transaksi', 'volume_penjualan', 'cluster'];


    public function graphJT($fileID)
    {
        $data = $this->select('DISTINCT(jumlah_transaksi)')->where('file_id', $fileID)->orderBy('jumlah_transaksi', "ASC")->findAll();

        return $data;
    }
    public function graphVP($fileID)
    {
        $data = $this->select('volume_penjualan')->where('file_id', $fileID)->findAll();

        return $data;
    }
    public function graphCL($fileID)
    {
        $data = $this->select('cluster')->where('file_id', $fileID)->findAll();

        return $data;
    }

    public function getDataByFileType($fileType)
    {
        // Query data dari database berdasarkan jenis file
        $query = $this->select('jumlah_transaksi, volume_penjualan, cluster')
            ->where('file_id', $fileType)
            ->findAll();

        return $query;
    }
}
