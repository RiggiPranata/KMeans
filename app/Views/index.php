<?= $this->extend('templates/index'); ?>

<?= $this->section('pages-content'); ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">K-Means visualisasion</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg"><span class="mr-1" id="countData"></span>Data</span>
                                <span>Clustering</span>
                            </p>
                            <div class="ml-auto d-flex flex-column pr-2">
                                <label for="fileId" class="ml-3">Pilih file :</label>
                                <select name="fileId" id="fileId" class="form-control custom-select ml-2 ">
                                    <?php foreach ($file as $f) : ?>
                                        <option value="<?= $f['file_id']; ?>"><?= $f['file_id']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="visitors-chart" height="200"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-4">
                                <i class="fas fa-square" style="color: rgba(70, 164, 211, 5);"></i>Cluster 0<br>Kurang Laris
                            </span>
                            <span class="mr-4   ">
                                <i class="fas fa-square" style="color: rgba(10, 210, 129, 2);"></i>Cluster 1<br>Laris
                            </span>
                            <span>
                                <i class="fas fa-square" style="color: rgba(255, 69, 0, 1.0);"></i>Cluster 2<br>Paling Laris
                            </span>
                        </div>
                    </div>
                </div>
                <!-- /.card -->


                <!-- /.card -->
            </div>
            <!-- /.col-md-6 -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Presentase Clustering</h3>
                    </div>
                    <div class="card-body" id="contPiC">
                        <canvas id="pieChart"></canvas>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.col-md-6 -->

        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">Products</h3>
                    </div>
                    <hr>
                    <div class="card-body table-responsive p-0">
                        <p class="ml-4">Cluster 0/Kurang Laris : <span id="c0"></span></p>

                        <p class="ml-4">Cluster 1/Laris : <span id="c1"></span></p>
                        <p class="ml-4">Cluster 2/Paling Laris : <span id="c2"></span></p>
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Volume Penjualan</th>
                                    <th>Cluster</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- data render -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
<?= $this->endSection(); ?>