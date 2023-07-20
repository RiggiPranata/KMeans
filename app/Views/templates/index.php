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

    <?php

    use Myth\Auth\Entities\User;

    if (isset($noRe)) : ?>
        <?= $noRe; ?>
    <?php endif; ?>

    <style>
        .dataTables_length {
            margin-top: 10px;
            margin-left: 20px;
        }

        .dataTables_filter {
            margin-right: 20px;
        }

        .dataTables_info {
            margin-left: 20px;
            margin-bottom: 10px;



        }

        .fa-user-circle {
            <?= (User()->activate()) ? 'color: #00FE00; ' : 'color: red;'; ?>
        }
    </style>
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
        var data = [];
        var donutData = {};
        $(function() {
            // Inisialisasi chart dan konfigurasi
            var ctx = document.getElementById('visitors-chart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bubble',
                data: {
                    datasets: [{
                        label: 'Data',
                        data: [],
                        // backgroundColor: []

                    }]
                },
                options: {
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom'
                        },
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Value: ' + context.parsed.x;
                                }
                            }
                        }
                    },
                    legend: {
                        display: false // Nonaktifkan legend
                    },
                }
            });

            // Event listener untuk perubahan pilihan jenis file
            $(document).ready(function() {
                $('#fileId').change(function() {
                    var fileType = $(this).val();
                    updateChart(fileType);
                });
            });

            // Mendapatkan data dari server dan mengupdate chart
            function updateChart(fileType) {
                $.ajax({
                    url: '<?php echo base_url("/clustering"); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        fileType: fileType
                    },
                    success: function(response) {
                        // Menyiapkan array untuk data dan backgroundColor
                        var chartData = [];
                        var chartBackgroundColor = [];

                        // Mengisi array chartData dan chartBackgroundColor dengan data yang sesuai
                        response.forEach(function(item) {
                            chartData.push({
                                x: item.x,
                                y: item.y,
                                r: item.r
                            });
                            chartBackgroundColor.push(item.backgroundColor);
                        });
                        // jumlah data
                        $('#countData').append(response[0].dataLength[0].length);
                        // jumlah data perklaster
                        c0 = response[0].c0[0].cluster;
                        c1 = response[0].c1[0].cluster;
                        c2 = response[0].c2[0].cluster;
                        $('#c0').append(c0);
                        $('#c1').append(c1);
                        $('#c2').append(c2);

                        // pie chart
                        donutData = {
                            labels: [
                                'Kurang Laris',
                                'Laris',
                                'Paling Laris',
                            ],
                            datasets: [{
                                data: [c0, c1, c2],
                                backgroundColor: ['rgba(70, 164, 211, 5)', 'rgba(10, 210, 129, 2)', 'rgba(255, 69, 0, 1.0)'],
                            }]
                        }

                        // Get context with jQuery - using jQuery's .get() method.
                        var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
                        var pieData = donutData;
                        var pieOptions = {
                            maintainAspectRatio: false,
                            responsive: true,
                        }
                        //Create pie or douhnut chart
                        // You can switch between pie and douhnut using the method below.
                        new Chart(pieChartCanvas, {
                            type: 'pie',
                            data: pieData,
                            options: pieOptions
                        })

                        // table
                        var kodeProduk = [];

                        kodeProduk = response[0].kode_barang.map(kb => {
                            return kb.kode_barang;
                        });
                        namaProduk = response[0].nama_barang.map(nb => {
                            return nb.nama;
                        });
                        clusterProduk = response[0].cluster.map(nb => {
                            return nb.cluster;
                        });


                        for (var i = 0; i < response[0].dataLength[0].length; i++) {
                            // var oe = (i % 2 == 0) ? "odd" : "even";
                            // var baris_baru = "<tr class=" + oe + "" + " ><td class='dtr-control sorting_1' tabindex='0'>" + kodeProduk[i] + " - " + namaProduk[i] + "</td><td>" + chartData[i].x + "</td><td>" + chartData[i].y + "</td><td>" + clusterProduk[i] + "</td></tr>";
                            // $("#example2").append(baris_baru);
                            dataLop = [
                                kodeProduk[i] + " - " + namaProduk[i],
                                chartData[i].x,
                                chartData[i].y,
                                clusterProduk[i]
                            ]
                            data.push(dataLop);

                        }

                        $('#example2').DataTable({
                            "data": data,
                            "paging": true,
                            "lengthChange": true,
                            "searching": true,
                            "ordering": true,
                            "info": true,
                            "autoWidth": false,
                            "responsive": true,
                            "lengthMenu": [
                                [10, 20, 50, -1],
                                [10, 20, 50, "All"]
                            ], // Menampilkan pilihan jumlah record per halaman
                            "pageLength": 20 // Jumlah record per halaman yang akan ditampilkan secara default

                        });
                        // console.log(data);

                        // Memperbarui data dan warna pada chart
                        chart.data.datasets[0].data = chartData;
                        chart.data.datasets[0].backgroundColor = chartBackgroundColor;
                        // memperbaharui length data

                        chart.update();
                        // Memperbarui data dan warna pada chart

                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }




            // Pertama kali, memuat data untuk jenis file default
            var defaultFileType = $('#fileId').val();

            updateChart(defaultFileType);




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