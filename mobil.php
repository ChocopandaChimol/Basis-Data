<?php
include './connection/koneksi.php';
include './navbar/navbar.php'; 

if (isset($_POST['tambah'])) {
    $id_mobil = $_POST['id_mobil'];
    $merk = $_POST['merk'];
    $model = $_POST['model'];
    $tahun = $_POST['tahun'];
    $no_plat = $_POST['no_plat'];
    $status = $_POST['status'];
    $harga = $_POST['harga_per_hari'];

    $sql = "INSERT INTO mobil (id_mobil, merk, model, tahun, no_plat, status, harga_per_hari)
            VALUES ('$id_mobil', '$merk', '$model', $tahun, '$no_plat', '$status', $harga)";

    if (mysqli_query($conn, $sql)) {
        echo "Mobil berhasil ditambahkan.<br>";
    } else {
        echo "Gagal menambahkan: " . mysqli_error($conn) . "<br>";
    }
}

if (isset($_POST['update_status'])) {
    $id_mobil = $_POST['id_mobil_update'];
    $status = $_POST['status_baru'];

    $sql = "UPDATE mobil SET status = '$status' WHERE id_mobil = '$id_mobil'";

    if (mysqli_query($conn, $sql)) {
        echo "Status mobil berhasil diupdate.<br>";
    } else {
        echo "Gagal update status: " . mysqli_error($conn) . "<br>";
    }
}
?>

<h2>Form Tambah Mobil</h2>
<form method="POST" action="">
    ID Mobil: <input type="text" name="id_mobil" required><br><br>
    Merk: <input type="text" name="merk" required><br><br>
    Model: <input type="text" name="model" required><br><br>
    Tahun: <input type="number" name="tahun" required><br><br>
    No Plat: <input type="text" name="no_plat" required><br><br>
    Status:
    <select name="status">
        <option value="tersedia">Tersedia</option>
        <option value="disewa">Disewa</option>
        <option value="servis">Servis</option>
    </select><br><br>
    Harga per Hari: <input type="number" step="0.01" name="harga_per_hari" required><br><br>
    <input type="submit" name="tambah" value="Tambah Mobil">
</form>
<hr>

<h2>Form Update Status Mobil</h2>
<form method="POST" action="">
    ID Mobil: <input type="text" name="id_mobil_update" required><br><br>
    Status Baru:
    <select name="status_baru">
        <option value="tersedia">Tersedia</option>
        <option value="disewa">Disewa</option>
        <option value="servis">Servis</option>
    </select><br><br>
    <input type="submit" name="update_status" value="Update Status">
</form>
