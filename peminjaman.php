<?php 
require_once 'connect.php';
require_once 'header.php';
echo "<div class='container'>";

// Proses untuk menyelesaikan peminjaman
if (isset($_POST['selesaikan'])) {
    $idpinjam = $_POST['idpinjam'];
    $tglkembali = date("Y-m-d");

    $con->begin_transaction();
    try {
        // 1. Update status peminjaman utama
        $stmt_update_main = $con->prepare("UPDATE tbpeminjaman SET status = 'selesai' WHERE idpinjam = ?");
        $stmt_update_main->bind_param("s", $idpinjam);
        $stmt_update_main->execute();

        // 2. Update detail peminjaman
        $stmt_update_detail = $con->prepare("UPDATE tbdetailpinjam SET tglkembali = ?, status = 'kembali' WHERE idpinjam = ?");
        $stmt_update_detail->bind_param("ss", $tglkembali, $idpinjam);
        $stmt_update_detail->execute();

        // 3. Kembalikan stok buku
        $stmt_get_details = $con->prepare("SELECT idbuku FROM tbdetailpinjam WHERE idpinjam = ?");
        $stmt_get_details->bind_param("s", $idpinjam);
        $stmt_get_details->execute();
        $result_details = $stmt_get_details->get_result();
        while ($detail = $result_details->fetch_assoc()) {
            $idbuku = $detail['idbuku'];
            $stmt_stock = $con->prepare("UPDATE tbbuku SET stok = stok + 1 WHERE idbuku = ?");
            $stmt_stock->bind_param("s", $idbuku);
            $stmt_stock->execute();
        }

        $con->commit();
        header("Location: peminjaman.php");
        exit();
    } catch (mysqli_sql_exception $exception) {
        $con->rollback();
        echo "<div class='alert alert-danger'>Gagal menyelesaikan peminjaman: " . $exception->getMessage() . "</div>";
    }
}

// Proses untuk menandai buku hilang
if (isset($_POST['hilang'])) {
    $idpinjam = $_POST['idpinjam'];

    $con->begin_transaction();
    try {
        // 1. Update status peminjaman utama menjadi 'selesai'
        $stmt_update_main = $con->prepare("UPDATE tbpeminjaman SET status = 'selesai' WHERE idpinjam = ?");
        $stmt_update_main->bind_param("s", $idpinjam);
        $stmt_update_main->execute();

        // 2. Update status detail peminjaman menjadi 'hilang'
        $stmt_update_detail = $con->prepare("UPDATE tbdetailpinjam SET status = 'hilang' WHERE idpinjam = ?");
        $stmt_update_detail->bind_param("s", $idpinjam);
        $stmt_update_detail->execute();

        // Stok tidak dikembalikan karena buku hilang

        $con->commit();
        header("Location: peminjaman.php");
        exit();
    } catch (mysqli_sql_exception $exception) {
        $con->rollback();
        echo "<div class='alert alert-danger'>Gagal menandai hilang: " . $exception->getMessage() . "</div>";
    }
}

// Proses untuk menghapus peminjaman
if (isset($_POST['delete'])) {
    $idpinjam = $_POST['idpinjam'];

    $con->begin_transaction();
    try {
        // 1. Ambil detail buku yang dipinjam untuk mengembalikan stok jika perlu
        $stmt_get_details = $con->prepare("SELECT idbuku FROM tbdetailpinjam WHERE idpinjam = ? AND status = 'dipinjam'");
        $stmt_get_details->bind_param("s", $idpinjam);
        $stmt_get_details->execute();
        $result_details = $stmt_get_details->get_result();

        // 2. Kembalikan stok untuk setiap buku yang statusnya masih 'dipinjam'
        $stmt_stock = $con->prepare("UPDATE tbbuku SET stok = stok + 1 WHERE idbuku = ?");
        while ($detail = $result_details->fetch_assoc()) {
            $idbuku = $detail['idbuku'];
            $stmt_stock->bind_param("s", $idbuku);
            $stmt_stock->execute();
        }

        // 3. Hapus dari tabel detail peminjaman
        $stmt_del_detail = $con->prepare("DELETE FROM tbdetailpinjam WHERE idpinjam = ?");
        $stmt_del_detail->bind_param("s", $idpinjam);
        $stmt_del_detail->execute();

        // 4. Hapus dari tabel peminjaman utama
        $stmt_del_main = $con->prepare("DELETE FROM tbpeminjaman WHERE idpinjam = ?");
        $stmt_del_main->bind_param("s", $idpinjam);
        $stmt_del_main->execute();

        $con->commit();
        header("Location: peminjaman.php");
        exit();
    } catch (mysqli_sql_exception $exception) {
        $con->rollback();
        echo "<div class='alert alert-danger'>Gagal menghapus peminjaman: " . $exception->getMessage() . "</div>";
    }
}

