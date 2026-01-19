<?php
// admin-login.php - Simple admin authentication
include 'db.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Fetch admin user
        if ($pdo) {
            $stmt = $pdo->prepare("SELECT id, username, password FROM admin_users WHERE username = ? AND active = 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
        } else {
            $error = "Database connection error";
            $admin = null;
        }

        if ($admin && password_verify($password, $admin['password'])) {
            // Set session
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Redirect to admin panel
            header('Location: admin-panel.php');
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin-login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Barangay Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-red-700 rounded-full flex items-center justify-center text-white font-bold text-2xl mx-auto mb-4">
                BM
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Admin Login</h1>
            <p class="text-slate-500 text-sm">Barangay Bacsay Mapula-pula Portal</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-slate-700 font-medium mb-2">Username</label>
                <input type="text" name="username" required 
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-red-500">
            </div>

            <div class="mb-6">
                <label class="block text-slate-700 font-medium mb-2">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-red-500">
            </div>

            <button type="submit" 
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition-colors">
                Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="index.php" class="text-red-600 text-sm hover:underline">← Back to Portal</a>
        </div>
    </div>
</body>
</html>