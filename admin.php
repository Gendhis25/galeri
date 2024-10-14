<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Inisialisasi variabel pencarian album dan foto
$search_album = isset($_GET['search_album']) ? $_GET['search_album'] : '';
$search_foto = isset($_GET['search_foto']) ? $_GET['search_foto'] : '';

// Query untuk mendapatkan semua album dengan pencarian
$sql_album = "SELECT * FROM album WHERE NamaAlbum LIKE ? OR AlbumID = ?";
$stmt_album = $conn->prepare($sql_album);
$search_album_wildcard = '%' . $search_album . '%';
$stmt_album->bind_param("ss", $search_album_wildcard, $search_album);
$stmt_album->execute();
$result_album = $stmt_album->get_result();


// Query untuk mendapatkan semua foto dengan pencarian dan menggabungkan tabel user untuk mengambil username
$sql_foto = "SELECT foto.*, album.NamaAlbum, user.Username 
             FROM foto 
             JOIN album ON foto.AlbumID = album.AlbumID 
             JOIN user ON foto.UserID = user.UserID
             WHERE (JudulFoto LIKE ? OR DeskripsiFoto LIKE ? OR FotoID LIKE ? OR user.Username LIKE ?)";
$stmt_foto = $conn->prepare($sql_foto);
$search_foto_wildcard = '%' . $search_foto . '%';
$stmt_foto->bind_param("ssss", $search_foto_wildcard, $search_foto_wildcard, $search_foto, $search_foto_wildcard);
$stmt_foto->execute();
$result_foto = $stmt_foto->get_result();



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
    <title>Admin - Kelola Album dan Foto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #6c63ff;
            text-align: center;
        }

        nav {
            text-align: right;
            margin-bottom: 20px;
        }

        a {
            color: #3498db;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: #e1e1e1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background-color: #f4f4f4;
        }

        th, td {
            text-align: left;
        }

        .btn-add, .btn-print {
            display: inline-block;
            background-color: #6c63ff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin-bottom: 20px;
        }

        .btn-add:hover, .btn-print:hover {
            background-color: #5a52cc;
        }

        .search-form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 80%;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        /* Media Print */
        @media print {
            nav, .btn-add, .btn-print, .search-form {
                display: none;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table, th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }

            body {
                color: #000;
                background-color: #fff;
            }

            .container {
                box-shadow: none;
            }

            h1, h2 {
                font-size: 18px;
                text-align: left;
                color: #000;
            }

            table, th, td {
                font-size: 12px;
            }

            th:nth-child(6), td:nth-child(6) {
                display: none;
            }

            body {
                margin: 0;
                padding: 0;
            }

            a {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin</h1>
        <nav>
            <a href="index.php"><i class="fas fa-home"></i></a> <!-- Ikon Beranda -->
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a> <!-- Ikon Logout -->
        </nav>

        <!-- Bagian Kelola Album -->
        <h2>Kelola Album</h2>
        <a href="tambah_album.php" class="btn-add">Tambah Album</a>
        <a href="#" class="btn-print" onclick="window.print()">Cetak</a>

        <!-- Form Pencarian Album -->
        <form action="" method="GET" class="search-form">
            <input type="text" name="search_album" placeholder="Cari album..." value="<?php echo htmlspecialchars($search_album); ?>">
            <input type="submit" value="Cari">
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Nama Album</th>
                <th>Deskripsi</th>
                <th>Tanggal Dibuat</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row_album = $result_album->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row_album['AlbumID']); ?></td>
                <td><?php echo htmlspecialchars($row_album['NamaAlbum']); ?></td>
                <td><?php echo htmlspecialchars($row_album['Deskripsi']); ?></td>
                <td><?php echo htmlspecialchars($row_album['TanggalDibuat']); ?></td>
                <td>
                    <a href="edit_album.php?id=<?php echo $row_album['AlbumID']; ?>">Edit</a> | 
                    <a href="hapus_album.php?id=<?php echo $row_album['AlbumID']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus album ini?');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <!-- Bagian Kelola Foto -->
        <h2>Kelola Foto</h2>
        <a href="upload.php" class="btn-add">Upload Foto</a>

        <!-- Form Pencarian Foto -->
        <form action="" method="GET" class="search-form">
            <input type="text" name="search_foto" placeholder="Cari foto..." value="<?php echo htmlspecialchars($search_foto); ?>">
            <input type="submit" value="Cari">
        </form>

        <table>
            <tr>
                <th>ID</th> 
                <th>User</th>
                <th>Foto</th> 
                <th>Judul Foto</th>
                <th>Deskripsi Foto</th>
                <th>Nama Album</th>
                <th>Tanggal Upload</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row_foto = $result_foto->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row_foto['FotoID']); ?></td>
                <td><?php echo htmlspecialchars($row_foto['Username']); ?></td>
                <td>
                    <img src="<?php echo htmlspecialchars($row_foto['LokasiFile']); ?>" alt="Foto" style="width:100px;height:auto;"> <!-- Corrected variable -->
                </td>
                <td><?php echo htmlspecialchars($row_foto['JudulFoto']); ?></td>
                <td><?php echo htmlspecialchars($row_foto['DeskripsiFoto']); ?></td>
                <td>
                <?php
                // Mendapatkan nama album berdasarkan AlbumID
                $album_id = $row_foto['AlbumID'];
                $album_sql = "SELECT NamaAlbum FROM album WHERE AlbumID = ?";
                $album_stmt = $conn->prepare($album_sql);
                $album_stmt->bind_param("i", $album_id);
                $album_stmt->execute();
                $album_result = $album_stmt->get_result();
                $album_data = $album_result->fetch_assoc();
                echo htmlspecialchars($album_data['NamaAlbum']);
                ?>
                </td>
                <td><?php echo htmlspecialchars($row_foto['TanggalUnggah']); ?></td>
                <td>
                    <a href="edit_foto.php?id=<?php echo $row_foto['FotoID']; ?>">Edit</a> | 
                    <a href="hapus.php?id=<?php echo $row_foto['FotoID']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus foto ini?');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

    </div>
</body>
</html>
