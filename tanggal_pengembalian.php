<?php
include './connection/koneksi.php';
include './navbar/navbar.php';

$denda = 0;
$error_message = '';
$success_message = '';
$edit_data = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pengembalian = mysqli_real_escape_string($conn, $_POST['id_pengembalian']);
    $id_rental = mysqli_real_escape_string($conn, $_POST['id_rental']);
    $tanggal_pengembalian = mysqli_real_escape_string($conn, $_POST['tanggal_pengembalian']);

    $rental = mysqli_query($conn, "SELECT * FROM rental WHERE id_rental = '$id_rental'");
    if ($data = mysqli_fetch_assoc($rental)) {
        $tanggal_kembali = $data['tanggal_kembali'];
        $id_mobil = mysqli_real_escape_string($conn, $data['id_mobil']);
        $tgl_kembali = new DateTime($tanggal_kembali);
        $tgl_kembali_aktual = new DateTime($tanggal_pengembalian);
        $selisih = $tgl_kembali_aktual->diff($tgl_kembali)->days;
        if ($tgl_kembali_aktual > $tgl_kembali) {
            $denda = $selisih * 50000;
        } else {
            $denda = 0;
        }

        if (isset($_POST['update'])) {
            $sql = "UPDATE pengembalian SET tanggal_pengembalian='$tanggal_pengembalian', denda=$denda
                    WHERE id_pengembalian='$id_pengembalian'";
            if (mysqli_query($conn, $sql)) {
                $success_message = "Data pengembalian berhasil diperbarui.";
            } else {
                $error_message = "Gagal update: " . mysqli_error($conn);
            }
        } else {
            $check_query = "SELECT id_pengembalian FROM pengembalian WHERE id_pengembalian='$id_pengembalian'";
            $check_result = mysqli_query($conn, $check_query);
            if (mysqli_num_rows($check_result) > 0) {
                $error_message = "ID Pengembalian '$id_pengembalian' sudah digunakan. Gunakan ID lain.";
            } else {
                $sql = "INSERT INTO pengembalian (id_pengembalian, id_rental, tanggal_pengembalian, denda)
                        VALUES ('$id_pengembalian', '$id_rental', '$tanggal_pengembalian', $denda)";
                if (mysqli_query($conn, $sql)) {
                    mysqli_query($conn, "UPDATE mobil SET status = 'tersedia' WHERE id_mobil = '$id_mobil'");
                    mysqli_query($conn, "UPDATE rental SET status = 'selesai' WHERE id_rental = '$id_rental'");
                    $success_message = "Pengembalian berhasil dicatat. Denda: Rp " . number_format($denda, 0, ',', '.');
                } else {
                    $error_message = "Gagal menyimpan pengembalian: " . mysqli_error($conn);
                }
            }
        }
    } else {
        $error_message = "Rental dengan ID $id_rental tidak ditemukan atau sudah selesai.";
    }
}
if (isset($_GET['delete'])) {
    $id_delete = mysqli_real_escape_string($conn, $_GET['delete']);
    $q = mysqli_query($conn, "SELECT * FROM pengembalian WHERE id_pengembalian='$id_delete'");
    if ($data = mysqli_fetch_assoc($q)) {
        $id_rental = mysqli_real_escape_string($conn, $data['id_rental']);
        $rental = mysqli_query($conn, "SELECT * FROM rental WHERE id_rental='$id_rental'");
        if ($rental_data = mysqli_fetch_assoc($rental)) {
            $id_mobil = mysqli_real_escape_string($conn, $rental_data['id_mobil']);
            mysqli_query($conn, "DELETE FROM pengembalian WHERE id_pengembalian='$id_delete'");
            mysqli_query($conn, "UPDATE rental SET status='disewa' WHERE id_rental='$id_rental'");
            mysqli_query($conn, "UPDATE mobil SET status='disewa' WHERE id_mobil='$id_mobil'");
            $success_message = "Data pengembalian berhasil dihapus.";
        } else {
            $error_message = "Data rental terkait tidak ditemukan.";
        }
    } else {
        $error_message = "Data pengembalian dengan ID $id_delete tidak ditemukan.";
    }
}
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM pengembalian WHERE id_pengembalian='$id_edit'");
    $edit_data = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengembalian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-4 sm:p-8">
        <?php if ($success_message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <?= $success_message ?>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <?= $error_message ?>
            </div>
        <?php endif; ?>
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6"><?= $edit_data ? 'Edit Pengembalian' : 'Form Pengembalian Mobil' ?></h2>
            <form method="POST" action="" class="space-y-4">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id_pengembalian" value="<?= $edit_data['id_pengembalian'] ?>">
                    <input type="hidden" name="id_rental" value="<?= $edit_data['id_rental'] ?>">
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Pengembalian</label>
                    <?php if ($edit_data): ?>
                        <p class="mt-1 block w-full text-gray-700 font-semibold"><?= htmlspecialchars($edit_data['id_pengembalian']) ?></p>
                    <?php else: ?>
                        <input type="text" name="id_pengembalian" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Rental Aktif</label>
                    <?php if ($edit_data): ?>
                        <p class="mt-1 block w-full text-gray-700 font-semibold"><?= htmlspecialchars($edit_data['id_rental']) ?></p>
                    <?php else: ?>
                        <select name="id_rental" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">-- Pilih --</option>
                            <?php
                            $rental_aktif = mysqli_query($conn, "SELECT r.*, p.nama, m.merk, m.model 
                                                                FROM rental r 
                                                                JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                                                                JOIN mobil m ON r.id_mobil = m.id_mobil 
                                                                WHERE r.status='disewa'");
                            while ($r = mysqli_fetch_assoc($rental_aktif)) {
                                echo "<option value='{$r['id_rental']}'>{$r['id_rental']} - {$r['nama']} - {$r['merk']} {$r['model']}</option>";
                            }
                            ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Pengembalian Aktual</label>
                    <input type="date" name="tanggal_pengembalian" required value="<?= $edit_data['tanggal_pengembalian'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="<?= $edit_data ? 'update' : '' ?>" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <i class="fas fa-save mr-2"></i><?= $edit_data ? 'Update' : 'Simpan Pengembalian' ?>
                    </button>
                    <?php if ($edit_data): ?>
                        <a href="pengembalian.php" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Data Pengembalian Mobil</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">ID Pengembalian</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">ID Rental</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Nama Pelanggan</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Mobil</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Tanggal Pengembalian</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Denda</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $query = "
                            SELECT p.*, r.id_pelanggan, r.id_mobil, pl.nama, CONCAT(m.merk, ' ', m.model) AS nama_mobil
                            FROM pengembalian p
                            JOIN rental r ON p.id_rental = r.id_rental
                            JOIN pelanggan pl ON r.id_pelanggan = pl.id_pelanggan
                            JOIN mobil m ON r.id_mobil = m.id_mobil
                            ORDER BY p.tanggal_pengembalian DESC
                        ";
                        $result = mysqli_query($conn, $query);
                        if (mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                        ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['id_pengembalian']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['id_rental']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['nama_mobil']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['tanggal_pengembalian']) ?></td>
                                    <td class="px-4 py-2">Rp <?= number_format($row['denda'], 0, ',', '.') ?></td>
                                    <td class="px-4 py-2">
                                        <a href="?edit=<?= $row['id_pengembalian'] ?>" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?delete=<?= $row['id_pengembalian'] ?>" onclick="return confirm('Yakin ingin menghapus pengembalian ini?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td class="px-4 py-4 text-center" colspan="7">Belum ada data pengembalian.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>