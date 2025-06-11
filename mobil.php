<?php
include './connection/koneksi.php';
include './navbar/navbar.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah'])) {
    $id_mobil = mysqli_real_escape_string($conn, $_POST['id_mobil']);
    $merk = mysqli_real_escape_string($conn, $_POST['merk']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun']);
    $no_plat = mysqli_real_escape_string($conn, $_POST['no_plat']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga_per_hari']);

    $check_query = "SELECT id_mobil FROM mobil WHERE id_mobil='$id_mobil'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "ID Mobil '$id_mobil' sudah digunakan. Gunakan ID lain.";
    } else {
        $sql = "INSERT INTO mobil (id_mobil, merk, model, tahun, no_plat, status, harga_per_hari)
                VALUES ('$id_mobil', '$merk', '$model', $tahun, '$no_plat', '$status', $harga)";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Mobil berhasil ditambahkan.";
        } else {
            $error_message = "Gagal menambahkan: " . mysqli_error($conn);
        }
    }
}

if (isset($_POST['update_status'])) {
    $id_mobil = mysqli_real_escape_string($conn, $_POST['id_mobil_update']);
    $status = mysqli_real_escape_string($conn, $_POST['status_baru']);

    $sql = "UPDATE mobil SET status = '$status' WHERE id_mobil = '$id_mobil'";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Status mobil berhasil diupdate.";
    } else {
        $error_message = "Gagal update status: " . mysqli_error($conn);
    }
}

if (isset($_POST['update_mobil'])) {
    $id_mobil = mysqli_real_escape_string($conn, $_POST['id_mobil']);
    $merk = mysqli_real_escape_string($conn, $_POST['merk']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun']);
    $no_plat = mysqli_real_escape_string($conn, $_POST['no_plat']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga_per_hari']);

    $sql = "UPDATE mobil SET merk='$merk', model='$model', tahun=$tahun, no_plat='$no_plat', status='$status', harga_per_hari=$harga WHERE id_mobil='$id_mobil'";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Data mobil berhasil diubah.";
    } else {
        $error_message = "Gagal mengubah: " . mysqli_error($conn);
    }
}

if (isset($_GET['delete'])) {
    $id_mobil = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM mobil WHERE id_mobil='$id_mobil'");
    $success_message = "Data mobil dengan ID $id_mobil berhasil dihapus.";
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id_mobil = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM mobil WHERE id_mobil='$id_mobil'");
    $edit_data = mysqli_fetch_assoc($res);
}

$result = mysqli_query($conn, "SELECT * FROM mobil ORDER BY tahun DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Mobil</title>
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
            <h2 class="text-2xl font-bold text-gray-800 mb-6"><?= $edit_data ? 'Edit Data Mobil' : 'Tambah Mobil' ?></h2>
            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Mobil</label>
                    <input type="text" name="id_mobil" required value="<?= $edit_data['id_mobil'] ?? '' ?>" <?= $edit_data ? 'readonly' : '' ?>
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Merk</label>
                    <input type="text" name="merk" required value="<?= $edit_data['merk'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Model</label>
                    <input type="text" name="model" required value="<?= $edit_data['model'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun</label>
                    <input type="number" name="tahun" required value="<?= $edit_data['tahun'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">No Plat</label>
                    <input type="text" name="no_plat" required value="<?= $edit_data['no_plat'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="tersedia" <?= isset($edit_data['status']) && $edit_data['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                        <option value="disewa" <?= isset($edit_data['status']) && $edit_data['status'] == 'disewa' ? 'selected' : '' ?>>Disewa</option>
                        <option value="servis" <?= isset($edit_data['status']) && $edit_data['status'] == 'servis' ? 'selected' : '' ?>>Servis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Harga per Hari</label>
                    <input type="number" step="0.01" name="harga_per_hari" required value="<?= $edit_data['harga_per_hari'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="<?= $edit_data ? 'update_mobil' : 'tambah' ?>" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <i class="fas fa-save mr-2"></i><?= $edit_data ? 'Update Mobil' : 'Tambah Mobil' ?>
                    </button>
                    <?php if ($edit_data): ?>
                        <a href="mobil.php" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Update Status Mobil</h2>
            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Mobil</label>
                    <input type="text" name="id_mobil_update" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status Baru</label>
                    <select name="status_baru" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="tersedia">Tersedia</option>
                        <option value="disewa">Disewa</option>
                        <option value="servis">Servis</option>
                    </select>
                </div>
                <div>
                    <button type="submit" name="update_status" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <i class="fas fa-sync-alt mr-2"></i>Update Status
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Data Mobil</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">ID Mobil</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Merk</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Model</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Tahun</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">No Plat</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Harga per Hari</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['id_mobil']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['merk']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['model']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['tahun']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['no_plat']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['status']) ?></td>
                                    <td class="px-4 py-2">Rp <?= number_format($row['harga_per_hari'], 0, ',', '.') ?></td>
                                    <td class="px-4 py-2">
                                        <a href="?edit=<?= $row['id_mobil'] ?>" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?delete=<?= $row['id_mobil'] ?>" onclick="return confirm('Yakin ingin menghapus mobil ini?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td class="px-4 py-4 text-center" colspan="8">Belum ada data mobil.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>