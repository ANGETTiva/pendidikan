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

// Handle CRUD
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

if ($action == 'list') {
    $matakuliah = getAllMataKuliah();
    
} elseif ($action == 'create') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'kode_mk' => sanitize($_POST['kode_mk']),
            'nama_mk' => sanitize($_POST['nama_mk']),
            'sks' => (int)$_POST['sks'],
            'semester' => (int)$_POST['semester'],
            'dosen_pengampu' => sanitize($_POST['dosen_pengampu'])
        ];
        
        $result = createData('mata_kuliah', $data);
        
        if ($result['success']) {
            setMessage('success', 'Mata kuliah berhasil ditambahkan');
            redirect('matakuliah.php');
        } else {
            setMessage('error', 'Gagal menambahkan: ' . $result['error']);
        }
    }
    
} elseif ($action == 'edit') {
    $mk_data = getById('mata_kuliah', $id);
    
    if (!$mk_data) {
        setMessage('error', 'Data tidak ditemukan');
        redirect('matakuliah.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'kode_mk' => sanitize($_POST['kode_mk']),
            'nama_mk' => sanitize($_POST['nama_mk']),
            'sks' => (int)$_POST['sks'],
            'semester' => (int)$_POST['semester'],
            'dosen_pengampu' => sanitize($_POST['dosen_pengampu'])
        ];
        
        $result = updateData('mata_kuliah', $id, $data);
        
        if ($result['success']) {
            setMessage('success', 'Mata kuliah berhasil diperbarui');
            redirect('matakuliah.php');
        } else {
            setMessage('error', 'Gagal memperbarui: ' . $result['error']);
        }
    }
    
} elseif ($action == 'delete') {
    $result = deleteData('mata_kuliah', $id);
    
    if ($result['success']) {
        setMessage('success', 'Mata kuliah berhasil dihapus');
    } else {
        setMessage('error', 'Gagal menghapus: ' . $result['error']);
    }
    
    redirect('matakuliah.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mata Kuliah - <?php echo APP_NAME; ?></title>
    <style>
        /* Gunakan CSS dari users.php */
        .badge-sks {
            background: #17a2b8;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.9rem;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
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
                <a href="users.php">
                    <i class="fas fa-users"></i> Kelola User
                </a>
                <a href="matakuliah.php" class="active">
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
        
        <main class="main-content">
            <div class="header">
                <h1>Mata Kuliah</h1>
                <p>Manajemen data mata kuliah</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message['type']; ?>">
                    <i class="fas fa-<?php echo $message['type'] == 'success' ? 'check-circle' : 'info-circle'; ?>"></i>
                    <?php echo $message['text']; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($action == 'list'): ?>
                <div class="crud-header">
                    <h2>Daftar Mata Kuliah</h2>
                    <a href="?action=create" class="btn-add">
                        <i class="fas fa-plus"></i> Tambah Mata Kuliah
                    </a>
                </div>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Semester</th>
                                <th>Dosen Pengampu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matakuliah as $mk): ?>
                            <tr>
                                <td><strong><?php echo $mk['kode_mk']; ?></strong></td>
                                <td><?php echo htmlspecialchars($mk['nama_mk']); ?></td>
                                <td>
                                    <span class="badge-sks"><?php echo $mk['sks']; ?> SKS</span>
                                </td>
                                <td>Semester <?php echo $mk['semester']; ?></td>
                                <td><?php echo htmlspecialchars($mk['dosen_pengampu']); ?></td>
                                <td class="actions">
                                    <a href="?action=edit&id=<?php echo $mk['id']; ?>" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?action=delete&id=<?php echo $mk['id']; ?>" 
                                       class="btn-action btn-delete"
                                       onclick="return confirm('Hapus mata kuliah ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($action == 'create' || $action == 'edit'): ?>
                <div class="crud-header">
                    <h2><?php echo $action == 'create' ? 'Tambah Mata Kuliah' : 'Edit Mata Kuliah'; ?></h2>
                    <a href="matakuliah.php" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="form-container">
                    <form method="POST">
                        <div class="form-group">
                            <label for="kode_mk">Kode MK *</label>
                            <input type="text" id="kode_mk" name="kode_mk" class="form-control" 
                                   value="<?php echo $mk_data['kode_mk'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_mk">Nama Mata Kuliah *</label>
                            <input type="text" id="nama_mk" name="nama_mk" class="form-control" 
                                   value="<?php echo $mk_data['nama_mk'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sks">SKS *</label>
                            <input type="number" id="sks" name="sks" class="form-control" 
                                   value="<?php echo $mk_data['sks'] ?? ''; ?>" min="1" max="6" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="semester">Semester *</label>
                            <input type="number" id="semester" name="semester" class="form-control" 
                                   value="<?php echo $mk_data['semester'] ?? ''; ?>" min="1" max="8" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="dosen_pengampu">Dosen Pengampu *</label>
                            <input type="text" id="dosen_pengampu" name="dosen_pengampu" class="form-control" 
                                   value="<?php echo $mk_data['dosen_pengampu'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="matakuliah.php" class="btn-cancel">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>