<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class KMeans extends BaseController
{
    protected $uri;
    protected $urisegments;


    public function __construct()
    {
        $this->uri = service('uri');
        $this->urisegments = $this->uri->getTotalSegments();
    }

    public function index()
    {
        $data = [
            'title' => 'K-Means',
            'segment' => $this->urisegments
        ];
        return view('kmeans', $data);
    }

    public function processUpload()
    {
        $uploadedFile = $this->request->getFile('excelFile');

        // Cek apakah file berhasil diunggah
        if ($uploadedFile->isValid() && $uploadedFile->getExtension() === 'xlsx') {
            // Lakukan pemrosesan file Excel di sini
            // Misalnya, simpan file ke direktori tertentu dan proses isinya

            // Ambil informasi file
            $fileName = $uploadedFile->getName();
            $fileSize = $uploadedFile->getSize();

            // Lakukan operasi lain sesuai kebutuhan Anda
            // ...

            // Lakukan perhitungan k-means clustering
            $clusters = $this->kMeansClustering($uploadedFile);

            // Simpan hasil perhitungan ke database
            $this->saveClustersToDatabase($clusters);

            // Tampilkan pesan sukses
            $data['message'] = "File Excel berhasil diunggah dan diproses. Nama file: $fileName, Ukuran file: $fileSize bytes";
            return view('upload_result', $data);
        } else {
            // Tampilkan pesan error
            $data['error'] = "Terjadi kesalahan saat mengunggah file. Pastikan file yang diunggah adalah file Excel (format .xlsx)";
            return view('upload_result', $data);
        }
    }

    private function kMeansClustering($uploadedFile)
    {
        // Load file Excel menggunakan library PhpSpreadsheet
        $spreadsheet = IOFactory::load($uploadedFile->getTempName());

        // Ambil data dari sheet pertama (asumsi data berada pada sheet pertama)
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Inisialisasi jumlah cluster
        $numClusters = 3;

        // Inisialisasi centroid awal secara acak
        $centroids = $this->initializeCentroids($data, $numClusters);

        // Inisialisasi variabel iterasi dan batas iterasi
        $iteration = 0;
        $maxIterations = 10;

        // Mulai iterasi perhitungan k-means clustering
        while ($iteration < $maxIterations) {
            $clusters = $this->assignDataToClusters($data, $centroids);
            $newCentroids = $this->calculateNewCentroids($clusters);

            // Jika centroid tidak berubah, hentikan iterasi
            if ($centroids == $newCentroids) {
                break;
            }

            $centroids = $newCentroids;
            $iteration++;
        }

        // Masukkan informasi cluster ke dalam array hasil
        $result = [];
        foreach ($data as $key => $row) {
            $cluster = $clusters[$key];
            $result[] = [
                'kode_barang' => $row[0],
                'jumlah_transaksi' => $row[1],
                'volume_penjualan' => $row[2],
                'cluster' => 'Cluster ' . $cluster
            ];
        }

        return $result;
    }

    private function initializeCentroids($data, $numClusters)
    {
        $centroids = [];

        // Mengacak data untuk memilih centroid awal secara acak
        shuffle($data);

        // Mengambil $numClusters data pertama sebagai centroid awal
        for ($i = 0; $i < $numClusters; $i++) {
            $centroids[] = [$data[$i][1], $data[$i][2]];
        }

        return $centroids;
    }

    private function assignDataToClusters($data, $centroids)
    {
        $clusters = [];

        foreach ($data as $row) {
            $minDistance = INF;
            $clusterIndex = null;

            // Menghitung jarak Euclidean antara data dengan setiap centroid
            foreach ($centroids as $index => $centroid) {
                $distance = sqrt(pow($row[1] - $centroid[0], 2) + pow($row[2] - $centroid[1], 2));

                // Memilih centroid dengan jarak terdekat
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $clusterIndex = $index;
                }
            }

            $clusters[] = $clusterIndex;
        }

        return $clusters;
    }

    private function calculateNewCentroids($clusters)
    {
        $newCentroids = [];

        // Menghitung rata-rata data pada setiap cluster
        $clusterData = [];
        foreach ($clusters as $index => $cluster) {
            $clusterData[$cluster][] = [
                'jumlah_transaksi' => $data[$index][1],
                'volume_penjualan' => $data[$index][2]
            ];
        }

        foreach ($clusterData as $data) {
            $numData = count($data);
            $sumTransaksi = 0;
            $sumVolume = 0;

            // Menghitung jumlah total transaksi dan volume penjualan pada cluster
            foreach ($data as $row) {
                $sumTransaksi += $row['jumlah_transaksi'];
                $sumVolume += $row['volume_penjualan'];
            }

            // Menghitung rata-rata transaksi dan volume penjualan pada cluster
            $meanTransaksi = $sumTransaksi / $numData;
            $meanVolume = $sumVolume / $numData;

            $newCentroids[] = [$meanTransaksi, $meanVolume];
        }

        return $newCentroids;
    }

    private function saveClustersToDatabase($clusters)
    {
        // Simpan hasil perhitungan ke dalam database
        // Sesuaikan dengan struktur database Anda

        // Menggunakan model ClusterModel sebagai contoh
        $clusterModel = new \App\Models\ClusterModel();

        foreach ($clusters as $cluster) {
            // Simpan data cluster ke dalam tabel cluster_result
            $clusterModel->insert($cluster);
        }
    }
}
