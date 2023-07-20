<?php

use Myth\Auth\Entities\User;
?>
<?= $this->extend('templates/index'); ?>

<?= $this->section('pages-content'); ?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Your profile information goes here -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">User Profile</h5>
                <p class="card-text"><strong>Email : </strong> <?= User()->email; ?></p>
                <p class="card-text"><strong>Username : </strong> <?= User()->username; ?></p>
                <!-- <a href="<?= site_url('reset-password'); ?>" class="btn btn-primary">Reset Password</a> -->
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<?= $this->endSection(); ?>