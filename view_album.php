<?php
session_start();
include 'koneksi.php';

// Periksa apakah parameter 'id' ada di URL
if (!isset($_GET['id'])) {
    echo "Album ID tidak ditemukan.";
    exit(); // Hentikan eksekusi jika tidak ada ID album
}

$album_id = $_GET['id'];

// Ambil informasi album berdasarkan AlbumID
$sql_album = "SELECT * FROM album WHERE AlbumID = ?";
$stmt_album = $conn->prepare($sql_album);
$stmt_album->bind_param("i", $album_id);
$stmt_album->execute();
$album_info = $stmt_album->get_result()->fetch_assoc();

// Cek apakah data album ada
if ($album_info === null) {
    echo "Album tidak ditemukan.";
    exit(); // Hentikan eksekusi jika album tidak ditemukan
}

// Ambil foto yang ada dalam album
$sql = "SELECT f.*, u.Username, f.TanggalUnggah FROM foto f JOIN user u ON f.UserID = u.UserID WHERE f.AlbumID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $album_id);
$stmt->execute();
$result = $stmt->get_result();

// Proses tombol like
if (isset($_POST['like'])) {
    if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
        $foto_id = $_POST['foto_id'];
        $user_id = $_SESSION['user_id'];

        // Cek apakah sudah di-like
        $sql_check_like = "SELECT * FROM likefoto WHERE FotoID = ? AND UserID = ?";
        $stmt_check_like = $conn->prepare($sql_check_like);
        $stmt_check_like->bind_param("ii", $foto_id, $user_id);
        $stmt_check_like->execute();
        $check_result = $stmt_check_like->get_result();

        if ($check_result->num_rows === 0) {
            // Jika belum pernah di-like, tambahkan ke database
            $sql_like = "INSERT INTO likefoto (FotoID, UserID) VALUES (?, ?)";
            $stmt_like = $conn->prepare($sql_like);
            $stmt_like->bind_param("ii", $foto_id, $user_id);
            $stmt_like->execute();
        } else {
            // Jika sudah di-like, hapus dari database
            $sql_unlike = "DELETE FROM likefoto WHERE FotoID = ? AND UserID = ?";
            $stmt_unlike = $conn->prepare($sql_unlike);
            $stmt_unlike->bind_param("ii", $foto_id, $user_id);
            $stmt_unlike->execute();
        }

        // Redirect untuk menghindari pengiriman data ulang
        header("Location: view_album.php?id=" . $album_id);
        exit();
    } else {
        echo "Anda harus login untuk menyukai foto.";
    }
}

// Proses pengiriman komentar
if (isset($_POST['komentar'])) {
    if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
        $foto_id = $_POST['foto_id'];
        $user_id = $_SESSION['user_id'];
        $komentar = $_POST['komentar'];

        // Simpan komentar ke database
        $sql_insert_comment = "INSERT INTO komentarfoto (FotoID, UserID, IsiKomentar) VALUES (?, ?, ?)";
        $stmt_insert_comment = $conn->prepare($sql_insert_comment);
        $stmt_insert_comment->bind_param("iis", $foto_id, $user_id, $komentar);
        $stmt_insert_comment->execute();

        // Redirect untuk menghindari pengiriman data ulang
        header("Location: view_album.php?id=" . $album_id);
        exit();
    } else {
        echo "Anda harus login untuk mengirim komentar.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
    <title><?php echo isset($album_info['NamaAlbum']) ? $album_info['NamaAlbum'] : 'Album Tidak Ditemukan'; ?> - Galeri Foto</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
}

nav {
            background: linear-gradient(45deg, #2d2e83, #6f73ff); /* Gradien ungu untuk navbar */
            color: white;
            padding: 20px;
            position: relative; /* Untuk pengaturan posisi ikon di kanan */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Bayangan untuk navbar */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-title {
            font-size: 1.5em;
            font-weight: bold;
            color: white;
            margin-left: 20px;
        }

        .nav-icons {
            margin-right: 20px; /* Menjaga jarak ikon dari pojok kanan */
        }

        .nav-icons a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-size: 1.2em; /* Ukuran ikon */
            transition: color 0.3s, transform 0.3s;
        }

        .nav-icons a:hover {
            color: #ddd; /* Warna hover ikon */
            transform: scale(1.1); /* Efek hover pada ikon */
        }

.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 0 20px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #333;
}

.album-description {
    text-align: center;
    color: #777;
}

.photo-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

.card {
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    text-align: center;
    padding: 15px;
}

.card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
}

.card strong {
    font-size: 18px;
    color: #333;
    margin-bottom: 10px;
    display: block;
}

.card p {
    color: #555;
    font-size: 14px;
    margin: 10px 0;
}

.uploaded-by {
    font-size: 12px;
    color: #999;
}

.like-comment-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
}

.like-btn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
}

.like-btn:hover, .edit-btn:hover {
    color: red;
}

.liked {
    color: red;
}

.like-count, .show-comments {
    font-size: 14px;
    color: #555;
    cursor: pointer;
}

.komentar-container {
    display: none;
    margin-top: 20px;
    text-align: left;
}

.komentar-container h4 {
    margin-bottom: 10px;
    color: #333;
}

.komentar {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
    margin-bottom: 10px;
}

.komentar strong {
    color: #333;
}

textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-top: 10px;
    font-size: 14px;
    color: #333;
    resize: none;
}

textarea:focus {
    outline: none;
    border-color: #555;
}

