<?php
require_once 'connect.php';
require_once 'header.php';
?>
<div class="container">
    <?php
    if (isset($_POST['addnew'])) {
        $idbuku = $_POST['idbuku'];
        $idkategori = $_POST['idkategori'];
        $judul = $_POST['judul'];
        $author = $_POST['author'];
        $stok = 0;
        $harga = $_POST['harga'];

        if (empty($idbuku) || empty($idkategori) || empty($judul) || empty($author) || empty($harga)) {
            echo "<div class='alert alert-warning'>Semua field harus diisi.</div>";
        } else {
            $sql = "INSERT INTO tbbuku (idbuku, idkategori, judul, author, stok, harga) 
                    VALUES ('$idbuku', '$idkategori', '$judul', '$author', '$stok', '$harga')";

            if ($con->query($sql) === TRUE) {
                header("Location: buku.php"); 
                exit();
            } else {
                echo "<div class='alert alert-danger'>Data gagal ditambahkan: " . $con->error . "</div>";
            }
        }
    }
    
    ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-plus"></i>&nbsp;Tambah Buku</h3>
                <form action="" method="POST">
                    <label for="idbuku">Id Buku</label>
                    <input type="text" id="idbuku" name="idbuku" class="form-control"><br>

                    <label for="idkategori">Kategori</label>
                    <select id="idkategori" name="idkategori" class="form-control">
                        <option value="">Pilih Kategori</option>
                        <?php
                            
                            $sql_kategori = "SELECT * FROM tbkategori";
                            $result_kategori = $con->query($sql_kategori);
                            while ($row_kategori = $result_kategori->fetch_assoc()) {
                                echo "<option value='" . $row_kategori['idkategori'] . "'>" . $row_kategori['idkategori'] . " - " . $row_kategori['namakategori'] . "</option>";
                            }
                        ?>
                    </select><br>

                    <label for="judul">Judul</label>
                    <input type="text" id="judul" name="judul" class="form-control"><br>

                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" class="form-control"><br>

                    <label for="harga">Harga</label>
                    <input type="number" id="harga" name="harga" class="form-control"><br>

                    <input type="submit" name="addnew" class="btn btn-success" value="Tambah">
                    <a href="buku.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
