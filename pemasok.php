<?php 
require_once 'connect.php';
require_once 'header.php';

echo "<div class='container'>";

// Proses penghapusan
if (isset($_POST['delete'])) {
    $id = $_POST['id']; 
    
    $sql = "DELETE FROM tbpemasok WHERE idpemasok = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $id);
    
    if ($stmt->execute()) {
        header("Location: pemasok.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus data: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Ambil data pemasok
$sql = "SELECT * FROM tbpemasok ORDER BY idpemasok";
$result = $con->query($sql);

if ($result->num_rows > 0) {
?>
    <h2>Data Pemasok</h2>
    <br>
    <a href="pemasokinsert.php" class="btn btn-info">Tambah Data</a>
    <br><br>

    <table class="table table-bordered table-striped">
        <tr>
            <th>ID Pemasok</th>
            <th>Nama Pemasok</th>
            <th>Alamat</th>
            <th>Telp</th> 
            <th>Aksi</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['idpemasok']); ?></td>
                <td><?php echo htmlspecialchars($row['namapemasok']); ?></td>
                <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                <td><?php echo htmlspecialchars($row['telp']); ?></td>
                <td>
                    <a href="pemasokedit.php?id=<?php echo $row['idpemasok']; ?>" class="btn btn-info btn-sm">Ubah</a>
                    <form method="post" action="" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                        <input type="hidden" name="id" value="<?php echo $row['idpemasok']; ?>">
                        <input type="submit" name="delete" value="Hapus" class="btn btn-danger btn-sm">
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php
} else {
    echo "<br><br><div class='alert alert-warning'>Tidak ada data pemasok.</div>";
}
?>
</div>