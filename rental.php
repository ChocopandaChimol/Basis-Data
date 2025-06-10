<?php
include './connection/koneksi.php';
include './navbar/navbar.php'; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_rental = $_POST['id_rental'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_mobil = $_POST['id_mobil'];
    $tanggal_sewa = $_POST['tanggal_sewa'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    $start = new DateTime($tanggal_sewa);
    $end = new DateTime($tanggal_kembali);
    $selisih = $start->diff($end)->days;
    if ($selisih <= 0) $selisih = 1;

    $result = mysqli_query($conn, "SELECT harga_per_hari FROM mobil WHERE id_mobil='$id_mobil'");
    $row = mysqli_fetch_assoc($result);
    $harga_per_hari = $row['harga_per_hari'];
    $total_harga = $harga_per_hari * $selisih;

    $sql = "INSERT INTO rental (id_rental, id_pelanggan, id_mobil, tanggal_sewa, tanggal_kembali, total_harga)
            VALUES ('$id_rental', '$id_pelanggan', '$id_mobil', '$tanggal_sewa', '$tanggal_kembali', $total_harga)";

    if (mysqli_query($conn, $sql)) {
        mysqli_query($conn, "UPDATE mobil SET status='disewa' WHERE id_mobil='$id_mobil'");
        echo "Transaksi rental berhasil disimpan. Total harga: Rp " . number_format($total_harga, 2);
    } else {
        echo "Gagal menyimpan data rental: " . mysqli_error($conn);
    }
}
?>

<h2>Form Rental Mobil</h2>
<form method="POST" action="">
    ID Rental: <input type="text" name="id_rental" required><br><br>

    Pilih Pelanggan:
    <select name="id_pelanggan" required>
        <option value="">-- Pilih --</option>
        <?php
        $pelanggan = mysqli_query($conn, "SELECT * FROM pelanggan");
        while ($p = mysqli_fetch_assoc($pelanggan)) {
            echo "<option value='{$p['id_pelanggan']}'>{$p['id_pelanggan']} - {$p['nama']}</option>";
        }
        ?>
    </select><br><br>

    Pilih Mobil (yang tersedia):
    <select name="id_mobil" required>
        <option value="">-- Pilih --</option>
        <?php
        $mobil = mysqli_query($conn, "SELECT * FROM mobil WHERE status='tersedia'");
        while ($m = mysqli_fetch_assoc($mobil)) {
            echo "<option value='{$m['id_mobil']}'>{$m['id_mobil']} - {$m['merk']} {$m['model']}</option>";
        }
        ?>
    </select><br><br>

    Tanggal Sewa: <input type="date" name="tanggal_sewa" required><br><br>
    Tanggal Kembali: <input type="date" name="tanggal_kembali" required><br><br>

    <input type="submit" value="Sewa">
</form>
