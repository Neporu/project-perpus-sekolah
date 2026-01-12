<?php
require_once 'connect.php';
require_once 'header.php';

$iddenda = '';
$denda_data = [];

// Cek apakah ID ada di URL
if (isset($_GET['id'])) {
    $iddenda = $_GET['id'];
    
    // Ambil data denda yang ada
    $stmt_get = $con->prepare("
        SELECT d.idpinjam, d.totaldenda, d.status, d.tglbuat, d.tglbayar, d.jmlbayar
        FROM tbdenda d
        WHERE d.iddenda = ?
    ");
    $stmt_get->bind_param("s", $iddenda);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    
    if ($result->num_rows > 0) {
        $denda_data = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Denda tidak ditemukan.</div>";
        exit;
    }
    $stmt_get->close();
} else {
    header("Location: denda.php");
    exit;
}

// Handle form submission untuk update
if (isset($_POST['update'])) {
    $jmlbayar = (int)$_POST['jmlbayar'];
    $tglbayar = !empty($_POST['tglbayar']) ? $_POST['tglbayar'] : NULL;
    $totaldenda_val = $denda_data['totaldenda'];

    // Batasi jmlbayar agar tidak melebihi totaldenda
    if ($jmlbayar > $totaldenda_val) {
        $jmlbayar = $totaldenda_val;
    }

    // Logika status otomatis
    if ($tglbayar !== NULL && $jmlbayar >= $totaldenda_val && $totaldenda_val > 0) {
        $status = 'lunas';
    } else {
        $status = 'belum lunas';
    }

    $con->begin_transaction();
    try {
        $stmt_update = $con->prepare("UPDATE tbdenda SET jmlbayar = ?, tglbayar = ?, status = ? WHERE iddenda = ?");
        $stmt_update->bind_param("isss", $jmlbayar, $tglbayar, $status, $iddenda);
        $stmt_update->execute();
        
        $con->commit();
        echo "<div class='alert alert-success'>Data denda berhasil diperbarui! Anda akan dialihkan...</div>";
        echo '<meta http-equiv="refresh" content="2;url=denda.php">';
        exit();
    } catch (mysqli_sql_exception $exception) {
        $con->rollback();
        echo "<div class='alert alert-danger'>Data gagal diperbarui: " . $exception->getMessage() . "</div>";
    }
}
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3><i class="bi bi-pencil-square"></i>&nbsp;Ubah Data Denda</h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">ID Denda</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($iddenda); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ID Pinjam</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($denda_data['idpinjam']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="totaldenda" class="form-label">Total Denda</label>
                            <input type="text" id="totaldenda" class="form-control" value="Rp <?php echo number_format($denda_data['totaldenda'], 0, ',', '.'); ?>" readonly>
                            <input type="hidden" id="totaldenda_val" value="<?php echo $denda_data['totaldenda']; ?>">
                        </div>

                        <hr>
                        <h4>Pembayaran</h4>
                        <div class="mb-3">
                            <label for="jmlbayar" class="form-label">Jumlah Pembayaran</label>
                            <input type="number" name="jmlbayar" id="jmlbayar" class="form-control" min="0" max="<?php echo htmlspecialchars($denda_data['totaldenda']); ?>" value="<?php echo htmlspecialchars($denda_data['jmlbayar']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="tglbayar" class="form-label">Tanggal Pembayaran</label>
                            <input type="date" id="tglbayar" name="tglbayar" class="form-control" value="<?php echo htmlspecialchars($denda_data['tglbayar']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="belum lunas" <?php echo ($denda_data['status'] == 'belum lunas') ? 'selected' : ''; ?>>Belum Lunas</option>
                                <option value="lunas" id="status-lunas" <?php echo ($denda_data['status'] == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                            </select>
                        </div>

                        <input type="submit" name="update" class="btn btn-success" value="Simpan Perubahan">
                        <a href="denda.php" class="btn btn-danger">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalDenda = parseFloat(document.getElementById('totaldenda_val').value) || 0;
    const jmlBayarInput = document.getElementById('jmlbayar');
    const tglBayarInput = document.getElementById('tglbayar');
    const statusSelect = document.getElementById('status');
    const statusLunasOption = document.getElementById('status-lunas');

    function checkPaymentStatus() {
        const jmlBayar = parseFloat(jmlBayarInput.value) || 0;
        const tglBayar = tglBayarInput.value;

        if (tglBayar && jmlBayar >= totalDenda && totalDenda > 0) {
            statusLunasOption.disabled = false;
            statusSelect.value = 'lunas';
        } else {
            statusLunasOption.disabled = true;
            statusSelect.value = 'belum lunas';
        }
    }

    // Panggil saat halaman dimuat untuk mengatur status awal
    checkPaymentStatus();

    jmlBayarInput.addEventListener('input', checkPaymentStatus);
    tglBayarInput.addEventListener('change', checkPaymentStatus);
});
</script>