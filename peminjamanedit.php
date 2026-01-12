<?php
require_once 'connect.php';
require_once 'header.php';
?>
<div class="container">
    <?php
    // Proses Update
    if (isset($_POST['update'])) {
        $idpinjam = $_POST['idpinjam'];
        $tgljatuhtempo = $_POST['tgljatuhtempo'];
        $nim = $_POST['nim'];
        $nik = $_POST['nik'];

        if (empty($tgljatuhtempo) || empty($nim) || empty($nik)) {
            echo "<div class='alert alert-warning'>Semua field harus diisi.</div>";
        } else {
            $sql = "UPDATE tbpeminjaman SET tgljatuhtempo = ?, nim = ?, nik = ? WHERE idpinjam = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssss", $tgljatuhtempo, $nim, $nik, $idpinjam);

            if ($stmt->execute()) {
                header("Location: peminjaman.php"); 
                exit();
            } else {
                echo "<div class='alert alert-danger'>Data gagal diubah: " . $con->error . "</div>";
            }
        }
    }

    // Ambil data berdasarkan ID dari URL
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $sql = "SELECT * FROM tbpeminjaman WHERE idpinjam = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        header("Location: peminjaman.php");
        exit();
    }
    $row = $result->fetch_assoc();

    // Ambil data untuk dropdown
    $mhs_sql = "SELECT nim, nama FROM tbmhs";
    $mhs_result = $con->query($mhs_sql);

    $petugas_sql = "SELECT nik, nama FROM tbpetugas";
    $petugas_result = $con->query($petugas_sql);
    ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-edit"></i>&nbsp;Ubah Peminjaman</h3>
                <form action="" method="POST">
                    <input type="hidden" name="idpinjam" value="<?php echo $row['idpinjam']; ?>">
                    
                    <label>ID Pinjam: <?php echo $row['idpinjam']; ?></label><br><br>
                    <label>Tanggal Pinjam: <?php echo $row['tglpinjam']; ?></label><br><br>

                    <label for="tgljatuhtempo">Tanggal Jatuh Tempo</label>
                    <input type="date" id="tgljatuhtempo" name="tgljatuhtempo" value="<?php echo $row['tgljatuhtempo']; ?>" class="form-control" required><br>

                    <label for="nim">Mahasiswa</label>
                    <select id="nim" name="nim" class="form-control" required>
                        <?php while($mhs_row = $mhs_result->fetch_assoc()) { 
                            $selected = ($mhs_row['nim'] == $row['nim']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $mhs_row['nim']; ?>" <?php echo $selected; ?>><?php echo $mhs_row['nim'] . " - " . $mhs_row['nama']; ?></option>
                        <?php } ?>
                    </select><br>

                    <label for="nik">Petugas</label>
                    <select id="nik" name="nik" class="form-control" required>
                        <?php while($petugas_row = $petugas_result->fetch_assoc()) { 
                            $selected = ($petugas_row['nik'] == $row['nik']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $petugas_row['nik']; ?>" <?php echo $selected; ?>><?php echo $petugas_row['nama']; ?></option>
                        <?php } ?>
                    </select><br>
                    
                    <input type="submit" name="update" class="btn btn-success" value="Update">
                    <a href="peminjaman.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
