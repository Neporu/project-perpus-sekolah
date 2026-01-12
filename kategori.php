<?php 
require_once 'connect.php';
require_once 'header.php';

echo "<div class='container'>";

// Proses penghapusan
if (isset($_POST['delete'])) {
    $id = $_POST['id']; 
    $sql = "DELETE FROM tbkategori WHERE idkategori = '$id'";
    if ($con->query($sql) === TRUE) {
        header("Location: kategori.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus data</div>";
    }
}

// Ambil data kategori
$sql = "SELECT * FROM tbkategori order by idkategori";
$result = $con->query($sql);

if ($result->num_rows > 0) {
?>
    <h2>Data Kategori</h2>
    <br>
    <a href="kategoriinsert.php" class="btn btn-info">Tambah Data</a>
    <br><br>

    <table class="table table-bordered table-striped">
        <tr>
            <th>Id Kategori</th>
            <th>Nama Kategori</th>
            <th colspan="2">Aksi</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['idkategori']; ?></td>
                <td><?php echo $row['namakategori']; ?></td>
                <td>
                    <a href="kategoriedit.php?id=<?php echo $row['idkategori']; ?>" class="btn btn-info btn-sm">Ubah</a>
                    <form method="post" action="" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                        <input type="hidden" name="id" value="<?php echo $row['idkategori']; ?>">
                        <input type="submit" name="delete" value="Hapus" class="btn btn-danger btn-sm">
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php
} else {
    echo "<br><br><div class='alert alert-warning'>Tidak ada data Kategori.</div>";
}
?>
</div>
