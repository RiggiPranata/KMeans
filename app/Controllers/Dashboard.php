<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    protected $uri;
    protected $urisegments;
    protected $builder;
    protected $kmeans;
    protected $barang;

    public function __construct()
    {
        $this->uri = service('uri');
        $this->urisegments = $this->uri->getTotalSegments();
        $db      = \Config\Database::connect();
        $this->builder = $db->table('clusters');
        $this->barang = $db->table('barang');
        $this->kmeans = new \App\Models\ClusterModel();
    }

    public function index()
    {
        $allFile = $this->builder->distinct(true)->select('file_id')->get()->getResultArray();
        // $cluster = $this->kmeans->graphCL($fileID);
        // dd($allfiles);
        $data = [
            'config' => config('Auth'),
            'title' => 'Dasboard',
            'segment' => $this->urisegments,
            'file' => $allFile,
            // 'jumlah_transaksi' => $jumlahTransaksi,
            // 'volume_penjualan' => $volumePenjualan,
            // 'cluster' => $cluster,
        ];
        return view('index', $data);
    }

    public function graphCluster()
    {
        $fileID = $this->request->getPost('fileType');

        // dd($jumlahTransaksi, $volumePenjualan, $cluster);
        $data = $this->kmeans->getDataByFileType($fileID);

        $query = $this->barang->select('nama')->join('clusters', 'clusters.kode_barang = barang.kode_barang', 'inner')->get();

        // Format data sesuai dengan format Bubble Chart
        $formattedData = [];
        foreach ($data as $row) {
            $formattedData[] = [
                'x' => $row['jumlah_transaksi'],
                'y' => $row['volume_penjualan'], // Nilai Y dapat diganti dengan data yang relevan
                'backgroundColor' => $this->getColorByCluster($row['cluster']),
                'dataLength' => $this->kmeans->countData($fileID),
                'kode_barang' => $this->kmeans->kode_barang($fileID),
                'nama_barang' => $query->getResultArray(),
                'cluster' => $this->kmeans->cluster($fileID),
                'c0' => $this->kmeans->countCluster($fileID, 0),
                'c1' => $this->kmeans->countCluster($fileID, 1),
                'c2' => $this->kmeans->countCluster($fileID, 2),
            ];
        }
        // d($formattedData['backgroundColor']);
        return $this->response->setJSON($formattedData);
    }


    private function getColorByCluster($cluster)
    {
        // Mengembalikan warna berdasarkan nilai cluster
        switch ($cluster) {
            case 0:
                return 'rgba(70, 164, 211, 5)';
            case 1:
                return 'rgba(10, 210, 129, 2)';
            case 2:
                return 'rgba(255, 69, 0, 1.0)';
            default:
                return 'rgba(70, 164, 211, 5)';
        }
    }
}
