<?php
require_once 'config.php';
require_once 'functions.php';

// Cek login
if (!isLoggedIn()) {
    setMessage('error', 'Silakan login terlebih dahulu');
    redirect('login.php');
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);

// Pesan
$message = getMessage();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
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
                <a href="dashboard.php" class="active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="profile.php">
                    <i class="fas fa-user"></i> Profil
                </a>
                <?php if ($user['user_role'] == 'admin'): ?>
                <a href="admin/users.php">
                    <i class="fas fa-users"></i> Kelola User
                </a>
                <a href="admin/matakuliah.php">
                    <i class="fas fa-book"></i> Mata Kuliah
                </a>
                <a href="#">
                    <i class="fas fa-file-signature"></i> PMB
                </a>
                <?php elseif ($user['user_role'] == 'mahasiswa'): ?>
                <a href="#">
                    <i class="fas fa-book"></i> Akademik
                </a>
                <a href="#">
                    <i class="fas fa-calendar-alt"></i> Jadwal
                </a>
                <a href="admin/nilai.php">
                    <i class="fas fa-chart-bar"></i> Nilai
                </a>
                <?php elseif ($user['user_role'] == 'calon_mahasiswa'): ?>
                <a href="calon/pendaftaran.php">
                    <i class="fas fa-edit"></i> Pendaftaran PMB
                </a>
                <a href="#">
                    <i class="fas fa-search"></i> Cek Status
                </a>
                <?php endif; ?>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>Dashboard</h1>
                <div class="breadcrumb">
                    <i class="fas fa-home"></i> Beranda
                </div>
            </div>
            
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message['type']; ?>">
                    <i class="fas fa-<?php echo $message['type'] == 'success' ? 'check-circle' : 'info-circle'; ?>"></i>
                    <?php echo $message['text']; ?>
                </div>
            <?php endif; ?>
            
            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-icon users">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h3>Profil</h3>
                    <p>Kelola data pribadi Anda</p>
                </div>
                
                <?php if ($user['user_role'] == 'admin'): ?>
                <div class="card">
                    <div class="card-icon pmb">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3>PMB</h3>
                    <p>Kelola penerimaan mahasiswa</p>
                </div>
                <div class="card">
                    <div class="card-icon academic">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Users</h3>
                    <p>Kelola pengguna sistem</p>
                </div>
                <?php elseif ($user['user_role'] == 'mahasiswa'): ?>
                <div class="card">
                    <div class="card-icon academic">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3>Akademik</h3>
                    <p>Informasi akademik</p>
                </div>
                <div class="card">
                    <div class="card-icon profile">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Nilai</h3>
                    <p>Lihat nilai akademik</p>
                </div>
                <?php elseif ($user['user_role'] == 'calon_mahasiswa'): ?>
                <div class="card">
                    <div class="card-icon pmb">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3>PMB</h3>
                    <p>Form pendaftaran</p>
                </div>
                <div class="card">
                    <div class="card-icon profile">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Status</h3>
                    <p>Cek status pendaftaran</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Aksi Cepat</h2>
                <div class="action-grid">
                    <a href="profile.php" class="action-btn">
                        <i class="fas fa-user-edit"></i>
                        <span>Edit Profil</span>
                    </a>
                    
                    <?php if ($user['user_role'] == 'admin'): ?>
                    <a href="#" class="action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Tambah User</span>
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Verifikasi PMB</span>
                    </a>
                    <?php elseif ($user['user_role'] == 'mahasiswa'): ?>
                    <a href="#" class="action-btn">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Lihat KRS</span>
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-file-alt"></i>
                        <span>Transkrip</span>
                    </a>
                    <?php elseif ($user['user_role'] == 'calon_mahasiswa'): ?>
                    <a href="#" class="action-btn">
                        <i class="fas fa-edit"></i>
                        <span>Isi Form PMB</span>
                    </a>
                    <a href="#" class="action-btn">
                        <i class="fas fa-upload"></i>
                        <span>Upload Dokumen</span>
                    </a>
                    <?php endif; ?>
                    
                    <a href="logout.php" class="action-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>