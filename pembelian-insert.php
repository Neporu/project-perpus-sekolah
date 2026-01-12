<?php
require_once 'connect.php';
require_once 'header.php';
$petugas = mysqli_query($con, "SELECT * FROM tbpetugas");
$pemasok = mysqli_query($con, "SELECT * FROM tbpemasok");
$buku = mysqli_query($con, "SELECT * FROM tbbuku");
?>
<div class="container">
    <?php
    if (isset($_POST['addnew'])) {
        // Ambil data dari form
        $notabeli = $_POST['notabeli'];
        $tgl = $_POST['tgl'];
        $nik = $_POST['nik'];
        $idpemasok = $_POST['idpemasok'];
        $idbuku = $_POST['idbuku'];
        $jml = (int)$_POST['jml'];
        $hargabeli = (int)$_POST['hargabeli'];
        $subtotal = $jml * $hargabeli;
        $totalbeli = $subtotal;

        // Validasi
        if (empty($notabeli) || empty($tgl) || empty($nik) || empty($idpemasok) || empty($idbuku) || $jml <= 0) {
            echo "<div class='alert alert-warning'>Seluruh data harus diisi dan jumlah harus lebih dari 0!</div>";
        } else {
            // Mulai transaksi untuk memastikan semua query berhasil
            $con->begin_transaction();
            try {
                // Query 1: Insert ke tbpembelian
                $stmt1 = $con->prepare("INSERT INTO tbpembelian (notabeli, tgl, nik, idpemasok, totalbeli) VALUES (?, ?, ?, ?, ?)");
                $stmt1->bind_param("ssssi", $notabeli, $tgl, $nik, $idpemasok, $totalbeli);
                $stmt1->execute();

                // Query 2: Insert ke tbdetailbeli
                $stmt2 = $con->prepare("INSERT INTO tbdetailbeli (notabeli, idbuku, jml, hargabeli, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt2->bind_param("ssiii", $notabeli, $idbuku, $jml, $hargabeli, $subtotal);
                $stmt2->execute();

                // Query 3: Update stok di tbbuku
                $stmt3 = $con->prepare("UPDATE tbbuku SET stok = stok + ? WHERE idbuku = ?");
                $stmt3->bind_param("is", $jml, $idbuku);
                $stmt3->execute();

                // Jika semua berhasil, simpan perubahan
                $con->commit();
                
                header("Location: pembelian.php");
                exit();

            } catch (mysqli_sql_exception $exception) {
                // Jika ada yang gagal, batalkan semua perubahan
                $con->rollback();
                echo "<div class='alert alert-danger'>Data gagal ditambahkan: " . $exception->getMessage() . "</div>";
            }
        }
    }
    ?>
</div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-plus"></i>&nbsp;Tambah Pembelian</h3>
                <form action="" method="POST">
                    <label for="notabeli">Nota Pembelian</label>
                    <input type="text" id="notabeli" name="notabeli" class="form-control" required><br>  
                    
                    <label for="tgl">Tanggal</label>
                    <input type="date" id="tgl" name="tgl" class="form-control" required><br>  
                    
                    <label for="nik">Petugas</label>
                    <select name="nik" id="nik" class="form-control" required>
                        <option value="" selected disabled>-- Pilih Petugas --</option>
                        <?php mysqli_data_seek($petugas, 0); ?>
                        <?php while($row = $petugas->fetch_assoc()): ?>
                        <option value="<?php echo $row['nik']; ?>"><?php echo $row['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <br>

                    <label for="idpemasok">Pemasok</label>
                    <select name="idpemasok" id="idpemasok" class="form-control" required>
                        <option value="" selected disabled>-- Pilih Pemasok --</option>
                        <?php mysqli_data_seek($pemasok, 0); ?>
                        <?php while($row = $pemasok->fetch_assoc()): ?>
                        <option value="<?php echo $row['idpemasok']; ?>"><?php echo $row['namapemasok']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <br>
                    
                    <label for="idbuku">Buku</label>
                    <select name="idbuku" id="idbuku" class="form-control" required>
                        <option value="" selected disabled>-- Pilih Buku --</option>
                        <?php mysqli_data_seek($buku, 0); ?>
                        <?php while($row = $buku->fetch_assoc()): ?>
                        <option value="<?php echo $row['idbuku']; ?>"><?php echo $row['judul']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <br>

                    <label for="jml">Jumlah</label>
                    <input type="number" name="jml" id="jml" class="form-control" min="1" required><br>

                    <label for="hargabeli">Harga Beli</label>
                    <input type="number" name="hargabeli" id="hargabeli" class="form-control" min="0" required><br>

                    <hr>
                    <label for="total-belanja">Total Belanja</label>
                    <input type="number" name="totalbeli" id="total-belanja" class="form-control" readonly><br>

                    <input type="submit" name="addnew" class="btn btn-success" value="Tambah">
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

    $('#jml, #hargabeli').on('input', calculateTotal);
});
</script>