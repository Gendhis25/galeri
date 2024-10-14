<?php
session_start();
include 'koneksi.php';

// Ambil daftar album
$sql_album = "SELECT * FROM album";
$result_album = $conn->query($sql_album);

// Ambil daftar foto untuk ditampilkan di galeri
$sql_foto = "SELECT * FROM foto ORDER BY TanggalUnggah DESC";
$result_foto = $conn->query($sql_foto);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
    <title>Galeri Foto</title>
    <style>
        /* style_index.css */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #f0f4f8, #e0e7ff); /* Gradien lembut sebagai latar belakang */
            color: #333;
        }

        h1, h2 {
            text-align: center;
            color: #2d2e83; /* Warna teks yang lebih gelap untuk kontras */
            margin: 20px 0;
            font-weight: 700;
        }

        nav {
            background: linear-gradient(45deg, #2d2e83, #6f73ff); /* Gradien ungu untuk navbar */
            color: white;
            padding: 20px;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-title {
            font-size: 1.5em;
            font-weight: bold;
            color: white;
            margin-left: 20px;
        }

        .nav-icons {
            margin-right: 20px;
        }

        .nav-icons a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-size: 1.2em;
            transition: color 0.3s, transform 0.3s;
        }

        .nav-icons a:hover {
            color: #ddd;
            transform: scale(1.1);
        }

        /* Daftar Album */
        ul {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 0;
            max-width: 1200px;
            margin: 40px auto;
        }

        ul li {
            background: #fff;
            margin: 15px;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: 250px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            position: relative;
        }

        ul li:hover {
            transform: scale(1.08);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        ul li strong {
            font-size: 1.3em;
            color: #6c63ff;
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
        }

        ul li span {
            font-style: italic;
            color: #555;
            margin-bottom: 20px;
            display: block;
        }

        ul li a {
            display: inline-block;
            color: #6c63ff;
            text-decoration: none;
            border: 2px solid #6c63ff;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: background-color 0.3s, color 0.3s;
        }

        ul li a:hover {
            background-color: #6c63ff;
            color: white;
        }

        /* Galeri Foto */
        .gallery {
            max-width: 1200px;
            margin: 20px auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .gallery-item {
            margin: 10px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover {
            transform: scale(1.05);
        }

        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .gallery-item p {
            text-align: center;
            padding: 10px;
            background-color: #fff;
            color: #333;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            ul {
                flex-direction: column;
                align-items: center;
            }

            ul li {
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
<nav>
    <div class="nav-title">Gallery Foto</div> <!-- Judul di pojok kiri -->
    <div class="nav-icons">
        <a href="index.php"><i class="fas fa-home"></i></a> <!-- Ikon Beranda -->
        <a href="index.php#galeri-foto"><i class="fas fa-images"></i></a> <!-- Ikon Galeri Foto -->
        <?php if (isset($_SESSION['username'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?> <!-- Jika admin -->
                <a href="admin.php"><i class="fas fa-cogs"></i></a> <!-- Ikon Kelola -->
                <a href="upload.php"><i class="fas fa-upload"></i></a> <!-- Ikon Upload -->
                <a href="tambah_album.php"><i class="fas fa-folder-plus"></i></a> <!-- Ikon Buat Album -->
            <?php else: ?> <!-- Jika user -->
                <a href="upload.php"><i class="fas fa-upload"></i></a> <!-- Ikon Upload -->
                <a href="tambah_album.php"><i class="fas fa-folder-plus"></i></a> <!-- Ikon Buat Album -->
            <?php endif; ?>
            <!-- Tampilkan Nama Akun yang Login -->
            <span style="margin-left: 20px; font-weight: bold;"> <?php echo $_SESSION['username']; ?></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a> <!-- Ikon Logout -->
        <?php else: ?>
            <a href="register.php"><i class="fas fa-user-plus"></i></a> <!-- Ikon Daftar -->
        <?php endif; ?>
    </div>
</nav>


    <h2>Daftar Album</h2>
    <ul>
        <?php while ($row_album = $result_album->fetch_assoc()): ?>
            <li>
                <strong><?php echo $row_album['NamaAlbum']; ?></strong> - <?php echo $row_album['Deskripsi']; ?>
                <span>(<?php echo $row_album['TanggalDibuat']; ?>)</span>
                <a href="view_album.php?id=<?php echo $row_album['AlbumID']; ?>">Lihat Foto</a>
            </li>
        <?php endwhile; ?>
    </ul>

    <h2 id="galeri-foto">Foto Hasil Unggahan</h2>
    <div class="gallery">
        <?php while ($row_foto = $result_foto->fetch_assoc()): ?>
            <div class="gallery-item">
                <?php
                $image_path =  $row_foto['LokasiFile'];
                if (file_exists($image_path)): ?>
                    <img src="<?php echo $image_path; ?>" alt="<?php echo $row_foto['DeskripsiFoto']; ?>">
                <?php else: ?>
                    <p>Gambar tidak ditemukan: <?php echo $image_path; ?></p>
                <?php endif; ?>
                <p><?php echo $row_foto['DeskripsiFoto']; ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
