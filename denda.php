<?php 
require_once 'connect.php';
require_once 'header.php';
echo "<div class='container'>";
// Proses penghapusan yang diperbaiki dengan penyesuaian stok
if (isset($_POST['delete'])) {
    $iddenda = $_POST['iddenda']; 

    $con->begin_transaction();
    try {        
        if ($result->num_rows > 0) {
            $detail = $result->fetch_assoc();
            $idbuku = $detail['idbuku'];
            $jml_dihapus = (int)$detail['jml'];
        }
        // Jika tidak ada detail, kita tetap lanjutkan proses hapus data pembeliannya

        // 3. Hapus dari tabel detail
        $stmt_del_detail = $con->prepare("DELETE FROM tbdetailbeli WHERE notabeli = ?");
        $stmt_del_detail->bind_param("s", $notabeli);
        $stmt_del_detail->execute();

        // 4. Hapus dari tabel utama
        $stmt_del_main = $con->prepare("DELETE FROM tbpembelian WHERE notabeli = ?");
        $stmt_del_main->bind_param("s", $notabeli);
        $stmt_del_main->execute();

        // Jika semua berhasil, commit
        $con->commit();
        header("Location: pembelian.php");
        exit();

    } catch (mysqli_sql_exception $exception) {
        // Jika ada yang gagal, rollback
        $con->rollback();
        echo "<div class='alert alert-danger'>Gagal menghapus data: " . $exception->getMessage() . "</div>";
    }
}

// Ambil semua data pembelian utama
$sql = "SELECT * FROM tbdenda ORDER BY tglbuat";
$result = $con->query($sql);
?>
<h2>Data Denda</h2>
<br>
<a href="denda-insert.php" class="btn btn-info">Tambah Data</a>
<br><br>
<?php
if ($result->num_rows > 0) { ?>
    <table  class="table">
        <tr>
            <th>Id Denda</th>
            <th>Id Pinjam</th>
            <th>Tanggal Denda dibuat</th>
            <th>Tanggal Bayar Denda</th>
            <th>Total Denda</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) {         ?>
            <tr>
                <td><?php echo $row['iddenda']; ?></td>
                <td><?php echo $row['idpinjam']; ?></td>
                <td><?php echo $row['tglbuat']; ?></td>
                <td><?php echo $row['tglbayar']; ?></td>
                <td>Rp <?php echo number_format($row['totaldenda'], 0, ',', '.'); ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <a href="denda-edit.php?id=<?php echo $row['iddenda']; ?>" class="btn btn-info btn-sm">Ubah</a>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php
} else {
    echo "<br><br><div class='alert alert-warning'>Tidak ada data Denda. Mohon tambahkan data Denda secara manual.</div>";
}
?>
</div>