<?php 
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle delete user
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $current_user = $_SESSION['user_id'];
    
    // Prevent deleting own account
    if ($delete_id == $current_user) {
        $error = 'Tidak bisa menghapus akun sendiri!';
    } else {
        $delete_query = "DELETE FROM users WHERE id = $delete_id";
        if (mysqli_query($conn, $delete_query)) {
            header('Location: users.php?status=dihapus');
            exit;
        } else {
            $error = 'Gagal menghapus user!';
        }
    }
}

// Handle register user
$register_error = '';
$register_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_user'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $register_error = 'Semua field harus diisi!';
    } elseif (strlen($username) < 3) {
        $register_error = 'Username minimal 3 karakter!';
    } elseif (strlen($password) < 6) {
        $register_error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $register_error = 'Password dan konfirmasi password tidak cocok!';
    } else {
        // Cek apakah username sudah ada
        $username_escape = mysqli_real_escape_string($conn, $username);
        $check_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username_escape'");
        
        if (mysqli_num_rows($check_user) > 0) {
            $register_error = 'Username sudah terdaftar!';
        } else {
            // Hash password dan buat user baru
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $insert_query = "INSERT INTO users (username, password) VALUES ('$username_escape', '$password_hash')";
            
            if (mysqli_query($conn, $insert_query)) {
                $register_success = 'Registrasi berhasil! User baru telah ditambahkan.';
            } else {
                $register_error = 'Gagal membuat akun. Coba lagi nanti!';
            }
        }
    }
}

// Handle edit user
$edit_error = '';
$edit_success = '';
$edit_user_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $edit_id = $_POST['edit_user_id'] ?? '';
    $username = $_POST['edit_username'] ?? '';
    $password = $_POST['edit_password'] ?? '';

    if (empty($username)) {
        $edit_error = 'Username tidak boleh kosong!';
    } elseif (strlen($username) < 3) {
        $edit_error = 'Username minimal 3 karakter!';
    } else {
        // Cek apakah username sudah ada (kecuali user yang sedang diedit)
        $username_escape = mysqli_real_escape_string($conn, $username);
        $check_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username_escape' AND id != $edit_id");
        
        if (mysqli_num_rows($check_user) > 0) {
            $edit_error = 'Username sudah digunakan!';
        } else {
            // Update username only jika password kosong
            if (empty($password)) {
                $update_query = "UPDATE users SET username = '$username_escape' WHERE id = $edit_id";
                if (mysqli_query($conn, $update_query)) {
                    $edit_success = 'Username berhasil diperbarui!';
                } else {
                    $edit_error = 'Gagal memperbarui user!';
                }
            } else {
                // Update dengan password baru
                if (strlen($password) < 6) {
                    $edit_error = 'Password minimal 6 karakter!';
                } else {
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);
                    $update_query = "UPDATE users SET username = '$username_escape', password = '$password_hash' WHERE id = $edit_id";
                    if (mysqli_query($conn, $update_query)) {
                        $edit_success = 'User berhasil diperbarui!';
                    } else {
                        $edit_error = 'Gagal memperbarui user!';
                    }
                }
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
    <title>Manajemen User - Sistem Riwayat Gizi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if (isset($_GET['status'])): ?>
    <div class="toast">
        User berhasil <?= htmlspecialchars($_GET['status']); ?>!
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.toast').style.display = 'none';
        }, 3000);
    </script>
    <?php endif; ?>

    <div class="layout">
        <aside class="sidebar">
            <h2>Monitoring Gizi</h2>
            <ul>
                <li><a href="index.php">Riwayat Gizi</a></li>
                <li class="active"><a href="users.php">User</a></li>
            </ul>
            <div class="sidebar-logout">
                <a href="logout.php" class="btn btn-danger btn-logout">🚪 Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="header">
                <div>
                    <h1>Manajemen User</h1>
                    <small style="color: #999;">User: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></small>
                </div>
                <div class="header-actions">
                    <button type="button" class="btn btn-primary btn-sm" onclick="openRegisterModal()">📝 Register</button>
                </div>
            </div>

            <?php if (!empty($error)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <div class="card">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $users_query = "SELECT * FROM users ORDER BY id ASC";
                        $users_result = mysqli_query($conn, $users_query);

                        while ($user = mysqli_fetch_assoc($users_result)) {
                            $is_current = ($user['id'] == $_SESSION['user_id']) ? true : false;
                            $tanda = $is_current ? ' (Anda)' : '';
                            $created_date = date('d-m-Y H:i', strtotime($user['created_at']));
                            
                            echo "<tr>
                                <td>{$user['id']}</td>
                                <td><strong>{$user['username']}</strong>{$tanda}</td>
                                <td>{$created_date}</td>
                                <td>
                                    <div class='action-buttons'>";
                            
                            if ($is_current) {
                                echo "<span style='color: #999; font-size: 12px;'>Akun Aktif</span>";
                            } else {
                                echo "<button type='button' class='btn btn-warning btn-sm' onclick='openEditModal({$user['id']}, \"{$user['username']}\")'>Edit</button>
                                    <a href='users.php?delete={$user['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus user ini?')\">Hapus</a>";
                            }
                            
                            echo "</div>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Registrasi Admin Baru</h2>
                <span class="close" onclick="closeRegisterModal()">&times;</span>
            </div>
            <div class="modal-body">
                <?php if (!empty($register_error)): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <?= htmlspecialchars($register_error) ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($register_success)): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✅</span>
                    <?= htmlspecialchars($register_success) ?>
                </div>
                <?php endif; ?>

                <form method="POST" id="registerForm">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="Masukkan username baru" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
                    </div>

                    <input type="hidden" name="register_user" value="1">
                    
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeRegisterModal()">Batal</button>
                        <button type="submit" class="btn btn-primary">Buat Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit User</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <?php if (!empty($edit_error)): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <?= htmlspecialchars($edit_error) ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($edit_success)): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✅</span>
                    <?= htmlspecialchars($edit_success) ?>
                </div>
                <?php endif; ?>

                <form method="POST" id="editForm">
                    <div class="form-group">
                        <label for="edit_username">Username</label>
                        <input type="text" id="edit_username" name="edit_username" placeholder="Masukkan username" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_password">Password Baru (Opsional)</label>
                        <input type="password" id="edit_password" name="edit_password" placeholder="Kosongkan jika tidak ingin mengubah">
                        <small style="color: #666; font-size: 12px; margin-top: 4px; display: block;">Minimal 6 karakter jika diisi</small>
                    </div>

                    <input type="hidden" name="edit_user" value="1">
                    <input type="hidden" id="edit_user_id" name="edit_user_id" value="">
                    
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRegisterModal() {
            document.getElementById('registerModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeRegisterModal() {
            document.getElementById('registerModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            // Reset form
            document.getElementById('registerForm').reset();
            // Clear any error/success messages
            const alerts = document.querySelectorAll('#registerModal .alert');
            alerts.forEach(alert => alert.remove());
        }

        function openEditModal(userId, username) {
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_password').value = '';
            document.getElementById('editModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
            // Focus on username field
            document.getElementById('edit_username').focus();
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            // Reset form
            document.getElementById('editForm').reset();
            // Clear any error/success messages
            const alerts = document.querySelectorAll('#editModal .alert');
            alerts.forEach(alert => alert.remove());
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const registerModal = document.getElementById('registerModal');
            const editModal = document.getElementById('editModal');
            if (event.target == registerModal) {
                closeRegisterModal();
            }
            if (event.target == editModal) {
                closeEditModal();
            }
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeRegisterModal();
                closeEditModal();
            }
        });
    </script>
</body>
</html>
