<?php
require_once 'connect.php';
require_once 'header.php';
?>
<div class="container">
    <?php
    // Proses Update
    if (isset($_POST['update'])) {
        if (empty($_POST['idkategori']) || empty($_POST['namakategori'])) {
            echo "<div class='alert alert-warning'>Semua field harus diisi.</div>";
        } else {
            $idkategori = $_POST['idkategori'];
            $namakategori = $_POST['namakategori'];

            $sql = "UPDATE tbkategori SET namakategori = '$namakategori'
                    WHERE idkategori = '$idkategori'";

            if ($con->query($sql) === TRUE) {
                header("Location: kategori.php"); 
                exit();
            } else {
                echo "<div class='alert alert-danger'>Data gagal diubah: " . $con->error . "</div>";
            }
        }
    }

    // Ambil data berdasarkan ID dari URL
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sql = "SELECT * FROM tbkategori WHERE idkategori = '$id'";
    $result = $con->query($sql);

    if ($result->num_rows < 1) {
        header("Location: kategori.php");
        exit();
    }

    $row = $result->fetch_assoc();
    ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-edit"></i>&nbsp;Ubah Kategori</h3>
                <form action="" method="POST">
                    <label for="idkategori">Id Kategori</label>
                    <input type="text" id="idkategori" name="idkategori" value="<?php echo $row['idkategori']; ?>" class="form-control" readonly><br>

                    <label for="nama_kategori">Nama Kategori</label>
                    <input type="text" id="namakategori" name="namakategori" value="<?php echo $row['namakategori']; ?>" class="form-control"><br>

                    <input type="submit" name="update" class="btn btn-success" value="Update">
                    <a href="kategori.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
