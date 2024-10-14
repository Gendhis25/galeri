<?php
include 'koneksi.php';

// Periksa apakah parameter 'id' ada di URL
if (!isset($_GET['id'])) {
    echo "Foto ID tidak ditemukan.";
    exit(); // Hentikan eksekusi jika tidak ada ID foto
}

$foto_id = $_GET['id'];

// Ambil informasi foto berdasarkan FotoID
$sql_foto = "SELECT * FROM foto WHERE FotoID = ?";
$stmt_foto = $conn->prepare($sql_foto);
$stmt_foto->bind_param("i", $foto_id);
$stmt_foto->execute();
$foto_info = $stmt_foto->get_result()->fetch_assoc();

// Cek apakah data foto ada
if ($foto_info === null) {
    echo "Foto tidak ditemukan.";
    exit(); // Hentikan eksekusi jika foto tidak ditemukan
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Foto - <?php echo $foto_info['JudulFoto']; ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        img {
            max-width: 100%;
            max-height: 100vh;
            object-fit: contain;
        }
        @media print {
            body {
                margin: 0;
            }
            img {
                width: 100%;
                height: auto;
            }
            /* Menyembunyikan tombol saat mencetak */
            button {
                display: none; /* Sembunyikan tombol Kembali saat mencetak */
            }
        }
    </style>
</head>
<body>
    <img src="<?php echo $foto_info['LokasiFile']; ?>" alt="<?php echo $foto_info['JudulFoto']; ?>">


    <script>
        // Secara otomatis membuka dialog cetak saat halaman dimuat
        window.onload = function() {
            window.print();
        };
    </script>

</body>
</html>
