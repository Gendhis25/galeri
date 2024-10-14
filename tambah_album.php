<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_album = $_POST['nama_album'];
    $deskripsi = $_POST['deskripsi'];
    $user_id = $_SESSION['user_id'];

    // Query untuk menambahkan kolom TanggalDibuat dengan nilai tanggal saat ini (CURDATE())
    $sql = "INSERT INTO album (UserID, NamaAlbum, Deskripsi, TanggalDibuat) VALUES (?, ?, ?, CURDATE())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $nama_album, $deskripsi);

    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect ke halaman album setelah berhasil
        exit();
    } else {
        echo "Terjadi kesalahan saat menambahkan album.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Tambah Album</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f4f8;
        }

        h1 {
            text-align: center;
            color: #6c63ff;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #6c63ff;
        }

        input[type="text"],
        textarea,
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            resize: none;
        }

        input[type="submit"] {
            background-color: #85a2d6;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color:  #a7c1e5;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            color: #ffffff;
            font-size: 24px;
            cursor: pointer;
            background-color: #85a2d6;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }

        .close-btn:hover {
            background-color: #a7c1e5;
        }
    </style>
</head>
<body>
    <form action="" method="POST">
        <button type="button" class="close-btn" onclick="window.location.href='index.php'">&times;</button>

        <h1>Tambah Album</h1>

        <label for="nama_album">Nama Album:</label>
        <input type="text" id="nama_album" name="nama_album" required>

        <label for="deskripsi">Deskripsi:</label>
        <textarea id="deskripsi" name="deskripsi" required></textarea>

        <input type="submit" value="Tambah Album">
    </form>
</body>
</html>
