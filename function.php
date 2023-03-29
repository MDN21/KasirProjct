<?php

session_start();

//Buat Koneksi
$c = mysqli_connect('localhost','root','','kasir');

if(isset($_POST['login'])){
    //initiate
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = mysqli_query($c, "SELECT * FROM user WHERE username='$username' and password='$password' ");
    $hitung = mysqli_num_rows($check);

    if($hitung > 0){
        //Jika datanya ditemukan berhasil login

        $_SESSION['login'] = 'True';
        header('location:index.php');
    } else {
        //Data tidak ditemukan 
        echo '
        <script>
        alert("Username atau Password salah");
        window.location.href="login.php"
        </script>
        ';
    }
}

if(isset($_POST['tambahbarang'])){
    $namaproduk = $_POST['namaproduk'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    $harga = $_POST['harga'];

    $insert = mysqli_query($c,"insert into produk (namaproduk,deskripsi,harga,stock) values ('$namaproduk','$deskripsi','$harga','$stock')");

    if($insert){
        header('location:stock.php');
    } else{
        echo '
        <script>
        alert("Gagal Menambah Barang Baru");
        window.location.href="stock.php"
        </script>
        ';
    }
}

if(isset($_POST['tambahpelanggan'])){
    $namapelanggan = $_POST['namapelanggan'];
    $notelp = $_POST['notelp'];
    $alamat = $_POST['alamat'];

    $insert = mysqli_query($c,"insert into pelanggan (namapelanggan,notelp,alamat) values ('$namapelanggan','$notelp','$alamat')");

    if($insert){
        header('location:pelanggan.php');
    } else{
        echo '
        <script>
        alert("Gagal Menambah Pelanggan Baru");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
}

if(isset($_POST['tambahpesanan'])){
    $idpelanggan = $_POST['idpelanggan'];
    $insert = mysqli_query($c,"insert into pesanan (idpelanggan) values ('$idpelanggan')");

    if($insert){
        header('location:index.php');
    } else{
        echo '
        <script>
        alert("Gagal Menambah Pesanan Baru");
        window.location.href="pesanan.php"
        </script>
        ';
    }
}

//Produk DI pesan
if(isset($_POST['addproduk'])){
    $idproduk = $_POST['idproduk'];
    $idp = $_POST['idp']; //id pesanan
    $qty = $_POST['qty']; //jumlah yang mau dikeluarkan

    //Hitung stock sekarang ada berapaa
    $hitung1 = mysqli_query($c,"select * from produk where idproduk='$idproduk'");
    $hitung2 = mysqli_fetch_array($hitung1);
    $stocksekarang = $hitung2['stock']; //stock barang saat ini 

    if($stocksekarang >= $qty){

        //Kurangi stocknya dengan jumlah yang akan dikeluarkan
        $selisih = $stocksekarang - $qty;

        //stocknya cukup
        $insert = mysqli_query($c,"insert into detailorder (idorder,idproduk,qty) values ('$idp','$idproduk','$qty')");
        $update = mysqli_query($c,"update produk set stock='$selisih' where idproduk='$idproduk'");

        if($insert&&$update){
            header('location:view.php?idp='.$idp);
        } else{
            echo '
            <script>
            alert("Gagal Menambah Pesanan Baru");
            window.location.href="view.php?idp='.$idp.'"
            </script>
            ';
        }
    } else{
        //stock gak cukup
            echo '
            <script>
            alert("Stock Barang Tidak Mencukupi");
            window.location.href="view.php?idp='.$idp.'"
            </script>
            ';
    }
}

//Menambah barang masuk 
    if(isset($_POST['barangmasuk'])){
        $idproduk = $_POST['idproduk'];
        $qty = $_POST['qty'];

        //cari tahu berapa stock yang ada sekarang
        $caristock = mysqli_query($c,"select * from produk where idproduk='$idproduk'");
        $caristock2 = mysqli_fetch_array($caristock);
        $stocksekarang = $caristock2['stock'];

        $newstock = $stocksekarang + $qty;

        $insertbarangmasuk = mysqli_query($c,"insert into masuk (idproduk,qty) values('$idproduk','$qty')");
        $updatetb = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idproduk'");

        if($insertbarangmasuk&&$updatetb){
            //Berhasil
            header('location:masuk.php');
        } else{
            //Gagal
            echo '
            <script>
            alert("Gagal Menambah Barang");
            window.location.href="masuk.php"
            </script>
            ';
        }
    }

    //Hapus Produk Pesanan
    if(isset($_POST['hapusproduk'])){
        $idp = $_POST['idp'];
        $idpr = $_POST['idpr'];
        $idorder = $_POST['idorder'];

        //Cek qty Sekarang
        $cek1 = mysqli_query($c,"select * from detailorder where iddetailorder='$idp'");
        $cek2 = mysqli_fetch_array($cek1);
        $qtysekarang = $cek2['qty'];

        //Cek stok sekarang
        $cek3 = mysqli_query($c,"select * from produk where idproduk='$idpr'");
        $cek4 = mysqli_fetch_array($cek3);
        $stockskr = $cek4['stock'];

        $hitung = $stockskr + $qtysekarang;

        $update = mysqli_query($c,"update produk set stock='$hitung' where idproduk='$idpr'"); //Update stock
        $hapus = mysqli_query($c,"delete from detailorder where idproduk='$idpr' and iddetailorder='$idp'");

        if($update&&$hapus){
            //Berhasil
            header('location:view.php?idp='.$idorder);
        } else{
            echo '
            <script>
            alert("Gagal Menghapus Barang");
            window.location.href="view.php?idp='.$idorder.'"
            </script>
            ';
        }
    }

    //edit barang
    if(isset($_POST['editbarang'])){
        $np = $_POST['namaproduk'];
        $deskripsi = $_POST['deskripsi'];
        $harga = $_POST['harga'];
        $idp = $_POST['idp'];

        $update = mysqli_query($c,"update produk set namaproduk='$np', deskripsi='$deskripsi', harga='$harga' where idproduk='$idp'");

        if($update){
            //Berhasil
            header('location:stock.php');
        } else{
            //Gagal
            echo '
            <script>
            alert("Gagal");
            window.location.href="stock.php"
            </script>
            ';
        }
    }

    //hapus barang
    if(isset($_POST['hapusbarang'])){
        $idp = $_POST['idp'];

        $delete = mysqli_query($c,"delete from produk where idproduk='$idp'");

        if($delete){
            //Berhasil
            header('location:stock.php');
        } else{
            //Gagal
            echo '
            <script>
            alert("Gagal");
            window.location.href="stock.php"
            </script>
            ';
        }
    }

    //edit pelanggan
    if(isset($_POST['editpelanggan'])){
        $np = $_POST['namapelanggan'];
        $nt = $_POST['notelp'];
        $a = $_POST['alamat'];
        $id = $_POST['idpl'];

        $update = mysqli_query($c,"update pelanggan set namapelanggan='$np', notelp='$nt', alamat='$a' where idpelanggan='$id'");

        if($update){
            //Berhasil
            header('location:pelanggan.php');
        } else{
            //Gagal
            echo '
            <script>
            alert("Gagal");
            window.location.href="pelanggan.php"
            </script>
            ';
        }
    }

    //delete pelanggan
    if(isset($_POST['hapuspelanggan'])){
        $idpl = $_POST['idpl'];

        $delete = mysqli_query($c,"delete from pelanggan where idpelanggan='$idpl'");

        if($delete){
            //Berhasil
            header('location:pelanggan.php');
        } else{
            //Gagal
            echo '
            <script>
            alert("Gagal");
            window.location.href="pelanggan.php"
            </script>
            ';
        }
    }

    //edit barang masuk
    if(isset($_POST['editbarangmasuk'])){
        //Berhasil
        $jumlah = $_POST['jumlah'];
        $idm = $_POST['idm']; //id masuk
        $idp = $_POST['idp']; //id produk

        //Cari tahu qty sekarang berapa
        $caritahu = mysqli_query($c,"select * from masuk where idmasuk='$idm'");
        $caritahu2 = mysqli_fetch_array($caritahu);
        $qtysekarang = $caritahu2['qty'];

        //cari tahu berapa stock yang ada sekarang
        $caristock = mysqli_query($c,"select * from produk where idproduk='$idp'");
        $caristock2 = mysqli_fetch_array($caristock);
        $stocksekarang = $caristock2['stock'];

        if($jumlah >= $qtysekarang){
            //Kalau inputan user lebih besar dari pada qty yang tercatat sekaarng
            //Hitung selisih
            $selisih = $jumlah - $qtysekarang;
            $newstock = $stocksekarang+$selisih;

            $update1 = mysqli_query($c,"update masuk set qty='$jumlah' where idmasuk='$idm'");
            $update2 = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idp'");

            if($update1&&$update2){
                header('location:masuk.php');
            } else{
                echo'
                <script>
                alert("Gagal");
                window.location.href="masuk.php"
                </script>
                ';
            }
        } else {
            //kalau lebih kecil
            //selisih 
            $selisih = $qtysekarang - $jumlah;
            $newstock = $stocksekarang-$selisih;

            $update1 = mysqli_query($c,"update masuk set qty='$jumlah' where idmasuk='$idm'");
            $update2 = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idp'");

            if($update1&&$update2){
                header('location:masuk.php');
            } else{
                echo'
                <script>
                alert("Gagal");
                window.location.href="masuk.php"
                </script>
                ';
            }

        }

    }

    //hapus data barang masuk
    if(isset($_POST['hapusbarangmasuk'])){
        $idm = $_POST['idm'];
        $idp = $_POST['idp'];

        //Cari tahu qty sekarang berapa
        $caritahu = mysqli_query($c,"select * from masuk where idmasuk='$idm'");
        $caritahu2 = mysqli_fetch_array($caritahu);
        $qtysekarang = $caritahu2['qty'];

        //cari tahu berapa stock yang ada sekarang
        $caristock = mysqli_query($c,"select * from produk where idproduk='$idp'");
        $caristock2 = mysqli_fetch_array($caristock);
        $stocksekarang = $caristock2['stock'];

        //kalau lebih kecil
        //selisih 
        $newstock = $stocksekarang-$qtysekarang;

        $update1 = mysqli_query($c,"delete from masuk where idmasuk='$idm'");
        $update2 = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idp'");

        if($update1&&$update2){
            header('location:masuk.php');
        } else{
            echo'
            <script>
            alert("Gagal");
            window.location.href="masuk.php"
            </script>
            ';
        }

        
    }

    //hapus orderan
    if(isset($_POST['hapusorder'])){
        $ido = $_POST['ido'];

        $cekdata = mysqli_query($c,"select * from detailorder where idorder='$ido'");

        while($ok=mysqli_fetch_array($cekdata)){
            //balikin stoknya
            $qty = $ok['qty'];
            $idproduk = $ok['idproduk'];
            $iddp = $ok['iddetailorder'];

             //cari tahu berapa stock yang ada sekarang
            $caristock = mysqli_query($c,"select * from produk where idproduk='$idproduk'");
            $caristock2 = mysqli_fetch_array($caristock);
            $stocksekarang = $caristock2['stock'];

            $newstock = $stocksekarang+$qty;

            $update = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idproduk'");


            //hapus data
            $querydelete = mysqli_query($c,"delete from detailorder where iddetailorder='$iddp'");


        }

        $delete = mysqli_query($c,"delete from pesanan where idorder='$ido'");

        if($update && $querydelete && $delete){
            //Berhasil
            header('location:index.php');
        } else{
            //Gagal
            echo '
            <script>
            alert("Gagal");
            window.location.href="index.php"
            </script>
            ';
        }
    }

    //edit detail pesanan
    if(isset($_POST['editdetailpesanan'])){
        //Berhasil
        $jumlah = $_POST['jumlah'];
        $iddp = $_POST['iddp']; //id masuk
        $idpr = $_POST['idpr']; //id produk
        $idpr = $_POST['idpr'];
        $idp = $_POST['idp'];

        //Cari tahu qty sekarang berapa
        $caritahu = mysqli_query($c,"select * from detailorder where iddetailorder='$iddp'");
        $caritahu2 = mysqli_fetch_array($caritahu);
        $qtysekarang = $caritahu2['qty'];

        //cari tahu berapa stock yang ada sekarang
        $caristock = mysqli_query($c,"select * from produk where idproduk='$idpr'");
        $caristock2 = mysqli_fetch_array($caristock);
        $stocksekarang = $caristock2['stock'];

        if($jumlah >= $qtysekarang){
            //Kalau inputan user lebih besar dari pada qty yang tercatat sekaarng
            //Hitung selisih
            $selisih = $jumlah - $qtysekarang;
            $newstock = $stocksekarang-$selisih;

            $update1 = mysqli_query($c,"update detailorder set qty='$jumlah' where iddetailorder='$iddp'");
            $update2 = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idpr'");

            if($update1&&$update2){
                header('location:view.php?idp='.$idp);
            } else{
                echo'
                <script>
                alert("Gagal");
                window.location.href="view.php?idp='.$idp.'"
                </script>
                ';
            }
        } else {
            //kalau lebih kecil
            //selisih 
            $selisih = $qtysekarang - $jumlah;
            $newstock = $stocksekarang+$selisih;

            $update1 = mysqli_query($c,"update detailorder set qty='$jumlah' where iddetailorder='$iddp'");
            $update2 = mysqli_query($c,"update produk set stock='$newstock' where idproduk='$idpr'");

            if($update1&&$update2){
                header('location:view.php?idp='.$idp);
            } else{
                echo'
                <script>
                alert("Gagal");
                window.location.href="view.php?idp='.$idp.'"
                </script>
                ';
            }

        }

    }


?>