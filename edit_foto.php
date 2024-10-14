<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Mengambil FotoID dari URL
$foto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Query untuk mendapatkan foto berdasarkan FotoID
$sql_foto = "SELECT * FROM foto WHERE FotoID = ?";
$stmt = $conn->prepare($sql_foto);
$stmt->bind_param("i", $foto_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Foto tidak ditemukan.";
    exit();
}

// Ambil data foto
$row_foto = $result->fetch_assoc();

// Proses form jika ada pengiriman data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $album_id = $_POST['album_id'];
    $user_id = $_SESSION['user_id']; // Ambil UserID dari session
    $lokasi_file = $row_foto['LokasiFile'];

    // Cek jika ada foto baru yang diupload
    if ($_FILES['foto']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['foto']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Cek apakah file gambar sebenarnya adalah gambar
        $check = getimagesize($_FILES['foto']['tmp_name']);
        if ($check === false) {
            echo "File bukan gambar.";
            $uploadOk = 0;
        }

        // Cek ukuran file
        if ($_FILES['foto']['size'] > 5000000) { // Maksimal 5MB
            echo "Maaf, ukuran file terlalu besar.";
            $uploadOk = 0;
        }

        // Cek jenis file
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "Hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
            $uploadOk = 0;
        }

        // Coba untuk mengupload file
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $lokasi_file = $target_file; // Update lokasi file jika berhasil
            } else {
                echo "Maaf, terjadi kesalahan saat mengupload file.";
            }
        }
    }

    // Update informasi foto
    $sql_update = "UPDATE foto SET JudulFoto = ?, DeskripsiFoto = ?, LokasiFile = ?, AlbumID = ?, UserID = ? WHERE FotoID = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssiii", $judul, $deskripsi, $lokasi_file, $album_id, $user_id, $foto_id);
    if ($stmt_update->execute()) {
        header("Location: admin.php"); // Redirect ke admin.php setelah update
        exit();
    } else {
        echo "Error: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit Foto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #6c63ff;
            text-align: center;
        }

        .btn {
            display: inline-block;
            background-color: #6c63ff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px 0;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #5a52cc;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="file"] {
            padding: 10px;
        }

        textarea {
            resize: none;
            height: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Foto</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="judul">Judul Foto:</label>
                <input type="text" name="judul" id="judul" value="<?php echo $row_foto['JudulFoto']; ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea name="deskripsi" id="deskripsi" required><?php echo $row_foto['DeskripsiFoto']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="foto">Upload Foto Baru (opsional):</label>
                <input type="file" name="foto" id="foto">
            </div>
            <input type="hidden" name="album_id" value="<?php echo $row_foto['AlbumID']; ?>">
            <button type="submit" class="btn">Simpan Perubahan</button>
        </form>
        <a href="admin.php?id=<?php echo $row_foto['FotoID']; ?>" class="btn">Kembali ke Admin</a>
    </div>
</body>
</html>
