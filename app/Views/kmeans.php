<?= $this->extend('templates/index'); ?>

<?= $this->section('pages-content'); ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Upload File Excel</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?= site_url('process-upload') ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="excelFile">Pilih File Excel</label>
                                <input type="file" class="form-control-file" id="excelFile" name="excelFile" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload dan Proses</button>
                        </form>
                        <br>
                        <p class="card-text mb-0">Format kolom excel: kode_barang, nama_barang, jumlah_transaksi, volume_penjualan</p>
                        <small class="card-text text-danger">format data berupa angka desimal kecuali untuk kolom nama_barang</small>
                        <br>
                        <a href="https://drive.google.com/drive/folders/1m6TX1XeHLt7_orliVKxFqTzNFkDIfDWG?usp=sharing" target="_blank" class="card-link">Download template excel</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>