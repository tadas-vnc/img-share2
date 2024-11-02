
<?php
require_once 'config.php';
require_once 'database.php';
require_once 'auth.php';

$db = new Database();
$auth = new Auth($db);

if (!$auth->isAdmin()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $db->query(
            'INSERT INTO categories (name) VALUES (:name)',
            [':name' => $_POST['category_name']]
        );
    } elseif (isset($_POST['delete_category'])) {
        $db->query(
            'DELETE FROM categories WHERE id = :id',
            [':id' => $_POST['category_id']]
        );
    }
    header('Location: admin.php');
    exit;
}

$categories = $db->query('SELECT * FROM categories');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Image Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg p-4 mb-8">
        <div class="container mx-auto">
            <a href="index.php" class="text-xl font-bold">← Back to Gallery</a>
        </div>
    </nav>

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-8">Admin Panel</h1>

        <div class="bg-white p-6 rounded shadow mb-8">
            <h2 class="text-xl font-bold mb-4">Add New Category</h2>
            <form method="POST" class="flex gap-4">
                <input type="text" 
                       name="category_name" 
                       required 
                       class="flex-1 px-4 py-2 border rounded"
                       placeholder="Category Name">
                <button type="submit" 
                        name="add_category" 
                        class="px-6 py-2 bg-green-500 text-white rounded">
                    Add Category
                </button>
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Manage Categories</h2>
            <div class="space-y-4">
                <?php while ($category = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                    <div class="flex justify-between items-center border-b pb-4">
                        <span class="text-lg">
                            <?= htmlspecialchars($category['name']) ?>
                        </span>
                        <form method="POST" class="inline">
                            <input type="hidden" 
                                   name="category_id" 
                                   value="<?= $category['id'] ?>">
                            <button type="submit" 
                                    name="delete_category" 
                                    class="px-4 py-2 bg-red-500 text-white rounded"
                                    onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>