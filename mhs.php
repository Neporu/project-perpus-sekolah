<?php 
require_once 'connect.php';
require_once 'header.php';

echo "<div class='container'>";

// Proses penghapusan
if (isset($_POST['delete'])) {
    $id = $_POST['id']; 
    $sql = "DELETE FROM tbmhs WHERE nim = '$id'";
    if ($con->query($sql) === TRUE) {
        header("Location: mhs.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus data</div>";
    }
}

// Ambil data mahasiswa
$sql = "SELECT * FROM tbmhs order by nim";
$result = $con->query($sql);

if ($result->num_rows > 0) {
?>
    <h2>Data mahasiswa</h2>
    <br>
    <a href="mhsinsert.php" class="btn btn-info">Tambah Data</a>
    <br><br>

    <table class="table table-bordered table-striped">
        <tr>
            <th>nim</th>
            <th>nama</th>
            <th>alamat</th>
            <th>telp</th>
            <th colspan="2">Aksi</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['nim']; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['alamat']; ?></td>
                <td><?php echo $row['telp']; ?></td>
                <td>
                    <a href="mhsedit.php?id=<?php echo $row['nim']; ?>" class="btn btn-info btn-sm">Ubah</a>
                    <form method="post" action="" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                        <input type="hidden" name="id" value="<?php echo $row['nim']; ?>">
                        <input type="submit" name="delete" value="Hapus" class="btn btn-danger btn-sm">
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php
} else {
    echo "<br><br><div class='alert alert-warning'>Tidak ada data Mahasiswa.</div>";
}
?>
</div>