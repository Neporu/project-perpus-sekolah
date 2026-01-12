<?php
require_once 'connect.php';
require_once 'header.php';
?>
<div class="container">
    <?php
    if (isset($_POST['addnew'])) {
        // Ambil data dari form
        $idkategori = $_POST['idkategori'];
        $namakategori = $_POST['namakategori'];

        // Validasi input
        if (empty($idkategori) || empty($namakategori)) {
            echo "<div class='alert alert-warning'>Seluruh data harus diisi!</div>";
        } else {
            // Query untuk memasukkan ke tabel kategori
            $sql = "INSERT INTO tbkategori (idkategori, namakategori) 
                    VALUES ('$idkategori', '$namakategori')";

            if ($con->query($sql) === TRUE) {
                header("Location: kategori.php");
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
                <h3><i class="glyphicon glyphicon-plus"></i>&nbsp;Tambah Kategori</h3>
                <form action="" method="POST">
                    <label for="idkategori">Id Kategori</label>
                    <input type="text" id="idkategori" name="idkategori" class="form-control"><br>

                    <label for="namakategori">Nama Kategori</label>
                    <input type="text" id="namakategori" name="namakategori" class="form-control"><br>

                    <input type="submit" name="addnew" class="btn btn-success" value="Tambah">
                    <a href="kategori.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
