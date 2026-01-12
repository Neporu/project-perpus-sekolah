<?php
require_once 'connect.php';
require_once 'header.php';
?>
<div class="container">
    <?php
    if (isset($_POST['addnew'])) {
        // Ambil data dari form
        $nim = $_POST['nim'];
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $telp = $_POST['telp'];

        // Validasi input
        if (empty($nim) || empty($nama) || empty($alamat) || empty($telp)) {
            echo "<div class='alert alert-warning'>Seluruh data harus diisi!</div>";
        } else {
            // Query untuk memasukkan ke tabel mahasiswa
            $sql = "INSERT INTO tbmhs (nim, nama, alamat, telp) 
                    VALUES ('$nim', '$nama', '$alamat', '$telp')";

            if ($con->query($sql) === TRUE) {
                header("Location: mhs.php");
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
                <h3><i class="glyphicon glyphicon-plus"></i>&nbsp;Tambah Mahasiswa</h3>
                <form action="" method="POST">
                    <label for="nim">NIM</label>
                    <input type="text" id="nim" name="nim" class="form-control"><br>

                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" class="form-control"><br>

                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" class="form-control"><br>

                    <label for="telp">Telepon</label>
                    <input type="text" id="telp" name="telp" class="form-control"><br>

                    <input type="submit" name="addnew" class="btn btn-success" value="Tambah">
                    <a href="mhs.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
