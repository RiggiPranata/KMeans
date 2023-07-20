<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Constraint\IsEqual;

class KMeans extends BaseController
{
    protected $uri;
    protected $urisegments;
    protected $builder;
    protected $clusters;
    protected $clusterModel;
    protected $barang;

    public function __construct()
    {
        $db      = \Config\Database::connect();
        $this->builder = $db->table('clusters');
        $this->barang = $db->table('barang');
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



            //query count kode_barang in cluster
            $c2c = $this->builder->selectCount('kode_barang')->where('file_id', $fileID)->where('cluster', 2)->get()->getResultArray();
            $c1c = $this->builder->selectCount('kode_barang')->where('file_id', $fileID)->where('cluster', 1)->get()->getResultArray();
            $c0c = $this->builder->selectCount('kode_barang')->where('file_id', $fileID)->where('cluster', 0)->get()->getResultArray();

            // dd($fileID);
            //query cluster kode_barang
            $c2 = $this->clusterModel->where('cluster', 2)->where('file_id', $fileID)->findColumn('kode_barang');
            $c1 = $this->clusterModel->where('cluster', 1)->where('file_id', $fileID)->findColumn('kode_barang');
            $c0 = $this->clusterModel->where('cluster', 0)->where('file_id', $fileID)->findColumn('kode_barang');
            if ($c0 == null) {
                $c0 = 'Tidak ada data yang memenuhi';
            }
            if ($c1 == null) {
                $c1 = 'Tidak ada data yang memenuhi';
            }
            if ($c2 == null) {
                $c2 = 'Tidak ada data yang memenuhi';
            }
            // dd($c0c, $c1c, $c2c);

            // Tampilkan pesan sukses
            $data = [
                'title' => "K-Means",
                'message' => "File Excel berhasil diunggah dan diproses. Nama file: $fileName, Ukuran file: $fileSize bytes, dan waktu unggahan " . date('d-m-Y_h:i:s'),
                'segment' => $this->urisegments,
                'clusters' => $this->clusters,
                'c0' => $c0,
                'c1' => $c1,
                'c2' => $c2,
                'c0c' => $c0c,
                'c1c' => $c1c,
                'c2c' => $c2c,
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
        $exl = $sheet->toArray();

        // Filter data hanya untuk kolom 'jumlah_transaksi' dan 'volume_penjualan'
        $data = array_map(function ($row) {
            return [
                $row[2],
                $row[3]
            ];
        }, $exl);

        $kodeBarang = array_map(function ($row) {
            return [
                $row[0]
            ];
        }, $exl);

        $tbl_barang = array_map(function ($row) {
            return [
                $row[0],
                $row[1]
            ];
        }, $exl);

        $barang = [];
        foreach ($tbl_barang as $tb) {
            $barang = [
                "kode_barang" => $tb[0],
                "nama" => $tb[1]
            ];
            // dd($barang);
            $this->barang->insert($barang);
        }

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
            if ($this->isEqual($centroids, $newCentroids)) {

                break;
            }

            $centroids = $newCentroids;
            $iteration++;
        }

        // Masukkan informasi cluster ke dalam array hasil
        $result = [];
        // dd($clusters, $clusters[1]);
        for ($i = 0; $i < count($data); $i++) {
            $kb = $kodeBarang[$i];
            $row = $data[$i];
            $result[] = [
                'file_id' => date('d-m-Y_h:i:s') . $uploadedFile->getName(),
                'kode_barang' => $kb[0],
                'jumlah_transaksi' => $row[0],
                'volume_penjualan' => $row[1],
                'cluster' =>  $clusters[$i]
            ];
        }

        return $result;
    }

    private function initializeCentroids($data, $numClusters)
    {
        // $centroids = [];

        // // Mengacak data untuk memilih centroid awal secara acak
        // shuffle($data);

        // // Mengambil $numClusters data pertama sebagai centroid awal
        // for ($i = 0; $i < $numClusters; $i++) {

        //     $centroids[] = [$data[$i][0], $data[$i][1]];
        // }

        // return $centroids;

        // $centroids = [];

        // // Mengacak data untuk memilih centroid awal secara acak
        // shuffle($data);

        // // Mengambil $numClusters data pertama sebagai centroid awal
        // $selectedIndices = array_rand($data, $numClusters);
        // $selectedData = array_intersect_key($data, array_flip($selectedIndices));

        // // Memastikan tidak ada nilai centroid yang sama
        // $uniqueData = array_values(array_unique($selectedData, SORT_REGULAR));


        // foreach ($uniqueData as $row) {
        //     $centroids[] = [$row[0], $row[1]];
        // }
        // dd($centroids);
        // return $centroids;

        $centroids = [];

        // Mendapatkan data unik untuk kolom 'jumlah_transaksi'
        $uniqueJumlahTransaksi = array_unique(array_column($data, 0));

        // Mengambil 3 nilai unik terbesar dari 'jumlah_transaksi'
        sort($uniqueJumlahTransaksi);
        shuffle($uniqueJumlahTransaksi);
        $selectedJumlahTransaksi = array_slice($uniqueJumlahTransaksi, 4, $numClusters);

        // Mengambil data dengan nilai 'jumlah_transaksi' yang sesuai
        foreach ($data as $row) {
            if (in_array($row[0], $selectedJumlahTransaksi)) {
                $centroids[] = [$row[0], $row[1]];
            }
            $jml = count($centroids);
            if ($jml == 3) {
                break;
            }
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
                $distance = sqrt(pow(($data[$i][0] - $centroids[$j][0]), 2) + pow(($data[$i][1] - $centroids[$j][1]), 2));

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
                'jumlah_transaksi' => $data[$index][0],
                'volume_penjualan' => $data[$index][1]
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

        foreach ($clusters as $cluster) {
            // Simpan data cluster ke dalam tabel cluster_result
            $this->clusterModel->insert($cluster);
        }
    }


    private function isEqual($centroids1, $centroids2)
    {
        // Memeriksa apakah kedua centroid identik
        for ($i = 0; $i < count($centroids1); $i++) {
            if (
                $centroids1[$i][0] != $centroids2[$i][0] ||
                $centroids1[$i][1] != $centroids2[$i][1]
            ) {
                return false;
            }
        }

        return true;
    }
}
