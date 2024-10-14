<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Cek apakah ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Mengonversi ID ke integer untuk keamanan

    // Ambil lokasi file dari foto yang akan dihapus
    $stmt = $conn->prepare("SELECT LokasiFile FROM foto WHERE FotoID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($lokasi_file);
    $stmt->fetch();

    // Hapus data dari database
    $stmt = $conn->prepare("DELETE FROM foto WHERE FotoID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Hapus foto dari server jika ada
        if ($lokasi_file) {
            $foto_path = 'uploads/' . $lokasi_file; // Pastikan ini sesuai dengan path file foto
            if (file_exists($foto_path)) {
                unlink($foto_path); // Menghapus file foto dari server
            }
        }
        header('Location: admin.php'); // Redirect setelah sukses
        exit();
    } else {
        echo "Gagal menghapus foto.";
    }
} else {
    echo "ID tidak valid. <a href='manage_album.php'>Kembali ke Kelola Album</a>";
}
?>
