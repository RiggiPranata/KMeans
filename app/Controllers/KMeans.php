<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class KMeans extends BaseController
{
    protected $uri;
    protected $urisegments;
    protected $clusters;
    protected $clusterModel;


    public function __construct()
    {
        $this->uri = service('uri');
        $this->urisegments = $this->uri->getTotalSegments();
        $this->clusterModel = new \App\Models\ClusterModel();
        date_default_timezone_set('Asia/Jakarta');
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
            $this->clusters = $this->kMeansClustering($uploadedFile);

            // dd($this->clusters);
            // Simpan hasil perhitungan ke database
            $this->saveClustersToDatabase($this->clusters);
            $fileID = $this->clusterModel->orderBy('file_id', 'DESC')->limit(1)->findColumn('file_id');

            // dd($fileID);
            //query cluster
            $c2 = $this->clusterModel->where('cluster', 2)->where('file_id', $fileID)->findColumn('kode_barang');
            $c1 = $this->clusterModel->where('cluster', 1)->where('file_id', $fileID)->findColumn('kode_barang');
            $c0 = $this->clusterModel->where('cluster', 0)->where('file_id', $fileID)->findColumn('kode_barang');
            // dd($c2);
            // Tampilkan pesan sukses
            $data = [
                'title' => "K-Means",
                'message' => "File Excel berhasil diunggah dan diproses. Nama file: $fileName, Ukuran file: $fileSize bytes, dan waktu unggahan " . date('d-m-Y_h:i:s'),
                'segment' => $this->urisegments,
                'clusters' => $this->clusters,
                'c0' => $c0,
                'c1' => $c1,
                'c2' => $c2,
                'noRe' => "<script>
                window.onbeforeunload = function() {
                    return 'Apakah Anda yakin ingin meninggalkan halaman ini?';
                };
            </script>"
            ];
            echo view('kmeans_result', $data);
        } else {
            // Tampilkan pesan error
            $data = [
                'title' => "K-Means",
                'message' => "Terjadi kesalahan saat mengunggah file. Pastikan file yang diunggah adalah file Excel (format .xlsx)",
                'segment' => $this->urisegments,
                'clusters' => $this->clusters,
                'noRe' => "<script>
                window.onbeforeunload = function() {
                    return 'Apakah Anda yakin ingin meninggalkan halaman ini?';
                };
            </script>"
            ];
            echo view('kmeans_result', $data);
        }
    }

    private function kMeansClustering($uploadedFile)
    {
        // Load file Excel menggunakan library PhpSpreadsheet
        $spreadsheet = IOFactory::load($uploadedFile->getTempName());

        // Ambil data dari sheet pertama (asumsi data berada pada sheet pertama)
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->removeRow(1);
        $data = $sheet->toArray();

        // dd($data);
        // Inisialisasi jumlah cluster
        $numClusters = 3;

        // Inisialisasi centroid awal secara acak
        $centroids = $this->initializeCentroids($data, $numClusters);

        // Inisialisasi variabel iterasi dan batas iterasi
        $iteration = 0;
        $maxIterations = 10;
        // dd($data, $centroids);
        // Mulai iterasi perhitungan k-means clustering
        while ($iteration < $maxIterations) {
            $clusters = $this->assignDataToClusters($data, $centroids);
            $newCentroids = $this->calculateNewCentroids($clusters, $data);

            // Jika centroid tidak berubah, hentikan iterasi
            if ($centroids == $newCentroids) {
                break;
            }

            $centroids = $newCentroids;
            $iteration++;
        }
        // dd($clusters);
        // Masukkan informasi cluster ke dalam array hasil
        $result = [];
        // dd($clusters, $clusters[1]);
        for ($i = 0; $i < count($data); $i++) {
            $row = $data[$i];
            $result[] = [
                'file_id' => date('d-m-Y_h:i:s') . $uploadedFile->getName(),
                'kode_barang' => $row[0],
                'jumlah_transaksi' => $row[1],
                'volume_penjualan' => $row[2],
                'cluster' =>  $clusters[$i]
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

        // Mengabaikan baris pertama yang berisi nama tabel/kolom
        $startIndex = 0;

        // Melakukan perhitungan jarak dan mengelompokkan data ke dalam cluster terdekat
        for ($i = $startIndex; $i < count($data); $i++) {
            $minDistance = INF;
            $closestCentroid = -1;

            for ($j = 0; $j < count($centroids); $j++) {
                $distance = sqrt(pow(($data[$i][1] - $centroids[$j][0]), 2) + pow(($data[$i][2] - $centroids[$j][1]), 2));

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $closestCentroid = $j;
                }
            }

            $clusters[$i] = $closestCentroid;
        }

        return $clusters;
    }

    private function calculateNewCentroids($clusters, $data)
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

        foreach ($clusters as $cluster) {
            // Simpan data cluster ke dalam tabel cluster_result
            $this->clusterModel->insert($cluster);
        }
    }
}
