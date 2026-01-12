<?php
require_once 'connect.php';
require_once 'header.php';

// Ambil data untuk dropdown
$mhs_sql = "SELECT nim, nama FROM tbmhs";
$mhs_result = $con->query($mhs_sql);

$petugas_sql = "SELECT nik, nama FROM tbpetugas";
$petugas_result = $con->query($petugas_sql);

$buku_sql = "SELECT idbuku, judul FROM tbbuku WHERE stok > 0";
$buku_result = $con->query($buku_sql);

?>
<div class="container">
    <?php
    // Proses Insert
    if (isset($_POST['submit'])) {
        $idpinjam = $_POST['idpinjam'];
        $tglpinjam = $_POST['tglpinjam'];
        $nim = $_POST['nim'];
        $nik = $_POST['nik'];
        $idbuku_list = $_POST['idbuku'];
        
        // Jatuh tempo 7 hari dari tgl pinjam
        $tgljatuhtempo = date('Y-m-d', strtotime($tglpinjam . ' + 7 days'));
        
        if (empty($idpinjam) || empty($tglpinjam) || empty($nim) || empty($nik) || empty($idbuku_list)) {
            echo "<div class='alert alert-warning'>Semua field harus diisi.</div>";
        } else {
            $con->begin_transaction();
            try {
                // 1. Insert ke tbpeminjaman
                $stmt_main = $con->prepare("INSERT INTO tbpeminjaman (idpinjam, nik, nim, tglpinjam, tgljatuhtempo, status) VALUES (?, ?, ?, ?, ?, 'aktif')");
                $stmt_main->bind_param("sssss", $idpinjam, $nik, $nim, $tglpinjam, $tgljatuhtempo);
                $stmt_main->execute();

                // 2. Insert ke tbdetailpinjam dan kurangi stok
                $stmt_detail = $con->prepare("INSERT INTO tbdetailpinjam (idpinjam, idbuku, status) VALUES (?, ?, 'dipinjam')");
                $stmt_stock = $con->prepare("UPDATE tbbuku SET stok = stok - 1 WHERE idbuku = ?");

                foreach ($idbuku_list as $idbuku) {
                    // Insert detail
                    $stmt_detail->bind_param("ss", $idpinjam, $idbuku);
                    $stmt_detail->execute();
                    // Kurangi stok
                    $stmt_stock->bind_param("s", $idbuku);
                    $stmt_stock->execute();
                }

                $con->commit();
                header("Location: peminjaman.php");
                exit();
            } catch (mysqli_sql_exception $exception) {
                $con->rollback();
                echo "<div class='alert alert-danger'>Data gagal ditambahkan: " . $exception->getMessage() . "</div>";
            }
        }
    }
    ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-plus"></i>&nbsp;Tambah Peminjaman</h3>
                <form action="" method="POST">
                    <label for="idpinjam">ID Pinjam</label>
                    <input type="text" id="idpinjam" name="idpinjam" class="form-control" required><br>
                    
                    <label for="tglpinjam">Tanggal Pinjam</label>
                    <input type="date" id="tglpinjam" name="tglpinjam" class="form-control" value="<?php echo date('Y-m-d'); ?>" required><br>

                    <label for="nim">Mahasiswa</label>
                    <select id="nim" name="nim" class="form-control" required>
                        <option value="">-- Pilih Mahasiswa --</option>
                        <?php while($row = $mhs_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['nim']; ?>"><?php echo $row['nim'] . " - " . $row['nama']; ?></option>
                        <?php } ?>
                    </select><br>

                    <label for="nik">Petugas</label>
                    <select id="nik" name="nik" class="form-control" required>
                        <option value="">-- Pilih Petugas --</option>
                        <?php while($row = $petugas_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['nik']; ?>"><?php echo $row['nama']; ?></option>
                        <?php } ?>
                    </select><br>

                    <label for="idbuku">Buku yang Dipinjam (Bisa pilih lebih dari satu)</label>
                    <select id="idbuku" name="idbuku[]" class="form-control" multiple required>
                        <?php while($row = $buku_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['idbuku']; ?>"><?php echo $row['judul']; ?></option>
                        <?php } ?>
                    </select><br>
                    
                    <input type="submit" name="submit" class="btn btn-success" value="Simpan">
                    <a href="peminjaman.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
