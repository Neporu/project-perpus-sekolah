<?php 
require_once 'connect.php';
require_once 'header.php';

echo "<div class='container'>";

// Proses penghapusan yang diperbaiki
if (isset($_POST['delete'])) {
    $id = $_POST['id']; 
    // Hapus dulu dari tabel detail, lalu dari tabel utama
    $con->query("DELETE FROM tbdetailbeli WHERE idbuku = '$id'");
    $sql = "DELETE FROM tbbuku WHERE idbuku = '$id'";
    if ($con->query($sql) === TRUE) {
        header("Location: buku.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus data</div>";
    }
}

// 1. Ambil semua data pembelian utama
$sql = "select 
    b.idbuku,
    b.idkategori,
    k.namakategori,
    b.judul,
    b.author,
    b.stok,
    b.harga
from tbbuku b
join tbkategori k on b.idkategori = k.idkategori;";
$result = $con->query($sql);

if ($result->num_rows > 0) {
?>
    <h2>Data Buku</h2>
    <br>
    <a href="bukuinsert.php" class="btn btn-info">Tambah Data</a>
    <br><br>
    <table  class="table">
        <tr>
            <th>Id Buku</th>
            <th>Id Kategori</th>
            <th>Judul</th>
            <th>Stok</th>
            <th>Harga</th>
            <th colspan="2">Aksi</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['idbuku']; ?></td>
                <td><?php echo $row['namakategori']; ?></td>
                <td><?php echo $row['judul']; ?></td>
                <td><?php echo $row['stok']; ?></td>
                <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><a href="bukuedit.php?idbuku=<?php echo $row['idbuku']; ?>" class="btn btn-warning">Edit</a></td>
                <td>
                    <form method="post" action="buku.php" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                        <input type="hidden" name="id" value="<?php echo $row['idbuku']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php
} else {
    echo "<br><br><div class='alert alert-warning'>Tidak ada data Buku.</div>";
}
?>
</div>