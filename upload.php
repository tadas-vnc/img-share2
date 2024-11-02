<?php
require_once 'config.php';
require_once 'database.php';
require_once 'auth.php';

$db = new Database();
$auth = new Auth($db);

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$categories = $db->query('SELECT * FROM categories');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['image'];
    $category_id = $_POST['category_id'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = UPLOAD_DIR . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $db->query(
                'INSERT INTO images (filename, category_id, user_id) VALUES (:filename, :category_id, :user_id)',
                [
                    ':filename' => $filename,
                    ':category_id' => $category_id,
                    ':user_id' => $_SESSION['user_id']
                ]
            );
            header('Location: index.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image - Image Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg p-4 mb-8">
        <div class="container mx-auto">
            <a href="index.php" class="text-xl font-bold">← Back to Gallery</a>
        </div>
    </nav>

    <div class="container mx-auto p-4">
        <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
            <h1 class="text-2xl font-bold mb-6">Upload Image</h1>
            
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block mb-2">Select Category:</label>
                    <select name="category_id" required class="w-full px-4 py-2 border rounded">
                        <?php while ($category = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                            <option value="<?= $category['id'] ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block mb-2">Choose Image:</label>
                    <input type="file" 
                           name="image" 
                           required 
                           accept="image/*" 
                           class="w-full">
                </div>
                
                <button type="submit" 
                        class="w-full px-6 py-2 bg-blue-500 text-white rounded">
                    Upload Image
                </button>
            </form>
        </div>
    </div>
</body>
</html>
