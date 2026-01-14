<?php
echo "<h2>Setup Database Sistem Pendidikan</h2>";

// Koneksi database
$conn = new mysqli('localhost', 'root', '');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

echo "<p style='color:green'>✓ Terhubung ke MySQL</p>";

// Buat database
$sql = "CREATE DATABASE IF NOT EXISTS educational_system 
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Database siap</p>";
} else {
    echo "<p style='color:red'>✗ Error: " . $conn->error . "</p>";
}

// Pilih database
$conn->select_db('educational_system');

// Buat tabel users
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    user_role ENUM('admin', 'mahasiswa', 'calon_mahasiswa') DEFAULT 'calon_mahasiswa',
    nim VARCHAR(20) NULL,
    phone VARCHAR(20) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Tabel users dibuat</p>";
} else {
    echo "<p style='color:red'>✗ Error: " . $conn->error . "</p>";
}

// Hash untuk password '123456'
$hashed_password = password_hash('123456', PASSWORD_DEFAULT);

// Insert user default
$users = [
    ['admin', 'admin@educampus.ac.id', $hashed_password, 'Administrator Sistem', 'admin', NULL, '081234567890'],
    ['mahasiswa1', 'mahasiswa1@educampus.ac.id', $hashed_password, 'Ahmad Santoso', 'mahasiswa', '202401001', '081298765432'],
    ['calon1', 'calon1@email.com', $hashed_password, 'Budi Raharjo', 'calon_mahasiswa', NULL, '08111222333']
];

$inserted = 0;
foreach ($users as $user) {
    // Cek dulu apakah user sudah ada
    $check = $conn->query("SELECT id FROM users WHERE username = '{$user[0]}'");
    
    if ($check->num_rows == 0) {
        $sql = "INSERT INTO users (username, email, password, full_name, user_role, nim, phone) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $user[0], $user[1], $user[2], $user[3], $user[4], $user[5], $user[6]);
        
        if ($stmt->execute()) {
            $inserted++;
            echo "<p style='color:green'>✓ User '{$user[0]}' ditambahkan</p>";
        } else {
            echo "<p style='color:red'>✗ Error: " . $stmt->error . "</p>";
        }
        
        $stmt->close();
    } else {
        echo "<p>User '{$user[0]}' sudah ada</p>";
    }
}

echo "<h3>Setup Selesai!</h3>";
echo "<p>Total user ditambahkan: $inserted</p>";

echo "<h3>Akun Login:</h3>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin / 123456</li>";
echo "<li><strong>Mahasiswa:</strong> mahasiswa1 / 123456</li>";
echo "<li><strong>Calon Mahasiswa:</strong> calon1 / 123456</li>";
echo "</ul>";

echo "<p><a href='login.php' style='background:#4CAF50;color:white;padding:15px 30px;text-decoration:none;border-radius:5px;display:inline-block;margin-top:20px;'>Mulai Login</a></p>";

$conn->close();
?>