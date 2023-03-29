<?php

    require 'ceklogin.php';

    if(isset($_GET['idp'])){
        $idp = $_GET['idp'];

        $ambilnamapelanggan = mysqli_query($c,"select * from pesanan p, pelanggan pl where p.idpelanggan=pl.idpelanggan and p.idorder='$idp'");
        $np = mysqli_fetch_array($ambilnamapelanggan);
        $namapel = $np['namapelanggan'];

    } else{
        header('location:index.php');
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Data Pesanan</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php">Aplikasi Kasir Solihah</a>
            <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Menu</div>
                            <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                                Order
                            </a>
                            <a class="nav-link" href="stock.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-warehouse"></i></div>
                                Stock Barang
                            </a>
                            <a class="nav-link" href="masuk.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>
                                Barang Masuk
                            </a>
                            <a class="nav-link" href="pelanggan.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-users-medical"></i></div>
                                Pelanggan
                            </a>
                            <a class="nav-link" size="50px" href="logout.php">
                                Logout
                            </a>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <h4 class="mt-4">Data Pesanan : <?=$idp;?></h4>
                        <h4 class="mt-4">Nama Pelanggan : <?=$namapel;?></h4>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Selamat Datang</li>
                        </ol>

                        <!-- Button to Open the Modal -->
                            <button type="button" class="btn btn-info mb-4" data-toggle="modal" data-target="#myModal">
                                Tambah Barang
                            </button>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table mr-1"></i>
                                Data Pesanan
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Produk</th>
                                                <th>Harga Satuan</th>
                                                <th>Jumlah</th>
                                                <th>Sub-total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>


                                        <?php
                                        $get = mysqli_query($c,"SELECT * FROM detailorder p, produk pr WHERE p.idproduk=pr.idproduk
                                        and idorder='$idp'");
                                        $i = 1;

                                        while($p=mysqli_fetch_array($get)){
                                        $idpr = $p['idproduk'];
                                        $iddp = $p['iddetailorder'];
                                        $qty = $p['qty'];
                                        $harga = $p['harga'];
                                        $namaproduk = $p['namaproduk'];
                                        $desc = $p['deskripsi'];
                                        $subtotal = $qty * $harga;
                                        
                                        ?>

                                            <tr>
                                                <td><?= $i++;?></td>
                                                <td><?= $namaproduk;?> (<?=$desc;?>)</td>
                                                <td>Rp <?= number_format($harga);?></td>
                                                <td><?= number_format($qty);?></td>
                                                <td>Rp <?= number_format($subtotal);?></td>
                                                <td>

                                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#edit<?=$idpr;?>">
                                                        Edit
                                                    </button> 

                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete<?=$idpr;?>">
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- The Modal Edit-->
                                            <div class="modal fade" id="edit<?=$idpr;?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                            
                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                <h4 class="modal-title">Ubah Data Detail Pesanan</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <form method="post">
                                                
                                                <!-- Modal body -->
                                                <div class="modal-body">
                                                    <input type="text" name="namaproduk" class="form-control" placeholder="Nama Produk" value="<?=$namaproduk?> : <?=$desc?>" disabled>
                                                    <input type="number" name="jumlah" class="form-control mt-2" placeholder="Jumlah Produk" value="<?=$qty?>">
                                                    <input type="hidden" name="iddp" value="<?=$iddp;?>">
                                                    <input type="hidden" name="idp" value="<?=$idp;?>">
                                                    <input type="hidden" name="idpr" value="<?=$idpr;?>">
                                                </div>
                                                
                                                <!-- Modal footer -->
                                                <div class="modal-footer">
                                                <button type="submit" class="btn btn-success" name="editdetailpesanan">Submit</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                </div>


                                                </form>


                                                    </div>
                                                </div>
                                            </div>

                                            <!-- The Modal -->
                                        <div class="modal fade" id="delete<?=$idpr;?>">
                                        <div class="modal-dialog">
                                        <div class="modal-content">
                                    
                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                        <h4 class="modal-title">Hapus Barang</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <form method="post">
                                        
                                        <!-- Modal body -->
                                        <div class="modal-body">
                                            Apakah Anda Yakin Ingin Menghapus Barang ini?
                                            <input type="hidden" name="idp" value="<?=$iddp;?>">
                                            <input type="hidden" name="idpr" value="<?=$idpr;?>">
                                            <input type="hidden" name="idorder" value="<?=$idp;?>">
                                        </div>
                                        
                                        <!-- Modal footer -->
                                        <div class="modal-footer">
                                        <button type="submit" class="btn btn-success" name="hapusproduk">Ya</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        </div>


                                        </form>


                                    </div>
                                    </div>
                                    </div>
                                        <?php

                                        };

                                        ?>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2020</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/datatables-demo.js"></script>
    </body>

     <!-- The Modal -->
    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
        <div class="modal-content">
        
            <!-- Modal Header -->
            <div class="modal-header">
            <h4 class="modal-title">Tambah Barang Baru</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form method="post">
            
            <!-- Modal body -->
            <div class="modal-body">
                Pilih Barang
                <select name="idproduk" class="form-control">

                <?php
                $getproduk = mysqli_query($c,"select * from produk where idproduk not in (select idproduk from detailorder where idorder='$idp')");

                while($pl = mysqli_fetch_array($getproduk)){
                    $namaproduk = $pl['namaproduk'];
                    $stock = $pl['stock'];
                    $deskripsi = $pl['deskripsi'];
                    $idproduk = $pl['idproduk'];
                
                ?>

                <option value="<?=$idproduk;?>"><?=$namaproduk;?> - <?=$deskripsi;?> (Stock: <?=$stock;?>)</option>

                <?php

                }

                ?>

                </select>

                <input type="number" name="qty" class="form-control mt-2" placeholder="Jumlah"  min="1" required>
                <input type="hidden" name="idp" value="<?=$idp;?>">
            </div>
            
            <!-- Modal footer -->
            <div class="modal-footer">
            <button type="submit" class="btn btn-success" name="addproduk">Submit</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>


            </form>


        </div>
        </div>
    </div>
</html>
