<?php
require_once '../config.php';
require_once '../functions.php';
require_once '../crud_functions.php';

// Cek login dan role admin
if (!isLoggedIn() || $_SESSION['user_role'] != 'admin') {
    setMessage('error', 'Akses ditolak. Hanya untuk admin.');
    redirect('../login.php');
}

$user = getUserById($_SESSION['user_id']);
$message = getMessage();

// Handle CRUD Operations
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// LIST - Tampilkan semua user
if ($action == 'list') {
    $users = getAll('users');
    
// CREATE - Form tambah user
} elseif ($action == 'create') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'full_name' => sanitize($_POST['full_name']),
            'user_role' => $_POST['user_role'],
            'nim' => $_POST['nim'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        $result = createData('users', $data);
        
        if ($result['success']) {
            setMessage('success', 'User berhasil ditambahkan');
            redirect('users.php');
        } else {
            setMessage('error', 'Gagal menambahkan user: ' . $result['error']);
        }
    }
    
// EDIT - Form edit user
} elseif ($action == 'edit') {
    $user_data = getById('users', $id);
    
    if (!$user_data) {
        setMessage('error', 'User tidak ditemukan');
        redirect('users.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'full_name' => sanitize($_POST['full_name']),
            'user_role' => $_POST['user_role'],
            'nim' => $_POST['nim'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Jika password diisi, update password
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        
        $result = updateData('users', $id, $data);
        
        if ($result['success']) {
            setMessage('success', 'User berhasil diperbarui');
            redirect('users.php');
        } else {
            setMessage('error', 'Gagal memperbarui user: ' . $result['error']);
        }
    }
    
// DELETE - Hapus user
} elseif ($action == 'delete') {
    $result = deleteData('users', $id);
    
    if ($result['success']) {
        setMessage('success', 'User berhasil dihapus');
    } else {
        setMessage('error', 'Gagal menghapus user: ' . $result['error']);
    }
    
    redirect('users.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - <?php echo APP_NAME; ?></title>
    
    <!----Link style Css---->
    <link rel="stylesheet" href="../assets/css/users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- SIDEBAR (sama dengan dashboard) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo APP_NAME; ?></h2>
                <p>v<?php echo APP_VERSION; ?></p>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                <div class="user-role"><?php echo $user['user_role']; ?></div>
            </div>
            
            <nav class="sidebar-menu">
                <a href="../dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="users.php" class="active">
                    <i class="fas fa-users"></i> Kelola User
                </a>
                <a href="matakuliah.php">
                    <i class="fas fa-book"></i> Mata Kuliah
                </a>
                <a href="nilai.php">
                    <i class="fas fa-chart-bar"></i> Nilai
                </a>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>
        
        <!-- MAIN CONTENT -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>Kelola User</h1>
                <p>Manajemen data pengguna sistem</p>
            </div>
            
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message['type']; ?>">
                    <i class="fas fa-<?php echo $message['type'] == 'success' ? 'check-circle' : 'info-circle'; ?>"></i>
                    <?php echo $message['text']; ?>
                </div>
            <?php endif; ?>
            
            <!-- CRUD Content -->
            <?php if ($action == 'list'): ?>
                <!-- List Users -->
                <div class="crud-header">
                    <h2>Daftar User</h2>
                    <a href="?action=create" class="btn-add">
                        <i class="fas fa-plus"></i> Tambah User
                    </a>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo $u['id']; ?></td>
                                <td><?php echo htmlspecialchars($u['username']); ?></td>
                                <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $u['user_role']; ?>">
                                        <?php echo $u['user_role']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $u['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                        <?php echo $u['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="?action=edit&id=<?php echo $u['id']; ?>" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?php echo $u['id']; ?>" 
                                       class="btn-action btn-delete"
                                       onclick="return confirm('Hapus user ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($action == 'create' || $action == 'edit'): ?>
                <!-- Create/Edit Form -->
                <div class="crud-header">
                    <h2><?php echo $action == 'create' ? 'Tambah User Baru' : 'Edit User'; ?></h2>
                    <a href="users.php" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="form-container">
                    <form method="POST">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" id="username" name="username" class="form-control" 
                                   value="<?php echo $user_data['username'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo $user_data['email'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name">Nama Lengkap *</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" 
                                   value="<?php echo $user_data['full_name'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">
                                Password <?php echo $action == 'create' ? '*' : '(Kosongkan jika tidak diubah)'; ?>
                            </label>
                            <input type="password" id="password" name="password" class="form-control" 
                                   <?php echo $action == 'create' ? 'required' : ''; ?>>
                        </div>
                        
                        <div class="form-group">
                            <label for="user_role">Role *</label>
                            <select id="user_role" name="user_role" class="form-control" required>
                                <option value="">Pilih Role</option>
                                <option value="admin" <?php echo ($user_data['user_role'] ?? '') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="mahasiswa" <?php echo ($user_data['user_role'] ?? '') == 'mahasiswa' ? 'selected' : ''; ?>>Mahasiswa</option>
                                <option value="calon_mahasiswa" <?php echo ($user_data['user_role'] ?? '') == 'calon_mahasiswa' ? 'selected' : ''; ?>>Calon Mahasiswa</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="nim">NIM (untuk mahasiswa)</label>
                            <input type="text" id="nim" name="nim" class="form-control" 
                                   value="<?php echo $user_data['nim'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Telepon</label>
                            <input type="text" id="phone" name="phone" class="form-control" 
                                   value="<?php echo $user_data['phone'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_active" value="1" 
                                    <?php echo ($user_data['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                Aktifkan akun
                            </label>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="users.php" class="btn-cancel">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>