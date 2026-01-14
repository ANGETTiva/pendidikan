<?php
// Konfigurasi Aplikasi
session_start();

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'educational_system');

// Aplikasi
define('APP_NAME', 'Sistem Pendidikan');
define('APP_VERSION', '1.0');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>