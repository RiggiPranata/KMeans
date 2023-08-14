<?php

namespace App\Controllers;

class FileMaster extends BaseController
{
    protected $uri;
    protected $urisegments;
    protected $builder;
    protected $kmeans;

    public function __construct()
    {
        $this->uri = service('uri');
        $this->urisegments = $this->uri->getTotalSegments();
        $db      = \Config\Database::connect();
        $this->builder = $db->table('clusters');
        $this->kmeans = new \App\Models\ClusterModel();
    }

    public function index($msg = null)
    {
        $allFile = $this->builder->distinct(true)->select('file_id, COUNT(kode_barang) as total')->groupBy('file_id')->get()->getResultArray();
        $data = [
            'config' => config('Auth'),
            'title' => 'Data File',
            'segment' => $this->urisegments,
            'file' => $allFile,
            'msg' => $msg
            // 'jumlah_transaksi' => $jumlahTransaksi,
            // 'volume_penjualan' => $volumePenjualan,
            // 'cluster' => $cluster,
        ];
        return view('file_master', $data);
    }

    public function getDataFile()
    {
        $fileID = $this->request->getVar('fileID');

        // Panggil model untuk mendapatkan data berdasarkan 'File_ID'
        $data = $this->kmeans->getDataByFileID($fileID);
        // Kirim data dalam format JSON

        if ($data) {
            echo json_encode($data);
        } else {
            // Jika data tidak ditemukan, kirimkan pesan dalam format JSON
            echo json_encode(array('message' => 'Data not found'));
        }
    }

    public function delete($fileID)
    {
        $query = $this->kmeans->where('file_id', $fileID)->delete();
        if ($query) {
            $msg = 'Data berhasil di hapus!';
        } else {
            $msg = 'Data gagal di hapus!';
        }
        return redirect()->to(site_url('/Master-data/' . $msg));
    }
}
