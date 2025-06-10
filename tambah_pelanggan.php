<?php
include './connection/koneksi.php';
include './navbar/navbar.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pelanggan = $_POST["id_pelanggan"];
    $nama         = $_POST["nama"];
    $alamat       = $_POST["alamat"];
    $no_hp        = $_POST["no_hp"];
    $email        = $_POST["email"];

    $sql = "INSERT INTO pelanggan (id_pelanggan, nama, alamat, no_hp, email)
            VALUES ('$id_pelanggan', '$nama', '$alamat', '$no_hp', '$email')";
    if (mysqli_query($conn, $sql)) {
        echo "Data pelanggan berhasil ditambahkan.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
<h2>Form Tambah Pelanggan</h2>
<form method="POST" action="">
    ID Pelanggan: <input type="text" name="id_pelanggan" required><br><br>
    Nama: <input type="text" name="nama" required><br><br>
    Alamat: <textarea name="alamat" required></textarea><br><br>
    No HP: <input type="text" name="no_hp" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    <input type="submit" value="Simpan">
</form>