// Ambil semua data peminjaman utama
$sql = "SELECT p.*, m.nama as nama_mhs, pt.nama as nama_petugas FROM tbpeminjaman p 
        JOIN tbmhs m ON p.nim = m.nim
        JOIN tbpetugas pt ON p.nik = pt.nik
        ORDER BY p.tglpinjam DESC";
$result = $con->query($sql);

// Ambil semua detail peminjaman
$detail_sql = "SELECT dp.*, b.judul FROM tbdetailpinjam dp JOIN tbbuku b ON dp.idbuku = b.idbuku";
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
<a href="peminjamaninsert.php" class="btn btn-info">Tambah Peminjaman</a>
<br><br>
<?php
if ($result->num_rows > 0) { ?>
    <table class="table table-bordered table-striped">
        <tr>
            <th>ID Pinjam</th>
            <th>Tgl Pinjam</th>
            <th>Jatuh Tempo</th>
            <th>Nama Mahasiswa</th>
            <th>Nama Petugas</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { 
            $current_pinjam = $row['idpinjam'];
        ?>
            <tr>
                <td><?php echo $current_pinjam; ?></td>
                <td><?php echo $row['tglpinjam']; ?></td>
                <td><?php echo $row['tgljatuhtempo']; ?></td>
                <td><?php echo $row['nama_mhs']; ?></td>
                <td><?php echo $row['nama_petugas']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <button popovertarget="modal-<?php echo $current_pinjam; ?>" class="btn btn-primary btn-sm">Lihat Detail</button>
                    <dialog style="padding: 20px; border-radius: 20px;" id="modal-<?php echo $current_pinjam; ?>" popover>
                        <h4>Detail Peminjaman: <?php echo $current_pinjam; ?></h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Judul Buku</th>
                                    <th>Tgl Kembali</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($details[$current_pinjam])): ?>
                                <?php foreach ($details[$current_pinjam] as $detail_item): ?>
                                <tr>
                                    <td><?php echo $detail_item['judul']; ?></td>
                                    <td><?php echo $detail_item['tglkembali']; ?></td>
                                    <td><?php echo $detail_item['status']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada detail untuk peminjaman ini.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </dialog>
                    
                    <form method="post" action="" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini? Stok buku yang dipinjam (jika ada) akan dikembalikan.');">
                        <input type="hidden" name="idpinjam" value="<?php echo $current_pinjam; ?>">
                        <input type="submit" name="delete" value="Hapus" class="btn btn-danger btn-sm">
                    </form>

                    <?php if ($row['status'] == 'aktif'): ?>
                        <a href="peminjamanedit.php?id=<?php echo $current_pinjam; ?>" class="btn btn-info btn-sm">Ubah</a>
                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Yakin ingin menyelesaikan peminjaman ini?');">
                            <input type="hidden" name="idpinjam" value="<?php echo $current_pinjam; ?>">
                            <input type="submit" name="selesaikan" value="Selesaikan" class="btn btn-success btn-sm">
                        </form>
                        <form method="post" action="" style="display:inline;" onsubmit="return confirm('Tandai sebagai hilang? Stok tidak akan dikembalikan dan proses tidak dapat dibatalkan.');">
                            <input type="hidden" name="idpinjam" value="<?php echo $current_pinjam; ?>">
                            <input type="submit" name="hilang" value="Hilang" class="btn btn-warning btn-sm">
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php
} else {
    echo "<br><br><div class='alert alert-warning'>Tidak ada data Peminjaman.</div>";
}
?>
</div>