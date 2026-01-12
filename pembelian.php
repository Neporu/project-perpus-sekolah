<?php 
require_once 'connect.php';
require_once 'header.php';
echo "<div class='container'>";
// Proses penghapusan yang diperbaiki dengan penyesuaian stok
if (isset($_POST['delete'])) {
    $notabeli = $_POST['id']; 

    $con->begin_transaction();
    try {
        // 1. Ambil detail item yang akan dihapus untuk tahu buku dan jumlahnya
        $stmt_get = $con->prepare("SELECT idbuku, jml FROM tbdetailbeli WHERE notabeli = ?");
        $stmt_get->bind_param("s", $notabeli);
        $stmt_get->execute();
        $result = $stmt_get->get_result();
        
        if ($result->num_rows > 0) {
            $detail = $result->fetch_assoc();
            $idbuku = $detail['idbuku'];
            $jml_dihapus = (int)$detail['jml'];

            // 2. Kurangi stok di tbbuku
            $stmt_stock = $con->prepare("UPDATE tbbuku SET stok = stok - ? WHERE idbuku = ?");
            $stmt_stock->bind_param("is", $jml_dihapus, $idbuku);
            $stmt_stock->execute();
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
$sql = "SELECT * FROM tbpembelian ORDER BY tgl DESC";
$result = $con->query($sql);
// 2. Ambil semua detail pembelian dan kelompokkan berdasarkan notabeli
$detail_sql = "SELECT * FROM tbdetailbeli";
$detail_result = $con->query($detail_sql);
$details = [];
if ($detail_result->num_rows > 0) {
    while($detail_row = $detail_result->fetch_assoc()) {
        $details[$detail_row['notabeli']][] = $detail_row;
    }
}
?>
<h2>Data Pembelian</h2>
<br>
<a href="pembelian-insert.php" class="btn btn-info">Tambah Data</a>
<br><br>
<?php
if ($result->num_rows > 0) { ?>
    <table  class="table">
        <tr>
            <th>Nota</th>
            <th>Tanggal</th>
            <th>NIK</th>
            <th>Id Pemasok</th>
            <th>Total Beli</th>
            <th>Aksi</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { 
            $current_nota = $row['notabeli'];
        ?>
            <tr>
                <td><?php echo $current_nota; ?></td>
                <td><?php echo $row['tgl']; ?></td>
                <td><?php echo $row['nik']; ?></td>
                <td><?php echo $row['idpemasok']; ?></td>
                <td>Rp <?php echo number_format($row['totalbeli'], 0, ',', '.'); ?></td>
                <td>
                    <button popovertarget="modal-<?php echo $current_nota; ?>" class="btn btn-primary btn-sm">Lihat Detail</button>
                    <dialog style="padding: 20px; border-radius: 20px;" id="modal-<?php echo $current_nota; ?>" popover>
                        <h4>Detail Pembelian: <?php echo $current_nota; ?></h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Id Buku</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($details[$current_nota])): ?>
                                <?php foreach ($details[$current_nota] as $detail_item): ?>
                                <tr>
                                    <td><?php echo $detail_item['idbuku']; ?></td>
                                    <td><?php echo $detail_item['jml']; ?></td>
                                    <td>Rp <?php echo number_format($detail_item['hargabeli'], 0, ',', '.'); ?></td>
                                    <td>Rp <?php echo number_format($detail_item['subtotal'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada detail untuk pembelian ini.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </dialog>
                    <a href="pembelian-edit.php?id=<?php echo $current_nota; ?>" class="btn btn-info btn-sm">Ubah</a>
                    <form method="post" action="" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                        <input type="hidden" name="id" value="<?php echo $current_nota; ?>">
                        <input type="submit" name="delete" value="Hapus" class="btn btn-danger btn-sm">
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php
} else {
    echo "<br><br><div class='alert alert-warning'>Tidak ada data Pembelian.</div>";
}
?>
</div>