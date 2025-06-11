<?php
include './connection/koneksi.php';
include './navbar/navbar.php';

$error_message = '';
$success_message = '';

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    $get_mobil = mysqli_query($conn, "SELECT id_mobil FROM rental WHERE id_rental='$id'");
    if ($mobil = mysqli_fetch_assoc($get_mobil)) {
        $mobil_id = mysqli_real_escape_string($conn, $mobil['id_mobil']);
        mysqli_query($conn, "UPDATE mobil SET status='tersedia' WHERE id_mobil='$mobil_id'");
        mysqli_query($conn, "DELETE FROM rental WHERE id_rental='$id'");
        $success_message = "Data rental dengan ID $id berhasil dihapus.";
    } else {
        $error_message = "Data rental dengan ID $id tidak ditemukan.";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_rental = mysqli_real_escape_string($conn, $_POST['id_rental']);
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST['id_pelanggan']);
    $id_mobil = mysqli_real_escape_string($conn, $_POST['id_mobil']);
    $tanggal_sewa = mysqli_real_escape_string($conn, $_POST['tanggal_sewa']);
    $tanggal_kembali = mysqli_real_escape_string($conn, $_POST['tanggal_kembali']);

    $start = new DateTime($tanggal_sewa);
    $end = new DateTime($tanggal_kembali);
    $selisih = $start->diff($end)->days;
    if ($selisih <= 0) $selisih = 1;

    $result = mysqli_query($conn, "SELECT harga_per_hari FROM mobil WHERE id_mobil='$id_mobil'");
    if ($row = mysqli_fetch_assoc($result)) {
        $harga_per_hari = $row['harga_per_hari'];
        $total_harga = $harga_per_hari * $selisih;

        if (isset($_POST['edit_mode'])) {
            $sql = "UPDATE rental SET id_pelanggan='$id_pelanggan', id_mobil='$id_mobil', tanggal_sewa='$tanggal_sewa', tanggal_kembali='$tanggal_kembali', total_harga='$total_harga' WHERE id_rental='$id_rental'";
            if (mysqli_query($conn, $sql)) {
                $success_message = "Data rental berhasil diperbarui.";
            } else {
                $error_message = "Gagal memperbarui: " . mysqli_error($conn);
            }
        } else {
            $check_query = "SELECT id_rental FROM rental WHERE id_rental='$id_rental'";
            $check_result = mysqli_query($conn, $check_query);
            if (mysqli_num_rows($check_result) > 0) {
                $error_message = "ID Rental '$id_rental' sudah digunakan. Gunakan ID lain.";
            } else {
                $sql = "INSERT INTO rental (id_rental, id_pelanggan, id_mobil, tanggal_sewa, tanggal_kembali, total_harga) 
                        VALUES ('$id_rental', '$id_pelanggan', '$id_mobil', '$tanggal_sewa', '$tanggal_kembali', $total_harga)";
                if (mysqli_query($conn, $sql)) {
                    mysqli_query($conn, "UPDATE mobil SET status='disewa' WHERE id_mobil='$id_mobil'");
                    $success_message = "Transaksi rental berhasil disimpan. Total harga: Rp " . number_format($total_harga, 0, ',', '.');
                } else {
                    $error_message = "Gagal menambahkan: " . mysqli_error($conn);
                }
            }
        }
    } else {
        $error_message = "Mobil dengan ID $id_mobil tidak ditemukan.";
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit_q = mysqli_query($conn, "SELECT * FROM rental WHERE id_rental='$id_edit'");
    $edit_data = mysqli_fetch_assoc($edit_q);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Rental</title>
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
            <h2 class="text-2xl font-bold text-gray-800 mb-6"><?= $edit_data ? 'Edit Rental' : 'Form Rental Mobil' ?></h2>
            <form method="POST" action="" class="space-y-4">
                <input type="hidden" name="edit_mode" value="<?= $edit_data ? '1' : '' ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Rental</label>
                    <input type="text" name="id_rental" required value="<?= $edit_data['id_rental'] ?? '' ?>" <?= $edit_data ? 'readonly' : '' ?>
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Pelanggan</label>
                    <select name="id_pelanggan" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">-- Pilih --</option>
                        <?php
                        $pelanggan = mysqli_query($conn, "SELECT * FROM pelanggan");
                        while ($p = mysqli_fetch_assoc($pelanggan)) {
                            $selected = ($edit_data && $edit_data['id_pelanggan'] == $p['id_pelanggan']) ? 'selected' : '';
                            echo "<option value='{$p['id_pelanggan']}' $selected>{$p['id_pelanggan']} - {$p['nama']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Mobil</label>
                    <select name="id_mobil" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">-- Pilih --</option>
                        <?php
                        $mobil = mysqli_query($conn, "SELECT * FROM mobil WHERE status='tersedia'"); // Only show available cars
                        while ($m = mysqli_fetch_assoc($mobil)) {
                            $selected = ($edit_data && $edit_data['id_mobil'] == $m['id_mobil']) ? 'selected' : '';
                            echo "<option value='{$m['id_mobil']}' $selected>{$m['id_mobil']} - {$m['merk']} {$m['model']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Sewa</label>
                    <input type="date" name="tanggal_sewa" required value="<?= $edit_data['tanggal_sewa'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali" required value="<?= $edit_data['tanggal_kembali'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <i class="fas fa-save mr-2"></i><?= $edit_data ? 'Update' : 'Sewa' ?>
                    </button>
                    <?php if ($edit_data): ?>
                        <a href="rental.php" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Data Transaksi Rental</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">ID Rental</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Pelanggan</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Mobil</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Tanggal Sewa</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Tanggal Kembali</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Total Harga</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $query = "
                            SELECT r.id_rental, p.nama AS nama_pelanggan, 
                                   CONCAT(m.merk, ' ', m.model) AS modal_mobil,
                                   r.tanggal_sewa, r.tanggal_kembali, r.total_harga
                            FROM rental r
                            JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan
                            JOIN mobil m ON r.id_mobil = m.id_mobil
                            ORDER BY r.tanggal_sewa DESC
                        ";
                        $result = mysqli_query($conn, $query);
                        if (mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                        ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['id_rental']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['modal_mobil']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['tanggal_sewa']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['tanggal_kembali']) ?></td>
                                    <td class="px-4 py-2">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                    <td class="px-4 py-2">
                                        <a href="?edit=<?= $row['id_rental'] ?>" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?hapus=<?= $row['id_rental'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td class="px-4 py-4 text-center" colspan="7">Belum ada data rental.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>