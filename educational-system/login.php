<?php
require_once 'config.php';
require_once 'functions.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$message = getMessage();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #333;
            margin: 10px 0;
        }
        
        .logo p {
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            border-color: #667eea;
            outline: none;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #dfd;
            color: #383;
            border: 1px solid #afa;
        }
        
        .demo-credentials {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }
        
        .demo-credentials strong {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }
        
        .demo-credentials code {
            background: #f5f5f5;
            padding: 3px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1><?php echo APP_NAME; ?></h1>
            <p>Sistem Manajemen Pendidikan</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message['type']; ?>">
                <?php echo $message['text']; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="process_login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                       placeholder="Masukkan username" required 
                       value="admin">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" 
                       placeholder="Masukkan password" required 
                       value="123456">
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <div class="demo-credentials">
            <strong>Akun Demo:</strong>
            <div>Admin: <code>admin / 123456</code></div>
            <div>Mahasiswa: <code>mahasiswa1 / 123456</code></div>
            <div>Calon: <code>calon1 / 123456</code></div>
        </div>
    </div>
    
    <script>
        // Auto-fill demo credentials on click
        document.querySelectorAll('.demo-credentials code').forEach(code => {
            code.addEventListener('click', function() {
                const [username, password] = this.textContent.split(' / ');
                document.getElementById('username').value = username.trim();
                document.getElementById('password').value = password.trim();
            });
        });
    </script>
</body>
</html>