<?php
require_once 'connect.php';
require_once 'header.php';

$petugas = mysqli_query($con, "SELECT * FROM tbpetugas");
$pemasok = mysqli_query($con, "SELECT * FROM tbpemasok");
$buku = mysqli_query($con, "SELECT * FROM tbbuku");

$id_from_url = isset($_GET['id']) ? $_GET['id'] : '';

if (isset($_POST['update'])) {
    // Ambil data baru dari form
    $notabeli = $_POST['notabeli'];
    $tgl = $_POST['tgl'];
    $nik = $_POST['nik'];
    $idpemasok = $_POST['idpemasok'];
    $new_idbuku = $_POST['idbuku'];
    $new_jml = (int)$_POST['jml'];
    $new_hargabeli = (int)$_POST['hargabeli'];
    $new_subtotal = $new_jml * $new_hargabeli;
    $new_totalbeli = $new_subtotal;

    if (empty($notabeli) || empty($tgl) || empty($nik) || empty($idpemasok) || empty($new_idbuku) || $new_jml <= 0) {
        echo "<div class='alert alert-warning'>Seluruh data harus diisi!</div>";
    } else {
        $con->begin_transaction();
        try {
            // 1. Ambil data lama untuk perbandingan stok
            $stmt_old = $con->prepare("SELECT idbuku, jml FROM tbdetailbeli WHERE notabeli = ?");
            $stmt_old->bind_param("s", $notabeli);
            $stmt_old->execute();
            $old_result = $stmt_old->get_result();
            if ($old_result->num_rows === 0) throw new Exception("Data pembelian asli tidak ditemukan.");
            $old_data = $old_result->fetch_assoc();
            $old_idbuku = $old_data['idbuku'];
            $old_jml = (int)$old_data['jml'];

            // 2. Update tabel tbpembelian
            $stmt1 = $con->prepare("UPDATE tbpembelian SET tgl = ?, nik = ?, idpemasok = ?, totalbeli = ? WHERE notabeli = ?");
            $stmt1->bind_param("sssis", $tgl, $nik, $idpemasok, $new_totalbeli, $notabeli);
            $stmt1->execute();

            // 3. Update tabel tbdetailbeli
            $stmt2 = $con->prepare("UPDATE tbdetailbeli SET idbuku = ?, jml = ?, hargabeli = ?, subtotal = ? WHERE notabeli = ?");
            $stmt2->bind_param("siiis", $new_idbuku, $new_jml, $new_hargabeli, $new_subtotal, $notabeli);
            $stmt2->execute();

            // 4. Sesuaikan stok buku
            // Kembalikan stok buku lama
            $stmt_stock_old = $con->prepare("UPDATE tbbuku SET stok = stok - ? WHERE idbuku = ?");
            $stmt_stock_old->bind_param("is", $old_jml, $old_idbuku);
            $stmt_stock_old->execute();
            
            // Tambah stok buku baru
            $stmt_stock_new = $con->prepare("UPDATE tbbuku SET stok = stok + ? WHERE idbuku = ?");
            $stmt_stock_new->bind_param("is", $new_jml, $new_idbuku);
            $stmt_stock_new->execute();

            $con->commit();
            header("Location: pembelian.php");
            exit();

        } catch (mysqli_sql_exception $exception) {
            $con->rollback();
            echo "<div class='alert alert-danger'>Data gagal diupdate: " . $exception->getMessage() . "</div>";
        }
    }
}

// Ambil data yang akan diedit (juga menggunakan prepare)
$stmt_get = $con->prepare("SELECT p.notabeli, p.tgl, p.nik, p.idpemasok, d.idbuku, d.jml, d.hargabeli FROM tbpembelian p JOIN tbdetailbeli d ON p.notabeli = d.notabeli WHERE p.notabeli = ?");
$stmt_get->bind_param("s", $id_from_url);
$stmt_get->execute();
$result = $stmt_get->get_result();
if ($result->num_rows < 1) {
    header("Location: pembelian.php");
    exit();
}
$data = $result->fetch_assoc();
?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-edit"></i>&nbsp;Ubah Pembelian</h3>
                <form action="" method="POST">
                    <label for="notabeli">Nota Pembelian</label>
                    <input type="text" id="notabeli" name="notabeli" class="form-control" value="<?php echo htmlspecialchars($data['notabeli']); ?>" readonly><br>  
                    
                    <label for="tgl">Tanggal</label>
                    <input type="date" id="tgl" name="tgl" class="form-control" value="<?php echo htmlspecialchars($data['tgl']); ?>" required><br>  
                    
                    <label for="nik">Petugas</label>
                    <select name="nik" id="nik" class="form-control" required>
                        <option value="" disabled>-- Pilih Petugas --</option>
                        <?php mysqli_data_seek($petugas, 0); ?>
                        <?php while($row = $petugas->fetch_assoc()): ?>
                        <option value="<?php echo $row['nik']; ?>" <?php echo ($data['nik'] == $row['nik']) ? 'selected' : ''; ?>>
                            <?php echo $row['nama']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                    <br>

                    <label for="idpemasok">Pemasok</label>
                    <select name="idpemasok" id="idpemasok" class="form-control" required>
                        <option value="" disabled>-- Pilih Pemasok --</option>
                        <?php mysqli_data_seek($pemasok, 0); ?>
                        <?php while($row = $pemasok->fetch_assoc()): ?>
                        <option value="<?php echo $row['idpemasok']; ?>" <?php echo ($data['idpemasok'] == $row['idpemasok']) ? 'selected' : ''; ?>>
                            <?php echo $row['namapemasok']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                    <br>
                    
                    <label for="idbuku">Buku</label>
                    <select name="idbuku" id="idbuku" class="form-control" required>
                        <option value="" disabled>-- Pilih Buku --</option>
                        <?php mysqli_data_seek($buku, 0); ?>
                        <?php while($row = $buku->fetch_assoc()): ?>
                        <option value="<?php echo $row['idbuku']; ?>" <?php echo ($data['idbuku'] == $row['idbuku']) ? 'selected' : ''; ?>>
                            <?php echo $row['judul']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                    <br>

                    <label for="jml">Jumlah</label>
                    <input type="number" name="jml" id="jml" class="form-control" min="1" value="<?php echo htmlspecialchars($data['jml']); ?>" required><br>

                    <label for="hargabeli">Harga Beli</label>
                    <input type="number" name="hargabeli" id="hargabeli" class="form-control" min="0" value="<?php echo htmlspecialchars($data['hargabeli']); ?>" required><br>

                    <hr>
                    <label for="total-belanja">Total Belanja</label>
                    <input type="number" name="totalbeli" id="total-belanja" class="form-control" readonly><br>

                    <input type="submit" name="update" class="btn btn-success" value="Update">
                    <a href="pembelian.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function calculateTotal() {
        let jumlah = parseInt($('#jml').val()) || 0;
        let harga = parseInt($('#hargabeli').val()) || 0;
        let subtotal = jumlah * harga;
        $('#total-belanja').val(subtotal);
    }

    // Hitung saat pertama kali halaman dimuat
    calculateTotal(); 

    // Hitung ulang setiap kali ada input
    $('#jml, #hargabeli').on('input', calculateTotal);
});
</script>