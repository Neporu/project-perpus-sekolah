<?php
require_once 'connect.php';
require_once 'header.php';

$id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : '');

if (!$id) {
    header("Location: pemasok.php");
    exit();
}
?>
<div class="container">
    <?php
    // Proses Update
    if (isset($_POST['update'])) {
        if (empty($_POST['idpemasok']) || empty($_POST['namapemasok']) || empty($_POST['alamat']) || empty($_POST['telp'])) {
            echo "<div class='alert alert-warning'>Semua field harus diisi.</div>";
        } else {
            $idpemasok = $_POST['idpemasok'];
            $namapemasok = $_POST['namapemasok'];
            $alamat = $_POST['alamat'];
            $telp = $_POST['telp'];

            $sql = "UPDATE tbpemasok SET namapemasok = ?, alamat = ?, telp = ? WHERE idpemasok = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssss", $namapemasok, $alamat, $telp, $idpemasok);

            if ($stmt->execute()) {
                header("Location: pemasok.php"); 
                exit();
            } else {
                echo "<div class='alert alert-danger'>Data gagal diubah: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }

    // Ambil data berdasarkan ID
    $sql = "SELECT * FROM tbpemasok WHERE idpemasok = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        header("Location: pemasok.php");
        exit();
    }

    $row = $result->fetch_assoc();
    $stmt->close();
    ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-edit"></i>&nbsp;Ubah pemasok</h3>
                <form action="pemasokedit.php?id=<?php echo htmlspecialchars($id); ?>" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                    <label for="idpemasok">Id pemasok</label>
                    <input type="text" id="idpemasok" name="idpemasok" value="<?php echo htmlspecialchars($row['idpemasok']); ?>" class="form-control" readonly><br>

                    <label for="namapemasok">Nama pemasok</label>
                    <input type="text" id="namapemasok" name="namapemasok" value="<?php echo htmlspecialchars($row['namapemasok']); ?>" class="form-control"><br>

                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($row['alamat']); ?>" class="form-control"><br>

                    <label for="telp">Telp</label>
                    <input type="text" id="telp" name="telp" value="<?php echo htmlspecialchars($row['telp']); ?>" class="form-control"><br>

                    <input type="submit" name="update" class="btn btn-success" value="Update">
                    <a href="pemasok.php" class="btn btn-info">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>