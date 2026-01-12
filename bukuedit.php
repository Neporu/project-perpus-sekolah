<?php
require_once 'connect.php';
require_once 'header.php';
?>
<div class="container">
    <?php
    // Proses Update
    if (isset($_POST['update'])) {
        if (empty($_POST['idbuku']) || empty($_POST['idkategori']) || empty($_POST['judul']) || empty($_POST['author']) || empty($_POST['harga'])) {
            echo "<div class='alert alert-warning'>Semua field harus diisi.</div>";
        } else {
            $idbuku = $_POST['idbuku'];
            $idkategori = $_POST['idkategori'];
            $judul= $_POST['judul'];
            $author = $_POST['author'];
            $stok = $_POST['stok'];
            $harga = $_POST['harga'];

            $sql = "UPDATE tbbuku SET idkategori = '$idkategori', judul = '$judul', author = '$author', stok = '$stok', harga = '$harga'
                    WHERE idbuku = '$idbuku'";

            if ($con->query($sql) === TRUE) {
                header("Location: buku.php"); 
                exit();
            } else {
                echo "<div class='alert alert-danger'>Data gagal diubah: " . $con->error . "</div>";
            }
        }
    }

    // Ambil data berdasarkan ID dari URL
    $id = isset($_GET['idbuku']) ? $_GET['idbuku'] : '';
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

    if ($result->num_rows < 1) {
        header("Location: buku.php");
        exit();
    }

    $row = $result->fetch_assoc();
    ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-edit"></i>&nbsp;Ubah Buku</h3>
                <form action="" method="POST">
                    <label for="idbuku">Id Buku</label>
                    <input type="text" id="idbuku" name="idbuku" value="<?php echo $row['idbuku']; ?>" class="form-control" readonly><br>

                    <label for="idkategori">Id Kategori</label>
                    <select id="idkategori" name="idkategori" class="form-control">
                        <option value="<?php echo $row['idkategori']; ?>"><?php echo $row['namakategori']; ?></option>
                        <?php
                            $sql_kategori = "SELECT * FROM tbkategori";
                            $result_kategori = $con->query($sql_kategori);
                            while ($row_kategori = $result_kategori->fetch_assoc()) {
                                echo "<option value='" . $row_kategori['idkategori'] . "'>" . $row_kategori['idkategori'] . " - " . $row_kategori['namakategori'] . "</option>";
                            }
                        ?>
                    </select><br>

                    <label for="judul">Judul</label>
                    <input type="text" id="judul" name="judul" value="<?php echo $row['judul']; ?>" class="form-control"><br>

                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" value="<?php echo $row['author']; ?>" class="form-control"><br>

                    <label for="stok">Stok</label>
                    <input type="text" id="stok" name="stok" value="<?php echo $row['stok']; ?>" class="form-control" readonly><br>

                    <label for="harga">Harga</label>
                    <input type="text" id="harga" name="harga" value="<?php echo $row['harga']; ?>" class="form-control"><br>

                    <input type="submit" name="update" class="btn btn-success" value="Update">
                    <a href="buku.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
