<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'kode_barang';
    protected $created_at = true;
    protected $updated_at = true;
    protected $useTimestamps = true;
    protected $allowedFields = ['kode_barang', 'nama', 'created_at', 'updated_at'];

    // Fungsi untuk mengecek duplikasi data berdasarkan kode_barang
    public function isDuplicate($kode_barang)
    {
        return $this->where('kode_barang', $kode_barang)->countAllResults() > 0;
    }

    // Fungsi untuk mendapatkan data berdasarkan kode_barang
    public function getDataByKodeBarang($kode_barang)
    {
        return $this->where('kode_barang', $kode_barang)->first();
    }

    // Fungsi untuk mendapatkan data yang akan dihapus berdasarkan kriteria tertentu
    // Misalnya, data dengan waktu update paling lama
    public function getDataToDelete($kode_barang)
    {
        return $this->where('kode_barang', $kode_barang)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    // Fungsi untuk menghapus data berdasarkan ID
    public function deleteData($id)
    {
        return $this->delete($id);
    }
}
