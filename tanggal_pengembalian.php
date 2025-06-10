<?php
include './connection/koneksi.php';
include './navbar/navbar.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pengembalian = $_POST['id_pengembalian'];
    $id_rental = $_POST['id_rental'];
    $tanggal_pengembalian = $_POST['tanggal_pengembalian'];
    $rental = mysqli_query($conn, "SELECT * FROM rental WHERE id_rental = '$id_rental'");
    $data = mysqli_fetch_assoc($rental);
    $tanggal_kembali = $data['tanggal_kembali'];
    $id_mobil = $data['id_mobil'];

    $tgl_kembali = new DateTime($tanggal_kembali);
    $tgl_kembali_aktual = new DateTime($tanggal_pengembalian);
    $selisih = $tgl_kembali_aktual->diff($tgl_kembali)->days;

    $denda = 0;
    if ($tgl_kembali_aktual > $tgl_kembali) {
        $denda = $selisih * 50000;
    }

    $sql = "INSERT INTO pengembalian (id_pengembalian, id_rental, tanggal_pengembalian, denda)
            VALUES ('$id_pengembalian', '$id_rental', '$tanggal_pengembalian', $denda)";
    $insert = mysqli_query($conn, $sql);

    if ($insert) {
        mysqli_query($conn, "UPDATE mobil SET status = 'tersedia' WHERE id_mobil = '$id_mobil'");
        mysqli_query($conn, "UPDATE rental SET status = 'selesai' WHERE id_rental = '$id_rental'");
        echo "Pengembalian berhasil dicatat. Denda: Rp " . number_format($denda, 0);
    } else {
        echo "Gagal menyimpan pengembalian: " . mysqli_error($conn);
    }
}
?>

<h2>Form Pengembalian Mobil</h2>
<form method="POST" action="">
    ID Pengembalian: <input type="text" name="id_pengembalian" required><br><br>

    Pilih Rental Aktif:
    <select name="id_rental" required>
        <option value="">-- Pilih --</option>
        <?php
        $rental_aktif = mysqli_query($conn, "SELECT * FROM rental WHERE status='disewa'");
        while ($r = mysqli_fetch_assoc($rental_aktif)) {
            echo "<option value='{$r['id_rental']}'>{$r['id_rental']} - {$r['id_pelanggan']} - Mobil: {$r['id_mobil']}</option>";
        }
        ?>
    </select><br><br>

    Tanggal Pengembalian Aktual: <input type="date" name="tanggal_pengembalian" required><br><br>

    <input type="submit" value="Simpan Pengembalian">
</form>
