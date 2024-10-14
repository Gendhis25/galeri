<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $role = $_POST['role']; // Tambahkan role dari form

    // Cek apakah username sudah ada
    $sql_check_username = "SELECT * FROM user WHERE Username = ?";
    $stmt_check_username = $conn->prepare($sql_check_username);
    $stmt_check_username->bind_param("s", $username);
    $stmt_check_username->execute();
    $result_check_username = $stmt_check_username->get_result();

    // Cek apakah email sudah ada
    $sql_check_email = "SELECT * FROM user WHERE Email = ?";
    $stmt_check_email = $conn->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $result_check_email = $stmt_check_email->get_result();

    if ($result_check_username->num_rows > 0) {
        $error = "Nama pengguna sudah ada. Silakan pilih nama pengguna lain.";
    } elseif ($result_check_email->num_rows > 0) {
        $error = "Email sudah digunakan. Silakan gunakan email lain.";
    } else {
        // Jika username dan email belum ada, lakukan insert
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password sebelum disimpan

        $sql = "INSERT INTO user (Username, Password, Email, NamaLengkap, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $hashed_password, $email, $nama_lengkap, $role);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Terjadi kesalahan saat registrasi.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Daftar</title>
    <style>
        /* style_register.css */
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

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #ffffff; /* Warna latar belakang form */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #6c63ff; /* Warna label */
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="submit"] {
            width: 90%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #85a2d6; /* Warna tombol submit (user) */
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #a7c1e5; /* Warna hover tombol submit */
        }

        p {
            text-align: center;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #6c63ff; /* Warna link login */
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Daftar</h1>
    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        
        <label for="nama_lengkap">Nama Lengkap:</label>
        <input type="text" name="nama_lengkap" required>

        <label for="role">Pilih Peran:</label>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        
        <input type="submit" value="Daftar">
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
    
    <div class="login-link">
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p> <!-- Tautan untuk login -->
    </div>
</body>
</html>
