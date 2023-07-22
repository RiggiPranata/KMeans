<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    protected $uri;
    protected $urisegments;
    protected $builder;
    protected $kmeans;
    protected $barang;
    protected $kl;
    protected $l;
    protected $pl;

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

        // $jtAvgc0 =  $this->kmeans->AVGCluster($fileID, 'jumlah_transaksi', 0);
        // $jtAvgc1 =  $this->kmeans->AVGCluster($fileID, 'jumlah_transaksi', 1);
        // $jtAvgc2 =  $this->kmeans->AVGCluster($fileID, 'jumlah_transaksi', 2);
        // $jtMaxc0 =  $this->kmeans->MAXCluster($fileID, 'jumlah_transaksi', 0);
        // $jtMaxc1 =  $this->kmeans->MAXCluster($fileID, 'jumlah_transaksi', 1);
        // $jtMaxc2 =  $this->kmeans->MAXCluster($fileID, 'jumlah_transaksi', 2);
        // $jtSumc0 =  $this->kmeans->SUMCluster($fileID, 'jumlah_transaksi', 0);
        // $jtSumc1 =  $this->kmeans->SUMCluster($fileID, 'jumlah_transaksi', 1);
        // $jtSumc2 =  $this->kmeans->SUMCluster($fileID, 'jumlah_transaksi', 2);
        // $jtMinc0 =  $this->kmeans->MINCluster($fileID, 'jumlah_transaksi', 0);
        // $jtMinc1 =  $this->kmeans->MINCluster($fileID, 'jumlah_transaksi', 1);
        // $jtMinc2 =  $this->kmeans->MINCluster($fileID, 'jumlah_transaksi', 2);
        // $vpAvgc0 =  $this->kmeans->AVGCluster($fileID, 'volume_penjualan', 0);
        // $vpAvgc1 =  $this->kmeans->AVGCluster($fileID, 'volume_penjualan', 1);
        // $vpAvgc2 =  $this->kmeans->AVGCluster($fileID, 'volume_penjualan', 2);
        // $vpMaxc0 =  $this->kmeans->MAXCluster($fileID, 'volume_penjualan', 0);
        // $vpMaxc1 =  $this->kmeans->MAXCluster($fileID, 'volume_penjualan', 1);
        // $vpMaxc2 =  $this->kmeans->MAXCluster($fileID, 'volume_penjualan', 2);
        // $vpSumc0 =  $this->kmeans->SUMCluster($fileID, 'volume_penjualan', 0);
        // $vpSumc1 =  $this->kmeans->SUMCluster($fileID, 'volume_penjualan', 1);
        // $vpSumc2 =  $this->kmeans->SUMCluster($fileID, 'volume_penjualan', 2);
        // $vpMinc0 =  $this->kmeans->MINCluster($fileID, 'volume_penjualan', 0);
        // $vpMinc1 =  $this->kmeans->MINCluster($fileID, 'volume_penjualan', 1);
        // $vpMinc2 =  $this->kmeans->MINCluster($fileID, 'volume_penjualan', 2);

        $jtavg0 = $this->kmeans->AVGCluster($fileID, 'jumlah_transaksi', 0);
        $jtavg1 = $this->kmeans->AVGCluster($fileID, 'jumlah_transaksi', 1);
        $jtavg2 = $this->kmeans->AVGCluster($fileID, 'jumlah_transaksi', 2);


        // Menggunakan pendekatan perbandingan bertingkat untuk menentukan kluster yang sesuai
        // if ($jtavg0 < $jtavg1 && $jtavg0 < $jtavg2) {
        //     $this->kl = 'Cluster 0';
        //     if ($jtavg1 < $jtavg2) {
        //         $this->l = 'Cluster 1';
        //         $this->pl = 'Cluster 2';
        //     } else {
        //         $this->l = 'Cluster 2';
        //         $this->pl = 'Cluster 1';
        //     }
        // } elseif ($jtavg1 < $jtavg2) {
        //     $this->kl = 'Cluster 1';
        //     if ($jtavg0 < $jtavg2) {
        //         $this->l = 'Cluster 0';
        //         $this->pl = 'Cluster 2';
        //     } else {
        //         $this->l = 'Cluster 2';
        //         $this->pl = 'Cluster 0';
        //     }
        // } else {
        //     $this->kl = 'Cluster 2';
        //     if ($jtavg0 < $jtavg1) {
        //         $this->l = 'Cluster 0';
        //         $this->pl = 'Cluster 1';
        //     } else {
        //         $this->l = 'Cluster 1';
        //         $this->pl = 'Cluster 0';
        //     }
        // }
        // dd($this->kl, $this->l, $this->pl);

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
