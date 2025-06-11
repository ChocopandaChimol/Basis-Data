<?php
include './connection/koneksi.php';
include './navbar/navbar.php';

$error_message = '';
$success_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan'])) {
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST["id_pelanggan"]);
    $nama = mysqli_real_escape_string($conn, $_POST["nama"]);
    $alamat = mysqli_real_escape_string($conn, $_POST["alamat"]);
    $no_hp = mysqli_real_escape_string($conn, $_POST["no_hp"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    $check_query = "SELECT id_pelanggan FROM pelanggan WHERE id_pelanggan='$id_pelanggan'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "ID Pelanggan '$id_pelanggan' sudah digunakan. Gunakan ID lain.";
    } else {
        $sql = "INSERT INTO pelanggan (id_pelanggan, nama, alamat, no_hp, email)
                VALUES ('$id_pelanggan', '$nama', '$alamat', '$no_hp', '$email')";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Data pelanggan berhasil ditambahkan.";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}

if (isset($_POST['update'])) {
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST["id_pelanggan"]);
    $nama = mysqli_real_escape_string($conn, $_POST["nama"]);
    $alamat = mysqli_real_escape_string($conn, $_POST["alamat"]);
    $no_hp = mysqli_real_escape_string($conn, $_POST["no_hp"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    $sql = "UPDATE pelanggan SET nama='$nama', alamat='$alamat', no_hp='$no_hp', email='$email'
            WHERE id_pelanggan='$id_pelanggan'";

    if (mysqli_query($conn, $sql)) {
        $success_message = "Data pelanggan berhasil diperbarui.";
    } else {
        $error_message = "Gagal update: " . mysqli_error($conn);
    }
}

if (isset($_GET['delete'])) {
    $id_delete = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM pelanggan WHERE id_pelanggan='$id_delete'");
    $success_message = "Data pelanggan berhasil dihapus.";
}
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM pelanggan WHERE id_pelanggan='$id_edit'");
    $edit_data = mysqli_fetch_assoc($res);
}

$query = "SELECT * FROM pelanggan ORDER BY id_pelanggan ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pelanggan</title>
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
            <h2 class="text-2xl font-bold text-gray-800 mb-6"><?= $edit_data ? 'Edit Pelanggan' : 'Tambah Pelanggan' ?></h2>
            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Pelanggan</label>
                    <input type="text" name="id_pelanggan" required value="<?= $edit_data['id_pelanggan'] ?? '' ?>" <?= $edit_data ? 'readonly' : '' ?>
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="nama" required value="<?= $edit_data['nama'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"><?= $edit_data['alamat'] ?? '' ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">No HP</label>
                    <input type="text" name="no_hp" required value="<?= $edit_data['no_hp'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required value="<?= $edit_data['email'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="<?= $edit_data ? 'update' : 'simpan' ?>" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <i class="fas fa-save mr-2"></i><?= $edit_data ? 'Update' : 'Simpan' ?>
                    </button>
                    <?php if ($edit_data): ?>
                        <a href="tambah_pelanggan.php" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Data Pelanggan</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">ID Pelanggan</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Nama</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Alamat</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">No HP</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['id_pelanggan']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['alamat']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['no_hp']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['email']) ?></td>
                                    <td class="px-4 py-2">
                                        <a href="?edit=<?= $row['id_pelanggan'] ?>" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="?delete=<?= $row['id_pelanggan'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td class="px-4 py-4 text-center" colspan="6">Belum ada data pelanggan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>