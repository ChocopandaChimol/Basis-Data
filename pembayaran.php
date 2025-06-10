<?php
include './connection/koneksi.php';
include './navbar/navbar.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pembayaran = $_POST['id_pembayaran'];
    $id_rental = $_POST['id_rental'];
    $metode = $_POST['metode'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];

    $sql = "INSERT INTO pembayaran (id_pembayaran, id_rental, metode, jumlah, tanggal)
            VALUES ('$id_pembayaran', '$id_rental', '$metode', $jumlah, '$tanggal')";

    if (mysqli_query($conn, $sql)) {
        echo "Pembayaran berhasil dicatat.";
    } else {
        echo "Gagal menyimpan pembayaran: " . mysqli_error($conn);
    }
}
?>

<h2>Form Pembayaran</h2>
<form method="POST" action="">
    ID Pembayaran: <input type="text" name="id_pembayaran" required><br><br>

    Pilih Rental:
    <select name="id_rental" required>
        <option value="">-- Pilih --</option>
        <?php
        $rental = mysqli_query($conn, "SELECT * FROM rental");
        while ($r = mysqli_fetch_assoc($rental)) {
            echo "<option value='{$r['id_rental']}'>{$r['id_rental']} - {$r['id_pelanggan']} - Mobil: {$r['id_mobil']}</option>";
        }
        ?>
    </select><br><br>

    Metode Pembayaran: <input type="text" name="metode" required><br><br>
    Jumlah: <input type="number" step="0.01" name="jumlah" required><br><br>
    Tanggal: <input type="date" name="tanggal" required><br><br>

    <input type="submit" value="Simpan Pembayaran">
</form>
