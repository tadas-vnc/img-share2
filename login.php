<?php
require_once 'config.php';
require_once 'database.php';
require_once 'auth.php';

$db = new Database();
$auth = new Auth($db);

if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($auth->login($_POST['username'], $_POST['password'])) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Image Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white p-8 rounded shadow">
            <h1 class="text-2xl font-bold mb-6">Login</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block mb-2">Username:</label>
                    <input type="text" 
                           name="username" 
                           required 
                           class="w-full px-4 py-2 border rounded">
                           </div>
                
                <div>
                    <label class="block mb-2">Password:</label>
                    <input type="password" 
                           name="password" 
                           required 
                           class="w-full px-4 py-2 border rounded">
                </div>
                
                <button type="submit" 
                        class="w-full px-6 py-2 bg-blue-500 text-white rounded">
                    Login
                </button>
                
                <p class="text-center">
                    Don't have an account? 
                    <a href="register.php" class="text-blue-500">Register here</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>