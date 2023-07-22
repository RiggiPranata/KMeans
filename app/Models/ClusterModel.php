<?php

namespace App\Models;

use CodeIgniter\Model;

class ClusterModel extends Model
{
    protected $table = 'clusters';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_barang', 'file_id', 'jumlah_transaksi', 'volume_penjualan', 'cluster'];


    public function countData($fileID)
    {
        $data = $this->select('COUNT(kode_barang) as length')->where('file_id', $fileID)->findAll();

        return $data;
    }
    public function kode_barang($fileID)
    {
        $data = $this->select('kode_barang')->where('file_id', $fileID)->findAll();

        return $data;
    }
    public function cluster($fileID)
    {
        $data = $this->select('cluster')->where('file_id', $fileID)->findAll();

        return $data;
    }

    public function countCluster($fileID, $c)
    {
        $data = $this->select('COUNT(cluster) as cluster')->where('file_id', $fileID)->where('cluster', $c)->findAll();

        return $data;
    }

    public function AVGCluster($fileID, $kolom, $cluster)
    {
        $data = $this->selectAvg($kolom, $kolom . 'AVG' . $cluster)->where('file_id', $fileID)->where('cluster', $cluster)->findAll();
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

    public function getAllIdByfile($fileID)
    {
        $query = $this->select('id')
            ->where('file_id', $fileID)
            ->findAll();

        return $query;
    }

    public function getDataByFileID($fileID)
    {
        // Gantikan query berikut dengan query yang sesuai untuk mendapatkan data berdasarkan 'File_ID' dari tabel cluster
        $query = $this->select("kode_barang,jumlah_transaksi,volume_penjualan")->where('file_id', $fileID)->findAll();
        return $query;
    }
}
