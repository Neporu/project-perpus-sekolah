<?php
require_once 'connect.php';
require_once 'header.php';
?>
<div class="container">
    <?php
    if (isset($_POST['addnew'])) {
        // Ambil data dari form
        $nik = $_POST['nik'];
        $nama = $_POST['nama'];
        $sandi = $_POST['sandi'];
        $alamat = $_POST['alamat'];
        $telp = $_POST['telp'];

        // Validasi input
        if (empty($nik) || empty($nama) || empty($sandi) || empty($alamat) || empty($telp)) {
            echo "<div class='alert alert-warning'>Seluruh data harus diisi!</div>";
        } else {
            // Query untuk memasukkan ke tabel petugas
            $sql = "INSERT INTO tbpetugas (nik, sandi, nama, alamat, telp) 
                    VALUES ('$nik', '$sandi', '$nama', '$alamat', '$telp')";

            if ($con->query($sql) === TRUE) {
                header("Location: petugas.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Data gagal ditambahkan: " . $con->error . "</div>";
            }
        }
    }
    ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-plus"></i>&nbsp;Tambah Petugas</h3>
                <form action="" method="POST">
                    <label for="nik">NIK</label>
                    <input type="text" id="nik" name="nik" class="form-control"><br>

                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" class="form-control"><br>

                    <label for="sandi">Sandi</label>
                    <input type="password" id="sandi" name="sandi" class="form-control"><br>

                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" class="form-control"><br>

                    <label for="telp">Telp</label>
                    <input type="text" id="telp" name="telp" class="form-control"><br>

                    <input type="submit" name="addnew" class="btn btn-success" value="Tambah">
                    <a href="petugas.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
