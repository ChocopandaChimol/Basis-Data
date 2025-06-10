CREATE DATABASE rental_mobil;
USE rental_mobil;

CREATE TABLE pelanggan (
    id_pelanggan CHAR(5) PRIMARY KEY,
    nama VARCHAR(50),
    alamat VARCHAR(100),
    no_hp VARCHAR(15),
    email VARCHAR(50)
);

CREATE TABLE mobil (
    id_mobil CHAR(5) PRIMARY KEY,
    merk VARCHAR(30),
    model VARCHAR(30),
    tahun INT,
    no_plat VARCHAR(15),
    status ENUM('tersedia', 'disewa', 'servis') DEFAULT 'tersedia',
    harga_per_hari DECIMAL(10, 2)
);


CREATE TABLE rental (
    id_rental CHAR(5) PRIMARY KEY,
    id_pelanggan CHAR(5),
    id_mobil CHAR(5),
    tanggal_sewa DATE,
    tanggal_kembali DATE,
    total_harga DECIMAL(10, 2),
    status ENUM('disewa', 'selesai', 'dibatalkan') DEFAULT 'disewa',
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    FOREIGN KEY (id_mobil) REFERENCES mobil(id_mobil)
);


CREATE TABLE pengembalian (
    id_pengembalian CHAR(5) PRIMARY KEY,
    id_rental CHAR(5),
    tanggal_pengembalian DATE,
    denda DECIMAL(10, 2),
    FOREIGN KEY (id_rental) REFERENCES rental(id_rental)
);


CREATE TABLE pembayaran (
    id_pembayaran CHAR(5) PRIMARY KEY,
    id_rental CHAR(5),
    metode VARCHAR(30),
    jumlah DECIMAL(10, 2),
    tanggal DATE,
    FOREIGN KEY (id_rental) REFERENCES rental(id_rental)
);