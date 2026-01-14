<?php
require_once 'config.php';
require_once 'functions.php';

echo "<h2>Setup Tabel CRUD</h2>";

$conn = getDB();

// 1. Tabel Mata Kuliah
$sql = "CREATE TABLE IF NOT EXISTS mata_kuliah (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_mk VARCHAR(10) UNIQUE NOT NULL,
    nama_mk VARCHAR(100) NOT NULL,
    sks INT NOT NULL,
    semester INT NOT NULL,
    dosen_pengampu VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Tabel mata_kuliah dibuat</p>";
    
    // Insert contoh data
    $contoh = [
        ['MK001', 'Pemrograman Web', 3, 3, 'Dr. Rina Wijaya'],
        ['MK002', 'Basis Data', 3, 2, 'Prof. Ahmad Sulaiman'],
        ['MK003', 'Algoritma & Struktur Data', 4, 1, 'Dr. Sari Dewi']
    ];
    
    foreach ($contoh as $mk) {
        $check = $conn->query("SELECT id FROM mata_kuliah WHERE kode_mk = '{$mk[0]}'");
        if ($check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO mata_kuliah (kode_mk, nama_mk, sks, semester, dosen_pengampu) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiis", $mk[0], $mk[1], $mk[2], $mk[3], $mk[4]);
            $stmt->execute();
            echo "<p>✓ Mata kuliah '{$mk[1]}' ditambahkan</p>";
        }
    }
} else {
    echo "<p style='color:red'>✗ Error: " . $conn->error . "</p>";
}

// 2. Tabel Pendaftaran PMB
$sql = "CREATE TABLE IF NOT EXISTS pendaftaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(50) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    alamat TEXT NOT NULL,
    asal_sekolah VARCHAR(100) NOT NULL,
    program_studi VARCHAR(50) NOT NULL,
    tahun_lulus INT NOT NULL,
    nilai_akhir DECIMAL(4,2) NOT NULL,
    status ENUM('pending', 'diverifikasi', 'diterima', 'ditolak') DEFAULT 'pending',
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Tabel pendaftaran dibuat</p>";
} else {
    echo "<p style='color:red'>✗ Error: " . $conn->error . "</p>";
}

// 3. Tabel Nilai
$sql = "CREATE TABLE IF NOT EXISTS nilai (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mahasiswa_id INT NOT NULL,
    mata_kuliah_id INT NOT NULL,
    nilai_angka DECIMAL(5,2) NOT NULL,
    nilai_huruf VARCHAR(2) NOT NULL,
    semester INT NOT NULL,
    tahun_ajaran VARCHAR(9) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Tabel nilai dibuat</p>";
} else {
    echo "<p style='color:red'>✗ Error: " . $conn->error . "</p>";
}

// 4. Tabel KRS (Kartu Rencana Studi)
$sql = "CREATE TABLE IF NOT EXISTS krs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mahasiswa_id INT NOT NULL,
    mata_kuliah_id INT NOT NULL,
    semester INT NOT NULL,
    tahun_ajaran VARCHAR(9) NOT NULL,
    status ENUM('pending', 'disetujui', 'ditolak') DEFAULT 'pending',
    tanggal_pengajuan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green'>✓ Tabel krs dibuat</p>";
} else {
    echo "<p style='color:red'>✗ Error: " . $conn->error . "</p>";
}

echo "<h3 style='color:green'>✓ Setup CRUD Selesai!</h3>";
echo "<p><a href='dashboard.php' style='background:#4CAF50;color:white;padding:10px;text-decoration:none;border-radius:5px;'>Kembali ke Dashboard</a></p>";

$conn->close();
?>