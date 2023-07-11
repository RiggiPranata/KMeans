<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KCS | <?= $title; ?></title>

    <base href="<?= base_url('adminlte'); ?>/">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- dropzonejs -->
    <link rel="stylesheet" href="plugins/dropzone/min/dropzone.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <?php if (isset($noRe)) : ?>
        <?= $noRe; ?>
    <?php endif; ?>

</head>
<!--
`body` tag options:

  Apply one or more of the following classes to to the body tag
  to get the desired effect

  * sidebar-collapse
  * sidebar-mini
-->

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?= $this->include('templates/navbar'); ?>

        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?= $this->include('templates/sidebar'); ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $title; ?></h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php if ($segment == 1) : ?>
                                    <li class="breadcrumb-item"><a href="<?= site_url(); ?>">Dasboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?= $title; ?></li>
                                <?php elseif ($segment == 0) : ?>
                                    <li class="breadcrumb-item active" aria-current="page"><?= $title; ?></li>
                                <?php endif; ?>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <?= $this->renderSection('pages-content') ?>


            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <?= $this->include('templates/footer'); ?>

    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE -->
    <script src="dist/js/adminlte.js"></script>

    <!-- OPTIONAL SCRIPTS -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <!-- dropzonejs -->
    <script src="plugins/dropzone/min/dropzone.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <!-- <script src="dist/js/pages/dashboard3.js"></script> -->

    <!-- ADDING  NEW -->
    <script>
        var hasil
        $(function() {
            var file = $('#fileId').val();
            $('#fileId').on('change', function() {
                $.ajax({
                    contentType: JSON,
                    url: "/clustering/" + file + "",
                    type: 'post',
                    success: function(result) {
                        hasil = result
                    }

                });
            });
            $('#fileId').on('load', function() {
                $.ajax({
                    url: "/clustering/" + file + "",
                    type: 'post',
                    success: function(result) {
                        hasil = result
                    }

                });
            });
            // Get context with jQuery - using jQuery's .get() method.

            var ticksStyle = {
                fontColor: '#495057',
                fontStyle: 'bold'
            }

            var mode = 'index'
            var intersect = true



            var $visitorsChart = $('#visitors-chart')
            // eslint-disable-next-line no-unused-vars
            var visitorsChart = new Chart($visitorsChart, {
                data: {
                    labels: [
                        <?php foreach ($jumlah_transaksi as $jt) : ?>
                            <?= $jt['jumlah_transaksi']; ?>,
                        <?php endforeach; ?>
                    ],
                    datasets: [
                        // {
                        //     type: 'bubble',
                        //     data: [
                        //         <?php foreach ($jumlah_transaksi as $jt) : ?>
                        //             <?= $jt['jumlah_transaksi']; ?>,
                        //         <?php endforeach; ?>
                        //     ],
                        //     backgroundColor: '#007bff',
                        //     // borderColor: '#007bff',
                        //     // pointBorderColor: '#007bff',
                        //     // pointBackgroundColor: '#007bff',
                        //     fill: true
                        //     // pointHoverBackgroundColor: '#007bff',
                        //     // pointHoverBorderColor    : '#007bff'
                        // },
                        {
                            type: 'bubble',
                            data: [
                                <?php foreach ($volume_penjualan as $vp) : ?>
                                    <?= $vp['volume_penjualan']; ?>,
                                <?php endforeach; ?>
                            ],
                            backgroundColor: '#00FF00',
                            // borderColor: '#007bff',
                            // pointBorderColor: '#007bff',
                            // pointBackgroundColor: '#007bff',
                            fill: true
                            // pointHoverBackgroundColor: '#ced4da',
                            // pointHoverBorderColor    : '#ced4da'
                        },
                        //  {
                        //     type: 'bubble',
                        //     data: [100, 67, 80, 77, 67, 80, 77],
                        //     backgroundColor: '#6F11F7',
                        //     // borderColor: '#007bff',
                        //     // pointBorderColor: '#007bff',
                        //     // pointBackgroundColor: '#007bff',
                        //     fill: true
                        //     // pointHoverBackgroundColor: '#ced4da',
                        //     // pointHoverBorderColor    : '#ced4da'
                        // }
                    ]
                },
                options: {
                    maintainAspectRatio: true,
                    tooltips: {
                        mode: mode,
                        intersect: intersect
                    },
                    hover: {
                        mode: mode,
                        intersect: intersect
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                lineWidth: '4px',
                                color: 'rgba(0, 0, 0, .2)',
                                zeroLineColor: '#00000'
                            },
                            ticks: $.extend({
                                beginAtZero: true,
                                suggestedMax: 140
                            }, ticksStyle)
                        }],
                        xAxes: [{
                            // display: true,
                            gridLines: {
                                display: true,
                                lineWidth: '4px',
                                color: 'rgba(0, 0, .2, .2)',
                                zeroLineColor: '#00000'
                            },
                            ticks: ticksStyle,
                        }]
                    }
                }
            })

            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

        })
        // DropzoneJS Demo Code Start
        Dropzone.autoDiscover = false

        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
        var previewNode = document.querySelector("#template")
        previewNode.id = ""
        var previewTemplate = previewNode.parentNode.innerHTML
        previewNode.parentNode.removeChild(previewNode)

        var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
            url: "/target-url", // Set the url
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            autoQueue: false, // Make sure the files aren't queued until manually added
            previewsContainer: "#previews", // Define the container to display the previews
            clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
        })

        myDropzone.on("addedfile", function(file) {
            // Hookup the start button
            file.previewElement.querySelector(".start").onclick = function() {
                myDropzone.enqueueFile(file)
            }
        })

        // Update the total progress bar
        myDropzone.on("totaluploadprogress", function(progress) {
            document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
        })

        myDropzone.on("sending", function(file) {
            // Show the total progress bar when upload starts
            document.querySelector("#total-progress").style.opacity = "1"
            // And disable the start button
            file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
        })

        // Hide the total progress bar when nothing's uploading anymore
        myDropzone.on("queuecomplete", function(progress) {
            document.querySelector("#total-progress").style.opacity = "0"
        })

        // Setup the buttons for all transfers
        // The "add files" button doesn't need to be setup because the config
        // `clickable` has already been specified.
        document.querySelector("#actions .start").onclick = function() {
            myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
        }
        document.querySelector("#actions .cancel").onclick = function() {
            myDropzone.removeAllFiles(true)
        }

        Dropzone.options.myGreatDropzone = { // camelized version of the `id`
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 2, // MB
            accept: function(file, done) {
                if (file.name == NULL) {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            }
        };

        // DropzoneJS Demo Code End
    </script>


</body>

</html>