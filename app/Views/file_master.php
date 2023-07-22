<?= $this->extend('templates/index'); ?>

<?= $this->section('pages-content'); ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php if ($msg != null) : ?>
                    <div class="alert alert-primary alert-dismissible fade show text-light" role="alert">
                        <?= $msg; ?>
                        <button type="button" class="close text-light" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tabel Cluster</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="example">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>File_ID</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php

                                // Loop through your 'Cluster' data and populate the table rows
                                foreach ($file as $fileId) {
                                    echo "<tr>";
                                    echo "<td>" . $i++ . "</td>";
                                    echo "<td>" . $fileId['file_id'] . " - " . $fileId['total'] . " Data</td>";
                                    echo "<td> <a href='" . site_url('/delete-data/') .
                                        $fileId['file_id'] . "' class='btn btn-danger delData'>Delete</a> </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>