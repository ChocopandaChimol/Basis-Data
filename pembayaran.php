<?php
include './connection/koneksi.php';
include './navbar/navbar.php';
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pembayaran WHERE id_pembayaran='$id'");
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4' role='alert'>Data pembayaran dengan ID $id berhasil dihapus.</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pembayaran = $_POST['id_pembayaran'];
    $id_rental = $_POST['id_rental'];
    $metode = $_POST['metode'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];

    if (isset($_POST['edit_mode'])) {
        mysqli_query($conn, "UPDATE pembayaran SET id_rental='$id_rental', metode='$metode', jumlah='$jumlah', tanggal='$tanggal' WHERE id_pembayaran='$id_pembayaran'");
        echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4' role='alert'>Data pembayaran berhasil diperbarui.</div>";
    } else {
        mysqli_query($conn, "INSERT INTO pembayaran (id_pembayaran, id_rental, metode, jumlah, tanggal) VALUES ('$id_pembayaran', '$id_rental', '$metode', $jumlah, '$tanggal')");
        echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4' role='alert'>Pembayaran berhasil dicatat.</div>";
    }
}
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $edit_q = mysqli_query($conn, "SELECT * FROM pembayaran WHERE id_pembayaran='$id_edit'");
    $edit_data = mysqli_fetch_assoc($edit_q);
}
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'tanggal';
$sort_order = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'ASC' : 'DESC';
$filter_id_pembayaran = isset($_GET['filter_id_pembayaran']) ? $_GET['filter_id_pembayaran'] : '';
$filter_id_rental = isset($_GET['filter_id_rental']) ? $_GET['filter_id_rental'] : '';
$filter_metode = isset($_GET['filter_metode']) ? $_GET['filter_metode'] : '';
$filter_jumlah = isset($_GET['filter_jumlah']) ? $_GET['filter_jumlah'] : '';
$filter_tanggal = isset($_GET['filter_tanggal']) ? $_GET['filter_tanggal'] : '';

$where_clauses = [];
if ($filter_id_pembayaran) {
    $where_clauses[] = "id_pembayaran LIKE '%$filter_id_pembayaran%'";
}
if ($filter_id_rental) {
    $where_clauses[] = "id_rental LIKE '%$filter_id_rental%'";
}
if ($filter_metode) {
    $where_clauses[] = "metode LIKE '%$filter_metode%'";
}
if ($filter_jumlah) {
    $where_clauses[] = "jumlah LIKE '%$filter_jumlah%'";
}
if ($filter_tanggal) {
    $where_clauses[] = "tanggal LIKE '%$filter_tanggal%'";
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
$query = "SELECT * FROM pembayaran $where_sql ORDER BY $sort_column $sort_order";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-4 sm:p-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Form Pembayaran</h2>
            <form method="POST" action="" class="space-y-4">
                <input type="hidden" name="edit_mode" value="<?= $edit_data ? '1' : '' ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Pembayaran</label>
                    <input type="text" name="id_pembayaran" required value="<?= $edit_data['id_pembayaran'] ?? '' ?>" 
                           <?= $edit_data ? 'readonly' : '' ?> 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Pilih Rental</label>
                    <select name="id_rental" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">-- Pilih --</option>
                        <?php
                        $rental = mysqli_query($conn, "SELECT * FROM rental");
                        while ($r = mysqli_fetch_assoc($rental)) {
                            $selected = ($edit_data && $edit_data['id_rental'] == $r['id_rental']) ? 'selected' : '';
                            echo "<option value='{$r['id_rental']}' $selected>{$r['id_rental']} - Pelanggan: {$r['id_pelanggan']} - Mobil: {$r['id_mobil']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                    <input type="text" name="metode" required value="<?= $edit_data['metode'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                    <input type="number" step="0.01" name="jumlah" required value="<?= $edit_data['jumlah'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" required value="<?= $edit_data['tanggal'] ?? '' ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <i class="fas fa-save mr-2"></i><?= $edit_data ? 'Update' : 'Simpan Pembayaran' ?>
                    </button>
                    <?php if ($edit_data): ?>
                        <a href="pembayaran.php" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Batal Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Filter Data Pembayaran</h2>
            <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Pembayaran</label>
                    <input type="text" name="filter_id_pembayaran" value="<?= htmlspecialchars($filter_id_pembayaran) ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Rental</label>
                    <input type="text" name="filter_id_rental" value="<?= htmlspecialchars($filter_id_rental) ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Metode</label>
                    <input type="text" name="filter_metode" value="<?= htmlspecialchars($filter_metode) ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                    <input type="text" name="filter_jumlah" value="<?= htmlspecialchars($filter_jumlah) ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="filter_tanggal" value="<?= htmlspecialchars($filter_tanggal) ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="pembayaran.php" class="ml-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Reset</a>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Data Pembayaran</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <?php
                            $columns = ['id_pembayaran' => 'ID Pembayaran', 'id_rental' => 'ID Rental', 'metode' => 'Metode', 'jumlah' => 'Jumlah', 'tanggal' => 'Tanggal'];
                            foreach ($columns as $col => $label) {
                                $new_order = ($sort_column == $col && $sort_order == 'ASC') ? 'desc' : 'asc';
                                $icon = ($sort_column == $col) ? ($sort_order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
                                echo "<th class='px-4 py-3 text-left text-sm font-medium uppercase'>
                                        <a href='?sort=$col&order=$new_order&filter_id_pembayaran=" . urlencode($filter_id_pembayaran) . "&filter_id_rental=" . urlencode($filter_id_rental) . "&filter_metode=" . urlencode($filter_metode) . "&filter_jumlah=" . urlencode($filter_jumlah) . "&filter_tanggal=" . urlencode($filter_tanggal) . "' class='flex items-center'>
                                            $label <i class='fas $icon ml-2'></i>
                                        </a>
                                      </th>";
                            }
                            ?>
                            <th class="px-4 py-3 text-left text-sm font-medium uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr class='hover:bg-gray-50'>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['id_pembayaran']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['id_rental']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['metode']) . "</td>";
                                echo "<td class='px-4 py-2'>Rp " . number_format($row['jumlah'], 0, ',', '.') . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['tanggal']) . "</td>";
                                echo "<td class='px-4 py-2'>
                                        <a href='?edit={$row['id_pembayaran']}' class='text-blue-600 hover:text-blue-800 mr-2'><i class='fas fa-edit'></i> Edit</a>
                                        <a href='?hapus={$row['id_pembayaran']}' onclick=\"return confirm('Yakin ingin menghapus data ini?')\" class='text-red-600 hover:text-red-800'><i class='fas fa-trash'></i> Hapus</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td class='px-4 py-4 text-center' colspan='6'>Belum ada data pembayaran.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>