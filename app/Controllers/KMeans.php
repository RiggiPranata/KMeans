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
    protected $barangModel;
    protected $db;

    public function __construct()
    {
        $this->db      = \Config\Database::connect();
        $this->builder = $this->db->table('clusters');
        $this->barang = $this->db->table('barang');
        $this->uri = service('uri');
        $this->urisegments = $this->uri->getTotalSegments();
        $this->clusterModel = new \App\Models\ClusterModel();
        $this->barangModel = new \App\Models\BarangModel();
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
            $barang[] = [
                "kode_barang" => $tb[0],
                "nama" => $tb[1]
            ];
        }
        // dd($barang);
        // Lakukan proses untuk mengecek dan mengganti data
        foreach ($barang as $row) {
            // dd($row['kode_barang']);
            $kode_barang = $row['kode_barang'];

            // Cek apakah data dengan kode_barang sudah ada di tabel barang
            if ($this->barangModel->isDuplicate($kode_barang)) {
                // Jika ada duplikasi, ambil data yang sudah ada di tabel
                $existingData = $this->barangModel->getDataByKodeBarang($kode_barang);

                // Tentukan kriteria untuk menghapus data duplikasi
                // Misalnya, data dengan waktu update paling lama akan dihapus
                $dataToDelete = $this->barangModel->getDataToDelete($kode_barang);

                // Mulai transaksi database
                $this->db->transStart();

                // Hapus data duplikasi berdasarkan kriteria yang telah ditentukan
                foreach ($dataToDelete as $dataR) {

                    $this->barangModel->deleteData($dataR['kode_barang']);
                }

                // Simpan data baru ke tabel barang
                $this->barangModel->insert($row);

                // Selesai transaksi database
                $this->db->transComplete();
            } else {
                // Jika tidak ada duplikasi, langsung simpan data baru ke tabel barang
                $this->barangModel->insert($row);
            }
        }

        // Inisialisasi jumlah cluster
        $numClusters = 3;

        // Inisialisasi centroid awal secara acak
        $centroids = $this->initializeCentroids($data, $numClusters);

        // Inisialisasi variabel iterasi dan batas iterasi
        $iteration = 0;
        $maxIterations = 10;

        // dd($centroids);
        // Mulai iterasi perhitungan k-means clustering
        while ($iteration < $maxIterations) {
            $clusters = $this->assignDataToClusters($data, $centroids);
            $newCentroids = $this->calculateNewCentroids($clusters, $data);
            // d($centroids);
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
        $uniqueJumlahTransaksi = array_unique(array_column($data, 0), SORT_NUMERIC);

        // Mengambil 3 nilai unik terbesar dari 'jumlah_transaksi'
        shuffle($uniqueJumlahTransaksi);
        $selectedJumlahTransaksi = [];
        sort($uniqueJumlahTransaksi);
        $minSelectedJumlahTransaksi = array_slice($uniqueJumlahTransaksi, 4, 1);
        $selectedJumlahTransaksi = array_merge($selectedJumlahTransaksi, $minSelectedJumlahTransaksi);

        rsort($uniqueJumlahTransaksi);
        $uniqueJumlahTransaksiMax = array_slice($uniqueJumlahTransaksi, 0, 20);
        shuffle($uniqueJumlahTransaksiMax);
        sort($uniqueJumlahTransaksiMax);
        $midSelectedJumlahTransaksi = array_slice($uniqueJumlahTransaksiMax, 4, 1);
        $selectedJumlahTransaksi = array_merge($selectedJumlahTransaksi, $midSelectedJumlahTransaksi);

        rsort($uniqueJumlahTransaksiMax);
        $maxSelectedJumlahTransaksi = array_slice($uniqueJumlahTransaksiMax, 4, 1);
        $selectedJumlahTransaksi = array_merge($selectedJumlahTransaksi, $maxSelectedJumlahTransaksi);




        // Mengambil data dengan nilai 'jumlah_transaksi' yang sesuai
        sort($data);
        foreach ($data as $row) {
            if (in_array($row[0], $selectedJumlahTransaksi)) {
                // Jika nilai $row[0] sudah ada di $selectedJumlahTransaksi, maka cek apakah nilai tersebut sudah ada di centroids[]
                $found = false;
                foreach ($centroids as $centroid) {
                    if ($centroid[0] === $row[0]) {
                        // Jika nilai sudah ada di centroids[], set $found menjadi true
                        $found = true;
                        break;
                    }
                }

                // Jika nilai belum ada di centroids[], tambahkan ke centroids[]
                if (!$found) {
                    $centroids[] = [$row[0], $row[1]];
                }
            }

            // Jika jumlah data di centroids[] sudah mencapai 3, berhenti dari perulangan
            $jml = count($centroids);
            if ($jml == 3) {
                break;
            }
        }
        // dd($data, $row, $centroids, $uniqueJumlahTransaksi, $uniqueJumlahTransaksiMax, $selectedJumlahTransaksi);
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
                // d($data[$i][0], $centroids[$j][0], $data[$i][1], $centroids[$j][1], pow(($data[$i][0] - $centroids[$j][0]), 2), pow(($data[$i][1] - $centroids[$j][1]), 2), $distance);
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
        // sort($clusterData);
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
        // dd($newCentroids, $clusters, $clusterData, $meanTransaksi, $meanVolume);
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
