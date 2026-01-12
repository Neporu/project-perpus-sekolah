<?php
require_once 'connect.php';
require_once 'header.php';
?>
<div class="container">
    <?php
    // Proses Update
    if (isset($_POST['update'])) {
        if (empty($_POST['nik']) || empty($_POST['nama']) || empty($_POST['sandi']) || empty($_POST['alamat']) || empty($_POST['telp'])) {
            echo "<div class='alert alert-warning'>Semua field harus diisi.</div>";
        } else {
            $nik = $_POST['nik'];
            $nama = $_POST['nama'];
            $sandi = $_POST['sandi'];
            $alamat = $_POST['alamat'];
            $telp = $_POST['telp'];

            $sql = "UPDATE tbpetugas SET nama = '$nama', sandi = '$sandi', alamat = '$alamat', telp = '$telp'
                    WHERE nik = '$nik'";

            if ($con->query($sql) === TRUE) {
                header("Location: petugas.php"); 
                exit();
            } else {
                echo "<div class='alert alert-danger'>Data gagal diubah: " . $con->error . "</div>";
            }
        }
    }

    // Ambil data berdasarkan ID dari URL
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sql = "SELECT * FROM tbpetugas WHERE nik = '$id'";
    $result = $con->query($sql);

    if ($result->num_rows < 1) {
        header("Location: petugas.php");
        exit();
    }

    $row = $result->fetch_assoc();
    ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-edit"></i>&nbsp;Ubah Petugas</h3>
                <form action="" method="POST">
                    <label for="nik">NIK</label>
                    <input type="text" id="nik" name="nik" value="<?php echo $row['nik']; ?>" class="form-control" readonly><br>

                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" value="<?php echo $row['nama']; ?>" class="form-control"><br>
                    <label for="sandi">Sandi</label>
                    <input type="text" id="sandi" name="sandi" value="<?php echo $row['sandi']; ?>" class="form-control"><br>
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" value="<?php echo $row['alamat']; ?>" class="form-control"><br>
                    <label for="telp">Telepon</label>
                    <input type="text" id="telp" name="telp" value="<?php echo $row['telp']; ?>" class="form-control"><br>

                    <input type="submit" name="update" class="btn btn-success" value="Update">
                    <a href="petugas.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
