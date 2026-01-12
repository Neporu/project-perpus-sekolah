<?php
require_once 'connect.php';
require_once 'header.php';
?>
<div class="container">
    <?php
    // Proses Update
    if (isset($_POST['update'])) {
        if (empty($_POST['nim']) || empty($_POST['nama']) || empty($_POST['alamat']) || empty($_POST['telp'])) {
            echo "<div class='alert alert-warning'>Semua field harus diisi.</div>";
        } else {
            $original_nim = $_POST['original_nim'];
            $nim = $_POST['nim'];
            $nama = $_POST['nama'];
            $alamat = $_POST['alamat'];
            $telp = $_POST['telp'];

            $sql = "UPDATE tbmhs SET nim = '$nim', nama = '$nama', alamat = '$alamat', telp = '$telp'
                    WHERE nim = '$original_nim'";

            if ($con->query($sql) === TRUE) {
                header("Location: mhs.php"); 
                exit();
            } else {
                echo "<div class='alert alert-danger'>Data gagal diubah: " . $con->error . "</div>";
            }
        }
    }

    // Ambil data berdasarkan ID dari URL
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sql = "SELECT * FROM tbmhs WHERE nim = '$id'";
    $result = $con->query($sql);

    if ($result->num_rows < 1) {
        header("Location: mhs.php");
        exit();
    }

    $row = $result->fetch_assoc();
    ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-edit"></i>&nbsp;Ubah Mahasiswa</h3>
                <form action="" method="POST">
                    <input type="hidden" name="original_nim" value="<?php echo $row['nim']; ?>">
                    <label for="nim">NIM</label>
                    <input type="text" id="nim" name="nim" value="<?php echo $row['nim']; ?>" class="form-control"><br>
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" value="<?php echo $row['nama']; ?>" class="form-control"><br>
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" value="<?php echo $row['alamat']; ?>" class="form-control"><br>
                    <label for="telp">Telp</label>
                    <input type="text" id="telp" name="telp" value="<?php echo $row['telp']; ?>" class="form-control"><br>
                    
                    <input type="submit" name="update" class="btn btn-success" value="Update">
                    <a href="mhs.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>