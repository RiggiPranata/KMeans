<?= $this->extend('templates/index'); ?>

<?= $this->section('pages-content'); ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Hasil Perhitungan K-Means Clustering</h3>
                        <br><small class="text-red"><?= $message; ?></small><br>
                        <?php if ($message == "Terjadi kesalahan saat mengunggah file. Pastikan file yang diunggah adalah file Excel (format .xlsx)") : ?>

                        <?php else : ?>
                            <?php if (is_array($c0) || is_object($c0)) : ?>
                                <p class="mb-0">Produk kurang laris : <?= $c0c[0]['kode_barang']; ?></p>
                                <?php foreach ($c0 as $kl) : ?>
                                    <?= $kl; ?>,
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="mb-0">Produk kurang laris : </p>
                                <p class="mb-0">Tidak ada data yang memenuhi</p>
                            <?php endif; ?>
                            <?php if (is_array($c1) || is_object($c1)) : ?>
                                <p class="mb-0">Produk laris : <?= $c1c[0]['kode_barang']; ?></p>
                                <?php foreach ($c1 as $l) : ?>
                                    <?= $l; ?>,
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="mb-0">Produk laris : </p>
                                <p class="mb-0">Tidak ada data yang memenuhi</p>
                            <?php endif; ?>
                            <?php if (is_array($c2) || is_object($c2)) : ?>
                                <p class="mb-0">Produk paling laris : <?= $c2c[0]['kode_barang']; ?></p>
                                <?php foreach ($c2 as $pl) : ?>
                                    <?= $pl; ?>,
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="mb-0">Produk paling laris : </p>
                                <p class="mb-0">Tidak ada data yang memenuhi</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (isset($clusters) && !empty($clusters)) : ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Jumlah Transaksi</th>
                                        <th>Volume Penjualan</th>
                                        <th>Cluster</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clusters as $cluster) : ?>
                                        <tr>
                                            <td><?= $cluster['kode_barang'] ?></td>
                                            <td><?= $cluster['jumlah_transaksi'] ?></td>
                                            <td><?= $cluster['volume_penjualan'] ?></td>
                                            <td><?= $cluster['cluster'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <p>Tidak ada hasil perhitungan yang ditampilkan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>