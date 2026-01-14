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
$message = getMessage();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/profile.css">
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
                <div class="profile-role"><?php echo $user['user_role']; ?></div>
            </div>
            
            <nav class="sidebar-menu">
                <a href="dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="profile.php" class="active">
                    <i class="fas fa-user"></i> Profil
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>Profil Pengguna</h1>
                <p>Kelola informasi profil Anda</p>
            </div>
            
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message['type']; ?>">
                    <i class="fas fa-<?php echo $message['type'] == 'success' ? 'check-circle' : 'info-circle'; ?>"></i>
                    <?php echo $message['text']; ?>
                </div>
            <?php endif; ?>
            
            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                        <div class="profile-role"><?php echo $user['user_role']; ?></div>
                    </div>
                </div>
                
                <div class="profile-details">
                    <div class="detail-group">
                        <label>Username</label>
                        <div class="value"><?php echo htmlspecialchars($user['username']); ?></div>
                    </div>
                    
                    <div class="detail-group">
                        <label>Email</label>
                        <div class="value"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    
                    <div class="detail-group">
                        <label>Role</label>
                        <div class="value"><?php echo $user['user_role']; ?></div>
                    </div>
                    
                    <?php if ($user['nim']): ?>
                    <div class="detail-group">
                        <label>NIM</label>
                        <div class="value"><?php echo htmlspecialchars($user['nim']); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($user['phone']): ?>
                    <div class="detail-group">
                        <label>Telepon</label>
                        <div class="value"><?php echo htmlspecialchars($user['phone']); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="detail-group">
                        <label>Status Akun</label>
                        <div class="value">
                            <?php echo $user['is_active'] ? 
                                '<span style="color:green">Aktif</span>' : 
                                '<span style="color:red">Nonaktif</span>'; ?>
                        </div>
                    </div>
                    
                    <div class="detail-group">
                        <label>Tanggal Bergabung</label>
                        <div class="value">
                            <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                        </div>
                    </div>
                    
                    <div class="detail-group">
                        <label>Terakhir Login</label>
                        <div class="value">
                            <?php echo $user['last_login'] ? 
                                date('d/m/Y H:i', strtotime($user['last_login'])) : 
                                'Belum pernah login'; ?>
                        </div>
                    </div>
                </div>
                
                <div class="actions">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button class="btn btn-primary" onclick="editProfile()">
                        <i class="fas fa-edit"></i> Edit Profil
                    </button>
                    <button class="btn btn-secondary" onclick="changePassword()">
                        <i class="fas fa-key"></i> Ganti Password
                    </button>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        <script src="assets/js/profile.js"></script>
    </script>
</body>
</html>