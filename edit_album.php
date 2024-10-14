<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Cek apakah ada parameter id yang dikirim
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$album_id = $_GET['id'];

// Query untuk mendapatkan album berdasarkan AlbumID
$sql_album = "SELECT * FROM album WHERE AlbumID = ?";
$stmt = $conn->prepare($sql_album);
$stmt->bind_param("i", $album_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Album tidak ditemukan.";
    exit();
}

$row_album = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_album = $_POST['nama_album'];
    $deskripsi = $_POST['deskripsi'];

    // Update album di database
    $sql_update = "UPDATE album SET NamaAlbum = ?, Deskripsi = ? WHERE AlbumID = ?";
    $update_stmt = $conn->prepare($sql_update);
    $update_stmt->bind_param("ssi", $nama_album, $deskripsi, $album_id);
    
    if ($update_stmt->execute()) {
        header("Location: admin.php"); // Kembali ke halaman admin
        exit();
    } else {
        echo "Terjadi kesalahan saat mengupdate album.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit Album</title>
    <style>
        /* style.css */

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    width: 100%;
}

h1 {
    text-align: center;
    color: #333;
}

nav a {
    display: inline-block;
    margin-bottom: 15px;
    text-decoration: none;
    color: #3498db;
    font-weight: bold;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

input[type="text"],
textarea {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 15px;
    font-size: 16px;
}

textarea {
    resize: vertical;
    min-height: 100px;
}

input[type="submit"] {
    padding: 10px;
    border: none;
    background-color: #3498db;
    color: white;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
    background-color: #2980b9;
}

@media (max-width: 600px) {
    .container {
        padding: 15px;
        width: 90%;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Album</h1>
        <nav>
            <a href="admin.php">Kembali ke Admin</a>
        </nav>
        <form method="POST">
            <label for="nama_album">Nama Album:</label><br>
            <input type="text" id="nama_album" name="nama_album" value="<?php echo isset($row_album['NamaAlbum']) ? htmlspecialchars($row_album['NamaAlbum']) : ''; ?>" required><br><br>

            <label for="deskripsi">Deskripsi:</label><br>
            <textarea id="deskripsi" name="deskripsi" required><?php echo isset($row_album['Deskripsi']) ? htmlspecialchars($row_album['Deskripsi']) : ''; ?></textarea><br><br>

            <input type="submit" value="Update Album">
        </form>
    </div>
</body>
</html>
