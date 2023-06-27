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
                                <input type="file" class="form-control-file" id="excelFile" name="excelFile">
                            </div>
                            <button type="submit" class="btn btn-primary">Upload dan Proses</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>