<?php 
require_once 'connect.php';
require_once 'header.php';

echo "<div class='container'>";

// Proses penghapusan
if (isset($_POST['delete'])) {
    $id = $_POST['id']; 
    $sql = "DELETE FROM tbpetugas WHERE nik = '$id'";
    if ($con->query($sql) === TRUE) {
        header("Location: petugas.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus data</div>";
    }
}

// Ambil data petugas
$sql = "SELECT * FROM tbpetugas order by nik";
$result = $con->query($sql);

if ($result->num_rows > 0) {
?>
    <h2>Data Petugas</h2>
    <br>
    <a href="petugasinsert.php" class="btn btn-info">Tambah Data</a>
    <br><br>

    <table class="table table-bordered table-striped">
        <tr>
            <th>NIK</th>
            <th>Nama</th>
            <th>Sandi</th>
            <th>Alamat</th>
            <th>Telepon</th>
            <th colspan="2">Aksi</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['nik']; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['sandi']; ?></td>
                <td><?php echo $row['alamat']; ?></td>
                <td><?php echo $row['telp']; ?></td>
                <td>
                    <a href="petugasedit.php?id=<?php echo $row['nik']; ?>" class="btn btn-info btn-sm">Ubah</a>
                    <form method="post" action="" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                        <input type="hidden" name="id" value="<?php echo $row['nik']; ?>">
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
