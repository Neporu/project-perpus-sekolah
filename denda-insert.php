<?php
require_once 'connect.php';
require_once 'header.php';

// --- Logika Perhitungan Denda Otomatis ---
$denda_per_hari = 1000; // Denda Rp 1.000 per hari
$denda_data = [];

// Query untuk mengambil semua detail peminjaman yang berpotensi kena denda
$query_details = "
    SELECT 
        dp.idpinjam,
        dp.status,
        dp.tglkembali,
        p.tgljatuhtempo,
        b.harga
    FROM tbdetailpinjam dp
    JOIN tbpeminjaman p ON dp.idpinjam = p.idpinjam
    JOIN tbbuku b ON dp.idbuku = b.idbuku
    WHERE dp.status = 'hilang' 
       OR (dp.status = 'kembali' AND dp.tglkembali > p.tgljatuhtempo)
       OR (dp.status = 'dipinjam' AND CURDATE() > p.tgljatuhtempo)
";

$result_details = $con->query($query_details);
$potential_fines = [];
if ($result_details) {
    while ($row = $result_details->fetch_assoc()) {
        $potential_fines[$row['idpinjam']][] = $row;
    }
}

// Hitung total denda untuk setiap idpinjam
foreach ($potential_fines as $idpinjam => $details) {
    $total_denda = 0;
    $unique_details = array_unique(array_column($details, 'status')); // Handle multiple books in one loan

    foreach ($details as $detail) {
        if ($detail['status'] == 'hilang') {
            $total_denda += $detail['harga'];
        } else { // Keterlambatan
            $tgl_jatuh_tempo = new DateTime($detail['tgljatuhtempo']);
            $tgl_referensi = ($detail['status'] == 'kembali') ? new DateTime($detail['tglkembali']) : new DateTime();
            
            if ($tgl_referensi > $tgl_jatuh_tempo) {
                $selisih = $tgl_referensi->diff($tgl_jatuh_tempo)->days;
                $total_denda += $selisih * $denda_per_hari;
            }
        }
    }
    
    if ($total_denda > 0) {
        // Periksa apakah denda untuk pinjaman ini sudah ada
        $stmt_check = $con->prepare("SELECT COUNT(*) FROM tbdenda WHERE idpinjam = ?");
        $stmt_check->bind_param("s", $idpinjam);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count == 0) {
            $denda_data[$idpinjam] = $total_denda;
        }
    }
}

if (isset($_POST['addnew'])) {
    // Ambil data dari form
    $iddenda = 'D' . time(); // ID Denda dibuat otomatis
    $idpinjam = $_POST['idpinjam'];
    $tglbuat = $_POST['tglbuat'];
    $totaldenda = (int)preg_replace('/[^\d]/', '', $_POST['totaldenda']); // Bersihkan format Rp
    $jmlbayar = (int)$_POST['jmlbayar'];
    
    // Batasi jmlbayar agar tidak melebihi totaldenda
    if ($jmlbayar > $totaldenda) {
        $jmlbayar = $totaldenda;
    }

    $tglbayar = !empty($_POST['tglbayar']) ? $_POST['tglbayar'] : NULL;
    
    // Logika status otomatis
    if ($tglbayar !== NULL && $jmlbayar >= $totaldenda && $totaldenda > 0) {
        $status = 'lunas';
    } else {
        $status = 'belum lunas';
    }

    if (empty($idpinjam) || empty($tglbuat)) {
        echo "<div class='alert alert-warning'>ID Pinjam dan Tanggal Dibuat harus diisi!</div>";
    } else {
        $con->begin_transaction();
        try {
            $stmt1 = $con->prepare("INSERT INTO tbdenda (iddenda, idpinjam, totaldenda, status, tglbuat, tglbayar, jmlbayar) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt1->bind_param("ssisssi", $iddenda, $idpinjam, $totaldenda, $status, $tglbuat, $tglbayar, $jmlbayar);
            $stmt1->execute();
            
            $con->commit();
            echo "<div class='alert alert-success'>Data denda berhasil ditambahkan! Anda akan dialihkan...</div>";
            echo '<meta http-equiv="refresh" content="2;url=denda.php">';
            exit();
        } catch (mysqli_sql_exception $exception) {
            $con->rollback();
            echo "<div class='alert alert-danger'>Data gagal ditambahkan: " . $exception->getMessage() . "</div>";
        }
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="box">
                <h3><i class="glyphicon glyphicon-plus"></i>&nbsp;Tambah Denda</h3>
                <form action="" method="POST">
                    <label for="idpinjam" class="form-label">ID Pinjam</label>
                    <select name="idpinjam" id="idpinjam" class="form-control" required>
                        <option value="" selected disabled>-- Pilih ID Pinjam yang Bermasalah --</option>
                        <?php foreach ($denda_data as $idpinjam_key => $total) : ?>
                        <option value="<?php echo htmlspecialchars($idpinjam_key); ?>"><?php echo htmlspecialchars($idpinjam_key); ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <label for="tglbuat" class="form-label">Tanggal Denda dibuat</label>
                    <input type="date" id="tglbuat" name="tglbuat" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>

                    <label for="totaldenda" class="form-label">Total Denda</label>
                    <input type="text" name="totaldenda" id="totaldenda" class="form-control" readonly>

                    <hr>
                    <h4>Pembayaran</h4>
                    <label for="jmlbayar" class="form-label">Jumlah Pembayaran</label>
                    <input type="number" name="jmlbayar" id="jmlbayar" class="form-control" min="0">

                    <label for="tglbayar" class="form-label">Tanggal Pembayaran</label>
                    <input type="date" id="tglbayar" name="tglbayar" class="form-control">
                    
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="belum lunas" selected>Belum Lunas</option>
                        <option value="lunas" id="status-lunas" disabled>Lunas</option>
                    </select>

                    <input type="submit" name="addnew" class="btn btn-success" value="Tambah">
                    <a href="denda.php" class="btn btn-danger">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dendaData = <?php echo json_encode($denda_data); ?>;
    const idPinjamSelect = document.getElementById('idpinjam');
    const totalDendaInput = document.getElementById('totaldenda');
    const jmlBayarInput = document.getElementById('jmlbayar');
    const tglBayarInput = document.getElementById('tglbayar');
    const statusSelect = document.getElementById('status');
    const statusLunasOption = document.getElementById('status-lunas');

    function formatCurrency(value) {
        if (!value && value !== 0) return '';
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    }

    function updateDenda() {
        const selectedIdPinjam = idPinjamSelect.value;
        const denda = dendaData[selectedIdPinjam] || 0;
        totalDendaInput.value = formatCurrency(denda);
        jmlBayarInput.max = denda; // Set max attribute for validation
        checkPaymentStatus();
    }

    function checkPaymentStatus() {
        const jmlBayar = parseFloat(jmlBayarInput.value) || 0;
        const tglBayar = tglBayarInput.value;
        const selectedIdPinjam = idPinjamSelect.value;
        const totalDenda = dendaData[selectedIdPinjam] || 0;

        if (tglBayar && jmlBayar >= totalDenda && totalDenda > 0) {
            statusLunasOption.disabled = false;
            statusSelect.value = 'lunas';
        } else {
            statusLunasOption.disabled = true;
            statusSelect.value = 'belum lunas';
        }
    }

    idPinjamSelect.addEventListener('change', updateDenda);
    jmlBayarInput.addEventListener('input', checkPaymentStatus);
    tglBayarInput.addEventListener('change', checkPaymentStatus);
});
</script>