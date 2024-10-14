<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Cek apakah ada ID album yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Mengonversi ID ke integer untuk keamanan
    echo "ID Album: " . $id . "<br>"; // Debug: tampilkan ID album yang akan dihapus

    // Hapus album dari database
    $stmt = $conn->prepare("DELETE FROM album WHERE AlbumID = ?");
    $stmt->bind_param("i", $id);

    echo "Menjalankan query untuk menghapus album dengan ID: $id<br>"; // Debug

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Album berhasil dihapus.<br>"; // Debug
            header('Location: admin.php');
            exit();
        } else {
            echo "Album tidak ditemukan atau sudah dihapus sebelumnya.<br>"; // Debug
        }
    } else {
        echo "Gagal menghapus album: " . $stmt->error; // Tampilkan pesan kesalahan
    }
} else {
    echo "ID tidak valid. <a href='manage_album.php'>Kembali ke Kelola Album</a>";
}
?>
