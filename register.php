<?php
include 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok!';
    } else {
        // Cek apakah username sudah ada
        $username_escape = mysqli_real_escape_string($conn, $username);
        $check_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username_escape'");
        
        if (mysqli_num_rows($check_user) > 0) {
            $error = 'Username sudah terdaftar!';
        } else {
            // Hash password dan buat user baru
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $insert_query = "INSERT INTO users (username, password) VALUES ('$username_escape', '$password_hash')";
            
            if (mysqli_query($conn, $insert_query)) {
                $success = 'Registrasi berhasil! Silakan login.';
                $_POST['username'] = '';
                $_POST['password'] = '';
                $_POST['confirm_password'] = '';
            } else {
                $error = 'Gagal membuat akun. Coba lagi nanti!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Riwayat Gizi</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f1f5f9;
        }

        .register-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .register-container h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-register:hover {
            background: #059669;
        }

        .alternative {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .alternative a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .alternative a:hover {
            text-decoration: underline;
        }

        .info {
            background: #dbeafe;
            color: #1e40af;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Registrasi Admin</h1>

        <div class="info">
            ℹ️ Buat akun admin baru untuk sistem Riwayat Gizi
        </div>

        <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="login.php" class="btn btn-primary" style="display: inline-block; padding: 10px 20px; text-decoration: none; color: white; background: #3b82f6; border-radius: 6px;">Ke Halaman Login</a>
        </div>
        <?php else: ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn-register">Buat Akun</button>
        </form>

        <div class="alternative">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>

        <?php endif; ?>
    </div>
</body>
</html>
