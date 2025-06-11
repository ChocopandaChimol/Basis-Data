<?php
session_start();
$loggedIn = isset($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .nav-link::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background: #3b82f6;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .hamburger {
            display: none;
        }
        @media (max-width: 640px) {
            .hamburger {
                display: block;
            }
            .nav-menu {
                display: none;
            }
            .nav-menu.active {
                display: flex;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-gray-900 bg-opacity-90 shadow-lg sticky top-0 z-50 border-b-2 border-blue-500">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="text-white text-xl font-bold">
                    Rental System
                </div>

                <button class="hamburger text-white focus:outline-none sm:hidden">
                    <i class="fas fa-bars text-2xl"></i>
                </button>

                <div class="nav-menu hidden sm:flex space-x-4 sm:space-x-6 items-center flex-wrap">
                    <a href="tambah_pelanggan.php" class="nav-link text-white px-4 py-2 rounded-md hover:bg-gray-800 hover:text-blue-400 transition duration-300">Manajemen Pelanggan</a>
                    <a href="mobil.php" class="nav-link text-white px-4 py-2 rounded-md hover:bg-gray-800 hover:text-blue-400 transition duration-300">Manajemen Mobil</a>
                    <a href="rental.php" class="nav-link text-white px-4 py-2 rounded-md hover:bg-gray-800 hover:text-blue-400 transition duration-300">Manajemen Rental</a>
                    <a href="tanggal_pengembalian.php" class="nav-link text-white px-4 py-2 rounded-md hover:bg-gray-800 hover:text-blue-400 transition duration-300">Manajemen Pengembalian</a>
                    <a href="pembayaran.php" class="nav-link text-white px-4 py-2 rounded-md hover:bg-gray-800 hover:text-blue-400 transition duration-300">Manajemen Pembayaran</a>
                </div>
            </div>
            <div class="nav-menu hidden flex-col space-y-2 pb-4 sm:hidden">
                <a href="tambah_pelanggan.php" class="nav-link text-white px-4 py-2 rounded-md hover:bg-gray-800 hover:text-blue-400 transition duration-300">Manajemen Pelanggan</a>
                <a href="mobil.php" class="nav-link text-white px-4 py-2 rounded-md hover:bg-gray-800 hover:text-blue-400 transition duration-300">Manajemen Mobil</a>
                <a href="rental.php" class="nav-link text-white px-4 py-2 rounded-md hover:bg-gray-800 hover:text-blue-400 transition duration-300">Manajemen Rental</a>
                <a href="tanggal_pengembalian.php" class="nav-link text-white px-4 py-2 rounded-md hover:bg-gray-800 hover:text-blue-400 transition duration-300">Manajemen Pengembalian</a>
                <a href="pembayaran.php" class="nav-link text-white px-4 py-2 rounded-md hover:bg-gray-800 hover:text-blue-400 transition duration-300">Manajemen Pembayaran</a>
        </div>
    </nav>

    <script>
        document.querySelector('.hamburger').addEventListener('click', function() {
            const menu = document.querySelector('.nav-menu.sm\\:hidden');
            menu.classList.toggle('active');
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });
    </script>
</body>
</html>