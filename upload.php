<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $album_id = $_POST['album_id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal_unggah = date('Y-m-d');

    // Menangani upload file
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);

    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        // Jika upload berhasil, simpan ke database
        $sql = "INSERT INTO foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFile, AlbumID, UserID) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $judul, $deskripsi, $tanggal_unggah, $target_file, $album_id, $_SESSION['user_id']);

        if ($stmt->execute()) {
            // Jika berhasil, redirect ke halaman yang sesuai
            if ($_SESSION['role'] == 'admin') {
                // Redirect ke admin.php jika login sebagai admin
                header("Location: admin.php?message=Foto berhasil diupload.");
            } else {
                // Redirect ke index.php jika login sebagai user
                header("Location: index.php?message=Foto berhasil diupload.");
            }
            exit();
        } else {
            echo "Terjadi kesalahan saat menyimpan data foto.";
        }
    } else {
        echo "Maaf, terjadi kesalahan saat mengupload foto.";
    }
}

// Mendapatkan daftar album
if ($_SESSION['role'] == 'admin') {
    // Jika admin, ambil semua album
    $sql = "SELECT * FROM album";
} else {
    // Jika user, ambil album yang dibuat oleh user lain dan album user sendiri
    $sql = "SELECT * FROM album WHERE UserID = ? OR UserID IN (SELECT DISTINCT UserID FROM album WHERE UserID != ?)";
}
$stmt = $conn->prepare($sql);
if ($_SESSION['role'] != 'admin') {
    $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Upload Foto</title>
    <style>
        /* style_upload.css */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f4f8; /* Warna latar belakang pastel */
        }

        h1 {
            text-align: center;
            color: #6c63ff; /* Warna teks pastel (ungu) */
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            color: #ffffff; /* Warna teks (putih) untuk kontras */
            font-size: 24px;
            cursor: pointer;
            background-color: #85a2d6; /* Warna yang selaras dengan tombol */
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
            background-color: #a7c1e5; /* Warna hover untuk close button */
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff; /* Warna latar belakang form */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative; /* Agar close button bisa diposisikan relatif terhadap form */
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #6c63ff; /* Warna label */
        }

        input[type="text"],
        input[type="file"],
        input[type="submit"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #85a2d6; /* Warna tombol submit (biru) */
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #a7c1e5; /* Warna hover tombol submit */
        }
    </style>
</head>
<body>
    <h1>Upload Foto</h1>

    <form action="upload.php" method="POST" enctype="multipart/form-data">
    <button type="button" class="close-btn" onclick="window.location.href='index.php'">&times;</button> <!-- Tanda Silang -->
    
    <label for="album">Pilih Album:</label>
    <select name="album_id" id="album" required>
        <?php while ($row = $result->fetch_assoc()): ?>
            <option value="<?php echo $row['AlbumID']; ?>"><?php echo $row['NamaAlbum']; ?></option>
        <?php endwhile; ?>
    </select>
    <br><br>

    <label for="judul">Judul Foto:</label>
    <input type="text" name="judul" id="judul" required>
    <br><br>

    <label for="deskripsi">Deskripsi Foto:</label>
    <textarea name="deskripsi" id="deskripsi" required></textarea>
    <br><br>

    <label for="foto">Pilih Foto:</label>
    <input type="file" name="foto" id="foto" accept="image/*" required>
    <br><br>

    <input type="submit" value="Upload Foto">
</form>

    <?php
    // Menampilkan pesan tanpa htmlspecialchars
    if (isset($_GET['message'])) {
        echo '<div style="color: green; text-align: center;">' . $_GET['message'] . '</div>';
    }
    ?>
</body>
</html>
