<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mengecek username
    $sql = "SELECT * FROM user WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['Password'])) {
            // Set session untuk username, user_id, dan role
            $_SESSION['username'] = $user['Username'];
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['role'] = $user['role']; // Menyimpan role user di session

            // Cek role user, jika admin arahkan ke admin.php, jika bukan ke index.php
            if ($user['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
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

        .container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
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

        .close-button:hover {
            color: #a7c1e5;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #6c63ff;
        }

        input[type="text"],
        input[type="password"],
        input[type="submit"] {
            width: 90%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
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

        p {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Login</h1>
    <div class="container">
        <form action="" method="POST">
            <button type="button" class="close-btn" onclick="window.location.href='index.php'">&times;</button> <!-- Tanda Silang sebagai Button -->

            <label for="username">Username:</label>
            <input type="text" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            
            <input type="submit" value="Login">
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