button[type="submit"] {
    background-color: #28a745;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}

button[type="submit"]:hover {
    background-color: #218838;
}

.edit-btn {
    font-size: 16px;
    color: #555;
    text-decoration: none;
    cursor: pointer;
}
@media print {
            .nav-title,
            .nav-icons,
            .btn-kembali {
                display: none; /* Sembunyikan elemen-elemen ini saat mencetak */
            }
            .card {
                break-inside: avoid; /* Mencegah pemecahan kartu foto saat mencetak */
            }
        }

    </style>
    <script>
        function toggleComments(fotoId) {
            const commentsContainer = document.getElementById('comments-' + fotoId);
            if (commentsContainer.style.display === 'none' || commentsContainer.style.display === '') {
                commentsContainer.style.display = 'block'; // Tampilkan komentar
            } else {
                commentsContainer.style.display = 'none'; // Sembunyikan komentar
            }
        }
    </script>
</head>
<body>
    <nav>
        <div class="nav-title">Gallery Foto</div>
        <div class="nav-icons">
            <a href="index.php"><i class="fas fa-home"></i></a>
            <?php if (isset($_SESSION['username'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php"><i class="fas fa-cogs"></i></a>
                    <a href="upload.php"><i class="fas fa-upload"></i></a>
                    <a href="tambah_album.php"><i class="fas fa-folder-plus"></i></a>
                <?php else: ?>
                    <a href="upload.php"><i class="fas fa-upload"></i></a>
                <?php endif; ?>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="register.php"><i class="fas fa-user-plus"></i></a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container">
        <h1><?php echo isset($album_info['NamaAlbum']) ? $album_info['NamaAlbum'] : 'Album Tidak Ada'; ?></h1>
        <p class="album-description"><?php echo isset($album_info['Deskripsi']) ? $album_info['Deskripsi'] : ''; ?></p>

        <div class="photo-gallery">
            <?php while ($foto = $result->fetch_assoc()): ?>
                <div class="card">
                    <strong><?php echo $foto['JudulFoto']; ?></strong>
                    <img src="<?php echo $foto['LokasiFile']; ?>" alt="<?php echo $foto['JudulFoto']; ?>">
                    <p><?php echo $foto['DeskripsiFoto']; ?></p>
                    <div class="uploaded-by">Diupload oleh: <?php echo $foto['Username']; ?> pada <?php echo date("d M Y", strtotime($foto['TanggalUnggah'])); ?></div>

                    <div class="like-comment-container">

                    <!-- Tombol cetak dengan mengarahkan ke cetak_foto.php -->
                    <a href="cetak.php?id=<?php echo $foto['FotoID']; ?>" class="print-btn">🖨️</a>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="post" action="">
                                <input type="hidden" name="foto_id" value="<?php echo $foto['FotoID']; ?>">
                                <button type="submit" name="like" class="like-btn <?php echo ($conn->query("SELECT * FROM likefoto WHERE FotoID = {$foto['FotoID']} AND UserID = {$_SESSION['user_id']}")->num_rows > 0) ? 'liked' : ''; ?>">❤️</button>
                            </form>
                        <?php endif; ?>
                        <span class="like-count">
                            <?php
                            $like_count_result = $conn->query("SELECT COUNT(*) AS like_count FROM likefoto WHERE FotoID = {$foto['FotoID']}");
                            $like_count = $like_count_result->fetch_assoc()['like_count'];
                            echo $like_count . ' Likes';
                            ?>
                        </span>
                        <span class="show-comments" onclick="toggleComments('<?php echo $foto['FotoID']; ?>')">🗨️ Komentar</span>

                        <!-- Cek visibilitas tombol edit -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php 
                            // Cek apakah pengguna adalah admin atau pengunggah
                            $is_admin = ($_SESSION['role'] === 'admin');
                            $is_uploader = ($_SESSION['user_id'] === $foto['UserID']);
                            if ($is_admin || $is_uploader): ?>
                                <a href="edit_user.php?id=<?php echo $foto['FotoID']; ?>" class="edit-btn">✏️</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="komentar-container" id="comments-<?php echo $foto['FotoID']; ?>">
                        <h4>Komentar:</h4>
                        <?php
                        $sql_komentar = "SELECT k.*, u.Username FROM komentarfoto k JOIN user u ON k.UserID = u.UserID WHERE k.FotoID = ?";
                        $stmt_komentar = $conn->prepare($sql_komentar);
                        $stmt_komentar->bind_param("i", $foto['FotoID']);
                        $stmt_komentar->execute();
                        $result_komentar = $stmt_komentar->get_result();
                        while ($komentar = $result_komentar->fetch_assoc()):
                        ?>
                            <div class="komentar">
                                <strong><?php echo $komentar['Username']; ?>:</strong>
                                <p><?php echo $komentar['IsiKomentar']; ?></p>
                            </div>
                        <?php endwhile; ?>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin'): ?>
                            <form method="post" action="">
                                <input type="hidden" name="foto_id" value="<?php echo $foto['FotoID']; ?>">
                                <textarea name="komentar" placeholder="Tulis komentar..." required></textarea>
                                <button type="submit">Kirim</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
<script>
    function printPhoto(fotoId) {
        // Ambil elemen gambar berdasarkan FotoID
        var fotoCard = document.querySelector('div.card img[src$="' + fotoId + '"]');

        if (fotoCard) {
            // Buat jendela baru untuk mencetak
            var newWindow = window.open("", "", "width=600,height=400");
            newWindow.document.write("<html><head><title>Cetak Foto</title></head><body>");
            newWindow.document.write("<img src='" + fotoCard.src + "' style='width: 100%;'>");
            newWindow.document.write("</body></html>");
            newWindow.document.close();
            newWindow.focus();
            newWindow.print();
            newWindow.close();
        } else {
            alert("Foto tidak ditemukan untuk dicetak.");
        }
    }
</script>

