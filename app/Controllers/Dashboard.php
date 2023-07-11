<?php

namespace App\Controllers;

class Dashboard extends BaseController
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

    public function index()
    {
        $fileID = '06-07-2023_11:44:04dataset_kcs.xlsx';
        $allFile = $this->builder->distinct(true)->select('file_id')->get()->getResultArray();
        $jumlahTransaksi = $this->kmeans->graphJT($fileID);
        $volumePenjualan = $this->kmeans->graphVP($fileID);
        $cluster = $this->kmeans->graphCL($fileID);
        // dd($allfiles);
        $data = [
            'config' => config('Auth'),
            'title' => 'Dasboard',
            'segment' => $this->urisegments,
            'file' => $allFile,
            'jumlah_transaksi' => $jumlahTransaksi,
            'volume_penjualan' => $volumePenjualan,
            'cluster' => $cluster,
        ];
        return view('index', $data);
    }

    public function graphCluster($fileID)
    {
        // $fileID = '06-07-2023_11:44:04dataset_kcs.xlsx';
        $allFile = $this->builder->distinct(true)->select('file_id')->get()->getResultArray();
        $jumlahTransaksi = $this->kmeans->graphJT($fileID);
        $volumePenjualan = $this->kmeans->graphVP($fileID);
        $cluster = $this->kmeans->graphCL($fileID);

        // dd($jumlahTransaksi, $volumePenjualan, $cluster);
        $data = [
            'file' => $allFile,
            'title' => 'Dasboard',
            'segment' => $this->urisegments,
        ];
        return $data;
    }
}
