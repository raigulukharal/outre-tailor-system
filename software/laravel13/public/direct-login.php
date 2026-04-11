<?php
// https://software.outre.online/direct-login.php

session_start();

$host = 'sdb-58.hosting.stackcp.net';
$dbname = 'software_outre-35303137b7c6';
$user = 'outre';
$pass = '191152@Rai';

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Find user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            
            // Also set a cookie for Laravel to recognize
            setcookie('auto_login', $user['id'], time() + 3600, '/', '.outre.online');
            
            // Redirect to dashboard with session
            header('Location: /dashboard?autologin=' . $user['id']);
            exit;
        } else {
            $error = "Invalid email or password!";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle auto-login from dashboard redirect
if (isset($_GET['autologin']) && !isset($_SESSION['user_id'])) {
    $userId = $_GET['autologin'];
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
        }
    } catch (PDOException $e) {
        // Ignore error
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>OUTRE Tailor - Direct Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-md mx-auto">
            <!-- Logo -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-indigo-900">OUTRE <span class="text-emerald-600">Tailor</span></h1>
                <p class="text-gray-600 mt-2">Management System</p>
            </div>
            
            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Welcome Back</h2>
                
                <?php if (isset($error)): ?>
                    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        ❌ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id']) && !isset($_POST['email'])): ?>
                    <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        ✅ You are already logged in as <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                    </div>
                    <div class="space-y-3">
                        <a href="/dashboard" class="block w-full text-center bg-indigo-900 text-white font-semibold py-3 rounded-lg hover:bg-indigo-800 transition">
                            Go to Dashboard →
                        </a>
                        <a href="/direct-login.php?logout=1" class="block w-full text-center bg-gray-500 text-white font-semibold py-3 rounded-lg hover:bg-gray-600 transition">
                            Logout
                        </a>
                    </div>
                <?php else: ?>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Email Address</label>
                            <input type="email" name="email" required 
                                   value="admin@outre.online"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Password</label>
                            <input type="password" name="password" required 
                                   value="password123"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                        </div>
                        <button type="submit" class="w-full bg-indigo-900 text-white font-semibold py-3 rounded-lg hover:bg-indigo-800 transition">
                            Sign In
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            
            <!-- Reset Option -->
            <div class="mt-6 text-center">
                <a href="?reset=1" onclick="return confirm('⚠️ WARNING: This will DELETE all orders and reset the database. Continue?')" 
                   class="text-sm text-red-600 hover:text-red-800 underline">
                    🔧 Reset Database (First Time Setup)
                </a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /direct-login.php');
    exit;
}

// Reset database if requested
if (isset($_GET['reset']) && $_GET['reset'] == 1) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        
        // Drop all tables
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $pdo->exec("DROP TABLE IF EXISTS users, orders, sessions, cache, cache_locks, jobs, failed_jobs, password_reset_tokens");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        
        // Create users table
        $pdo->exec("CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");
        
        // Create orders table
        $pdo->exec("CREATE TABLE orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(255) NOT NULL,
            address TEXT NULL,
            serial_no VARCHAR(255) NOT NULL UNIQUE,
            dress_no INT NOT NULL,
            reference_name VARCHAR(255) NULL,
            reference_phone VARCHAR(255) NULL,
            booking_date DATE NOT NULL,
            delivery_date DATE NOT NULL,
            status ENUM('active', 'completed') DEFAULT 'active',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            INDEX orders_phone_index (phone),
            INDEX orders_serial_no_index (serial_no)
        )");
        
        // Create sessions table
        $pdo->exec("CREATE TABLE sessions (
            id VARCHAR(255) PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            payload LONGTEXT NOT NULL,
            last_activity INT NOT NULL,
            INDEX sessions_user_id_index (user_id),
            INDEX sessions_last_activity_index (last_activity)
        )");
        
        // Insert new admin
        $hash = password_hash('password123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute(['Administrator', 'admin@outre.online', $hash]);
        
        // Insert sample order
        $stmt = $pdo->prepare("INSERT INTO orders (name, phone, address, serial_no, dress_no, reference_name, reference_phone, booking_date, delivery_date, status, created_at, updated_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([
            'Sample Customer', '03001234567', 'Sample Address', 'ORD001', 
            101, 'Reference Person', '03007654321', date('Y-m-d'), 
            date('Y-m-d', strtotime('+7 days')), 'active'
        ]);
        
        echo "<script>alert('✅ Database reset complete! Admin user and sample order created.'); window.location.href='/direct-login.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('❌ Error: " . addslashes($e->getMessage()) . "'); window.location.href='/direct-login.php';</script>";
    }
    exit;
}
?>