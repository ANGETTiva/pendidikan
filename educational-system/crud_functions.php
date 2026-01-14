<?php
require_once 'config.php';
require_once 'functions.php';

// ==================== FUNGSI UMUM CRUD ====================

/**
 * Ambil semua data dari tabel
 */
function getAll($table, $where = "", $params = []) {
    $conn = getDB();
    $sql = "SELECT * FROM $table";
    
    if ($where) {
        $sql .= " WHERE $where";
    }
    
    $sql .= " ORDER BY id DESC";
    
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $result = $conn->query($sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $conn->close();
    return $data;
}

/**
 * Ambil satu data berdasarkan ID
 */
function getById($table, $id) {
    $conn = getDB();
    $stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $data;
}

/**
 * Tambah data baru
 */
function createData($table, $data) {
    $conn = getDB();
    
    // Siapkan SQL
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameter
    $types = '';
    $values = [];
    
    foreach ($data as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $value;
    }
    
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        $stmt->close();
        $conn->close();
        return ['success' => true, 'id' => $id];
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return ['success' => false, 'error' => $error];
    }
}

/**
 * Update data
 */
function updateData($table, $id, $data) {
    $conn = getDB();
    
    // Siapkan SQL
    $setClause = [];
    foreach (array_keys($data) as $column) {
        $setClause[] = "$column = ?";
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameter
    $types = '';
    $values = [];
    
    foreach ($data as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $value;
    }
    
    $types .= 'i'; // untuk id
    $values[] = $id;
    
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        $affected = $stmt->affected_rows;
        $stmt->close();
        $conn->close();
        return ['success' => true, 'affected' => $affected];
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return ['success' => false, 'error' => $error];
    }
}

/**
 * Hapus data
 */
function deleteData($table, $id) {
    $conn = getDB();
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $affected = $stmt->affected_rows;
        $stmt->close();
        $conn->close();
        return ['success' => true, 'affected' => $affected];
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return ['success' => false, 'error' => $error];
    }
}

/**
 * Validasi input
 */
function validateInput($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? '';
        
        // Required
        if (isset($rule['required']) && $rule['required'] && empty($value)) {
            $errors[$field] = $rule['message'] ?? "Field $field harus diisi";
        }
        
        // Min length
        if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
            $errors[$field] = $rule['message'] ?? "Minimal {$rule['min_length']} karakter";
        }
        
        // Max length
        if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
            $errors[$field] = $rule['message'] ?? "Maksimal {$rule['max_length']} karakter";
        }
        
        // Email
        if (isset($rule['email']) && $rule['email'] && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[$field] = "Format email tidak valid";
        }
        
        // Numeric
        if (isset($rule['numeric']) && $rule['numeric'] && !is_numeric($value)) {
            $errors[$field] = "Harus berupa angka";
        }
    }
    
    return $errors;
}

// ==================== FUNGSI SPESIFIK ====================

/**
 * Ambil semua mata kuliah
 */
function getAllMataKuliah() {
    return getAll('mata_kuliah');
}

/**
 * Ambil semua pendaftaran berdasarkan user
 */
function getPendaftaranByUser($user_id) {
    return getAll('pendaftaran', 'user_id = ?', [$user_id]);
}

/**
 * Ambil semua nilai berdasarkan mahasiswa
 */
function getNilaiByMahasiswa($mahasiswa_id) {
    $conn = getDB();
    $sql = "SELECT n.*, mk.nama_mk, mk.kode_mk, mk.sks 
            FROM nilai n 
            JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id 
            WHERE n.mahasiswa_id = ? 
            ORDER BY n.semester DESC, n.tahun_ajaran DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $mahasiswa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $data;
}

/**
 * Ambil semua KRS berdasarkan mahasiswa
 */
function getKRSByMahasiswa($mahasiswa_id) {
    $conn = getDB();
    $sql = "SELECT k.*, mk.nama_mk, mk.kode_mk, mk.sks 
            FROM krs k 
            JOIN mata_kuliah mk ON k.mata_kuliah_id = mk.id 
            WHERE k.mahasiswa_id = ? 
            ORDER BY k.semester DESC, k.tahun_ajaran DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $mahasiswa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $data;
}

/**
 * Hitung total SKS yang diambil mahasiswa
 */
function getTotalSKSMahasiswa($mahasiswa_id, $semester, $tahun_ajaran) {
    $conn = getDB();
    $sql = "SELECT SUM(mk.sks) as total_sks 
            FROM krs k 
            JOIN mata_kuliah mk ON k.mata_kuliah_id = mk.id 
            WHERE k.mahasiswa_id = ? AND k.semester = ? AND k.tahun_ajaran = ? 
            AND k.status = 'disetujui'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $mahasiswa_id, $semester, $tahun_ajaran);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $row['total_sks'] ?? 0;
}

/**
 * Konversi nilai angka ke huruf
 */
function konversiNilai($angka) {
    if ($angka >= 85) return 'A';
    if ($angka >= 80) return 'A-';
    if ($angka >= 75) return 'B+';
    if ($angka >= 70) return 'B';
    if ($angka >= 65) return 'B-';
    if ($angka >= 60) return 'C+';
    if ($angka >= 55) return 'C';
    if ($angka >= 50) return 'C-';
    if ($angka >= 40) return 'D';
    return 'E';
}
?>