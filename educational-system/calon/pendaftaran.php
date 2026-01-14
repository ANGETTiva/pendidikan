<?php
require_once '../config.php';
require_once '../functions.php';
require_once '../crud_functions.php';

// Cek login dan role calon mahasiswa
if (!isLoggedIn() || $_SESSION['user_role'] != 'calon_mahasiswa') {
    setMessage('error', 'Akses ditolak. Hanya untuk calon mahasiswa.');
    redirect('../login.php');
}

$user = getUserById($_SESSION['user_id']);
$message = getMessage();

// Handle CRUD
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

if ($action == 'list') {
    $pendaftaran = getPendaftaranByUser($user['id']);
    
} elseif ($action == 'create') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'user_id' => $user['id'],
            'nama_lengkap' => sanitize($_POST['nama_lengkap']),
            'tempat_lahir' => sanitize($_POST['tempat_lahir']),
            'tanggal_lahir' => $_POST['tanggal_lahir'],
            'jenis_kelamin' => $_POST['jenis_kelamin'],
            'alamat' => sanitize($_POST['alamat']),
            'asal_sekolah' => sanitize($_POST['asal_sekolah']),
            'program_studi' => $_POST['program_studi'],
            'tahun_lulus' => (int)$_POST['tahun_lulus'],
            'nilai_akhir' => (float)$_POST['nilai_akhir']
        ];
        
        $result = createData('pendaftaran', $data);
        
        if ($result['success']) {
            setMessage('success', 'Pendaftaran berhasil dikirim');
            redirect('pendaftaran.php');
        } else {
            setMessage('error', 'Gagal mendaftar: ' . $result['error']);
        }
    }
    
} elseif ($action == 'edit') {
    $daftar_data = getById('pendaftaran', $id);
    
    // Cek kepemilikan
    if (!$daftar_data || $daftar_data['user_id'] != $user['id']) {
        setMessage('error', 'Data tidak ditemukan atau bukan milik Anda');
        redirect('pendaftaran.php');
    }
    
    // Cek jika sudah diverifikasi tidak bisa edit
    if ($daftar_data['status'] != 'pending') {
        setMessage('error', 'Pendaftaran sudah diproses, tidak dapat diedit');
        redirect('pendaftaran.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'nama_lengkap' => sanitize($_POST['nama_lengkap']),
            'tempat_lahir' => sanitize($_POST['tempat_lahir']),
            'tanggal_lahir' => $_POST['tanggal_lahir'],
            'jenis_kelamin' => $_POST['jenis_kelamin'],
            'alamat' => sanitize($_POST['alamat']),
            'asal_sekolah' => sanitize($_POST['asal_sekolah']),
            'program_studi' => $_POST['program_studi'],
            'tahun_lulus' => (int)$_POST['tahun_lulus'],
            'nilai_akhir' => (float)$_POST['nilai_akhir']
        ];
        
        $result = updateData('pendaftaran', $id, $data);
        
        if ($result['success']) {
            setMessage('success', 'Pendaftaran berhasil diperbarui');
            redirect('pendaftaran.php');
        } else {
            setMessage('error', 'Gagal memperbarui: ' . $result['error']);
        }
    }
    
} elseif ($action == 'delete') {
    $daftar_data = getById('pendaftaran', $id);
    
    // Cek kepemilikan
    if (!$daftar_data || $daftar_data['user_id'] != $user['id']) {
        setMessage('error', 'Data tidak ditemukan atau bukan milik Anda');
    } else {
        $result = deleteData('pendaftaran', $id);
        
        if ($result['success']) {
            setMessage('success', 'Pendaftaran berhasil dibatalkan');
        } else {
            setMessage('error', 'Gagal membatalkan: ' . $result['error']);
        }
    }
    
    redirect('pendaftaran.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran PMB - <?php echo APP_NAME; ?></title>
    <!----Link style Css---->
    <link rel="stylesheet" href="../assets/css/pendaftaran.css">
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
                <a href="pendaftaran.php" class="active">
                    <i class="fas fa-edit"></i> Pendaftaran PMB
                </a>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>Pendaftaran PMB</h1>
                <p>Formulir Penerimaan Mahasiswa Baru</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message['type']; ?>">
                    <i class="fas fa-<?php echo $message['type'] == 'success' ? 'check-circle' : 'info-circle'; ?>"></i>
                    <?php echo $message['text']; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($action == 'list'): ?>
                <div class="crud-header">
                    <h2>Data Pendaftaran Anda</h2>
                    <?php if (empty($pendaftaran)): ?>
                        <a href="?action=create" class="btn-add">
                            <i class="fas fa-plus"></i> Daftar Baru
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if (empty($pendaftaran)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Anda belum memiliki data pendaftaran. Silakan daftar terlebih dahulu.
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Program Studi</th>
                                    <th>Nilai Akhir</th>
                                    <th>Status</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendaftaran as $index => $daftar): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($daftar['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($daftar['program_studi']); ?></td>
                                    <td><?php echo number_format($daftar['nilai_akhir'], 2); ?></td>
                                    <td>
                                        <span class="badge-status badge-<?php echo $daftar['status']; ?>">
                                            <?php 
                                            $statusText = [
                                                'pending' => 'Menunggu',
                                                'diverifikasi' => 'Diverifikasi',
                                                'diterima' => 'Diterima',
                                                'ditolak' => 'Ditolak'
                                            ];
                                            echo $statusText[$daftar['status']];
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($daftar['tanggal_daftar'])); ?></td>
                                    <td class="actions">
                                        <a href="?action=edit&id=<?php echo $daftar['id']; ?>" 
                                           class="btn-action btn-edit"
                                           <?php echo $daftar['status'] != 'pending' ? 'style="opacity:0.5;pointer-events:none;"' : ''; ?>>
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?action=delete&id=<?php echo $daftar['id']; ?>" 
                                           class="btn-action btn-delete"
                                           onclick="return confirm('Batalkan pendaftaran ini?')"
                                           <?php echo $daftar['status'] != 'pending' ? 'style="opacity:0.5;pointer-events:none;"' : ''; ?>>
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
            <?php elseif ($action == 'create' || $action == 'edit'): ?>
                <div class="crud-header">
                    <h2><?php echo $action == 'create' ? 'Form Pendaftaran PMB' : 'Edit Pendaftaran'; ?></h2>
                    <a href="pendaftaran.php" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="form-container">
                    <form method="POST">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap *</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" 
                                   value="<?php echo $daftar_data['nama_lengkap'] ?? $user['full_name']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="tempat_lahir">Tempat Lahir *</label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control" 
                                   value="<?php echo $daftar_data['tempat_lahir'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir *</label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control" 
                                   value="<?php echo $daftar_data['tanggal_lahir'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin *</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                                <option value="">Pilih</option>
                                <option value="L" <?php echo ($daftar_data['jenis_kelamin'] ?? '') == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="P" <?php echo ($daftar_data['jenis_kelamin'] ?? '') == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="alamat">Alamat Lengkap *</label>
                            <textarea id="alamat" name="alamat" class="form-control" rows="3" required><?php echo $daftar_data['alamat'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="asal_sekolah">Asal Sekolah *</label>
                            <input type="text" id="asal_sekolah" name="asal_sekolah" class="form-control" 
                                   value="<?php echo $daftar_data['asal_sekolah'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="program_studi">Program Studi *</label>
                            <select id="program_studi" name="program_studi" class="form-control" required>
                                <option value="">Pilih Program Studi</option>
                                <option value="Teknik Informatika" <?php echo ($daftar_data['program_studi'] ?? '') == 'Teknik Informatika' ? 'selected' : ''; ?>>Teknik Informatika</option>
                                <option value="Sistem Informasi" <?php echo ($daftar_data['program_studi'] ?? '') == 'Sistem Informasi' ? 'selected' : ''; ?>>Sistem Informasi</option>
                                <option value="Teknik Komputer" <?php echo ($daftar_data['program_studi'] ?? '') == 'Teknik Komputer' ? 'selected' : ''; ?>>Teknik Komputer</option>
                                <option value="Manajemen Informatika" <?php echo ($daftar_data['program_studi'] ?? '') == 'Manajemen Informatika' ? 'selected' : ''; ?>>Manajemen Informatika</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tahun_lulus">Tahun Lulus *</label>
                            <input type="number" id="tahun_lulus" name="tahun_lulus" class="form-control" 
                                   value="<?php echo $daftar_data['tahun_lulus'] ?? date('Y'); ?>" 
                                   min="2000" max="<?php echo date('Y'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nilai_akhir">Nilai Akhir (Rata-rata) *</label>
                            <input type="number" id="nilai_akhir" name="nilai_akhir" class="form-control" 
                                   value="<?php echo $daftar_data['nilai_akhir'] ?? ''; ?>" 
                                   step="0.01" min="0" max="100" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-save">
                                <i class="fas fa-paper-plane"></i> 
                                <?php echo $action == 'create' ? 'Kirim Pendaftaran' : 'Update Pendaftaran'; ?>
                            </button>
                            <a href="pendaftaran.php" class="btn-cancel">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
<script src="../assets/javascript/pendaftaran.js"></script>
</html>