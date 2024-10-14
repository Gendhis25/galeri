<?php
$host = 'localhost'; // atau alamat host database Anda
$username = 'root'; // username database Anda
$password = ''; // password database Anda
$dbname = 'gallery'; // nama database Anda

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
