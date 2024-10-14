<?php
session_start();
include 'koneksi.php';

if (isset($_POST['foto_id']) && isset($_SESSION['username'])) {
    $foto_id = $_POST['foto_id'];
    $user_id = $_SESSION['username'];

    // Cek apakah pengguna sudah memberikan like
    $sql_check = "SELECT * FROM likefoto WHERE FotoID = ? AND UserID = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("is", $foto_id, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // Insert like baru
        $sql_insert = "INSERT INTO likes (FotoID, UserID) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("is", $foto_id, $user_id);
        $stmt_insert->execute();

        // Update jumlah like di foto
        $sql_update = "UPDATE foto SET like_count = like_count + 1 WHERE FotoID = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $foto_id);
        $stmt_update->execute();

        echo "Like berhasil ditambahkan!";
    } else {
        echo "Anda sudah memberikan like pada foto ini!";
    }
}
?>
