<?php 
require_once 'connect.php';
require_once 'header.php';
echo "<div class='container'>";

// Proses penghapusan denda
if (isset($_POST['delete'])) {
    $iddenda = $_POST['iddenda']; 

    $stmt = $con->prepare("DELETE FROM tbdenda WHERE iddenda = ?");
    $stmt->bind_param("s", $iddenda);
    
    if ($stmt->execute()) {
        header("Location: denda.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus data: " . $con->error . "</div>";
    }
}

// Ambil semua data denda
$sql = "SELECT * FROM tbdenda ORDER BY tglbuat DESC";
$result = $con->query($sql);
?>
<h2>Data Denda</h2>
<br>
<a href="denda-insert.php" class="btn btn-info">Tambah Data</a>
<br><br>
<?php
if ($result->num_rows > 0) { ?>
    <table  class="table table-bordered table-striped">
        <tr>
            <th>Id Denda</th>
            <th>Id Pinjam</th>
            <th>Tanggal Denda dibuat</th>
            <th>Tanggal Bayar Denda</th>
            <th>Total Denda</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['iddenda']; ?></td>
                <td><?php echo $row['idpinjam']; ?></td>
                <td><?php echo $row['tglbuat']; ?></td>
                <td><?php echo $row['tglbayar']; ?></td>
                <td>Rp <?php echo number_format($row['totaldenda'], 0, ',', '.'); ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <?php if ($row['status'] != 'lunas'): ?>
                        <a href="denda-edit.php?id=<?php echo $row['iddenda']; ?>" class="btn btn-info btn-sm">Ubah</a>
                    <?php endif; ?>
                    <form method="post" action="" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                            <input type="hidden" name="iddenda" value="<?php echo $row['iddenda']; ?>">
                            <input type="submit" name="delete" value="Hapus" class="btn btn-danger btn-sm">
                    </form>
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