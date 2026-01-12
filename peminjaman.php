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
$sql = "SELECT * FROM tbpeminjaman ORDER BY tglpinjam";
$result = $con->query($sql);
// 2. Ambil semua detail pembelian dan kelompokkan berdasarkan notabeli
$detail_sql = "SELECT * FROM tbdetailpinjam";
$detail_result = $con->query($detail_sql);
$details = [];
if ($detail_result->num_rows > 0) {
    while($detail_row = $detail_result->fetch_assoc()) {
        $details[$detail_row['idpinjam']][] = $detail_row;
    }
}
?>
<h2>Data Peminjaman</h2>
<br>
<a href="peminjamaninsert.php" class="btn btn-info">Tambah Data</a>
<br><br>
<?php
if ($result->num_rows > 0) { ?>
    <table  class="table">
        <tr>
            <th>Id Peminjaman</th>
            <th>NIK</th>
            <th>NIM</th>
            <th>Tanggal Peminjaman</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { 
            $current_idpinjam = $row['idpinjam'];
        ?>
            <tr>
                <td><?php echo $current_idpinjam; ?></td>
                <td><?php echo $row['tglpinjam']; ?></td>
                <td><?php echo $row['nik']; ?></td>
                <td><?php echo $row['nim']; ?></td>
                <td><?php echo $row['tgljatuhtempo']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <button popovertarget="modal-<?php echo $current_idpinjam; ?>" class="btn btn-primary btn-sm">Lihat Detail</button>
                    <dialog style="padding: 20px; border-radius: 20px;" id="modal-<?php echo $current_idpinjam; ?>" popover>
                        <h4>Detail Peminjaman: <?php echo $current_idpinjam; ?></h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID Pinjam</th>
                                    <th>ID Buku</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($details[$current_idpinjam])): ?>
                                <?php foreach ($details[$current_idpinjam] as $detail_item): ?>
                                <tr>
                                    <td><?php echo $detail_item['idpinjam']; ?></td>
                                    <td><?php echo $detail_item['idbuku']; ?></td>
                                    <td><?php echo $detail_item['tglkembali']; ?></td>
                                    <td><?php echo $detail_item['status']; ?></td>
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