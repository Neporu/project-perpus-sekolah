<?php
require_once 'connect.php';
require_once 'header.php';
?>
<div class="container">
    <?php
    // Proses Insert
    if (isset($_POST['submit'])) {
        if (empty($_POST['idpemasok']) || empty($_POST['namapemasok']) || empty($_POST['alamat']) || empty($_POST['telp'])) {
            echo "<div class='alert alert-warning'>Semua field harus diisi.</div>";
        } else {
            $idpemasok = $_POST['idpemasok'];
            $namapemasok = $_POST['namapemasok'];
            $alamat = $_POST['alamat'];
            $telp = $_POST['telp'];

            // Cek apakah idpemasok sudah ada
            $check_sql = "SELECT * FROM tbpemasok WHERE idpemasok = ?";
            $check_stmt = $con->prepare($check_sql);
            $check_stmt->bind_param("s", $idpemasok);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                echo "<div class='alert alert-danger'>ID Pemasok sudah ada. Silakan gunakan ID lain.</div>";
            } else {
                $sql = "INSERT INTO tbpemasok (idpemasok, namapemasok, alamat, telp) VALUES (?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ssss", $idpemasok, $namapemasok, $alamat, $telp);

                if ($stmt->execute()) {
                    header("Location: pemasok.php"); 
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Data gagal ditambahkan: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
            $check_stmt->close();
        }
    }
    ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-plus"></i>&nbsp;Tambah Pemasok</h3>
                <form action="" method="POST">
                    <label for="idpemasok">ID Pemasok</label>
                    <input type="text" id="idpemasok" name="idpemasok" class="form-control" required><br>

                    <label for="namapemasok">Nama Pemasok</label>
                    <input type="text" id="namapemasok" name="namapemasok" class="form-control" required><br>

                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" class="form-control" required></textarea><br>

                    <label for="telp">Telp</label>
                    <input type="text" id="telp" name="telp" class="form-control" required><br>

                    <input type="submit" name="submit" class="btn btn-success" value="Tambah">
                    <a href="pemasok.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>