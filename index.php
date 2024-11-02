
<?php
require_once 'config.php';
require_once 'database.php';
require_once 'auth.php';

$db = new Database();
$auth = new Auth($db);

$categoriesResult = $db->query('SELECT * FROM categories');
$categories = [];
if ($categoriesResult) {
    while ($row = $categoriesResult->fetchArray(SQLITE3_ASSOC)) {
        $categories[] = $row;
    }
}

$category_id = $_GET['category'] ?? null;

$sql = 'SELECT i.*, c.name as category_name, u.username 
        FROM images i 
        LEFT JOIN categories c ON i.category_id = c.id 
        LEFT JOIN users u ON i.user_id = u.id';
if ($category_id) {
    $sql .= ' WHERE i.category_id = :category_id';
}
$images = $db->query($sql, [':category_id' => $category_id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-xl font-bold">Image Gallery</a>
            <div class="space-x-4">
                <?php if ($auth->isLoggedIn()): ?>
                    <a href="upload.php" class="text-blue-500">Upload Image</a>
                    <?php if ($auth->isAdmin()): ?>
                        <a href="admin.php" class="text-green-500">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="text-red-500">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-blue-500">Login</a>
                    <a href="register.php" class="text-green-500">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-4">
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">Categories</h2>
            <div class="flex space-x-4">
                <a href="index.php" class="px-4 py-2 bg-blue-500 text-white rounded">All</a>
                <?php foreach ($categories as $category): ?>
                    <a href="?category=<?= $category['id'] ?>" 
                       class="px-4 py-2 bg-blue-500 text-white rounded">
                        <?= htmlspecialchars($category['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php if ($images): ?>
                <?php while ($image = $images->fetchArray(SQLITE3_ASSOC)): ?>
                    <div class="bg-white p-4 rounded shadow">
                        <img src="<?= UPLOAD_DIR . $image['filename'] ?>" 
                             alt="Gallery Image" 
                             class="w-full h-48 object-cover mb-2">
                        <p class="text-sm text-gray-600">
                            Category: <?= htmlspecialchars($image['category_name']) ?>
                        </p>
                        <p class="text-sm text-gray-600">
                            Uploaded by: <?= htmlspecialchars($image['username']) ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>