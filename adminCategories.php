<?php
// adminCategories.php – Admin interface for viewing and editing all categories
require 'connectionpage.php';
// Only allow access if user is logged in and has admin rights
if (!isset($_SESSION['user']) || !$_SESSION['user']['isAdmin']) {
    header('Location: login.php');
    exit;
}

// Query the database for all category records
$allCategories = $pdo->query('SELECT * FROM category')->fetchAll();

include 'header.php';
?>

<h2>Category Dashboard</h2>
<p><a href="addCategory.php">Add New Category</a></p>

<ul>
    <?php foreach ($allCategories as $item): ?>
        <li>
            <?= htmlspecialchars($item['name']) ?>
            <a href="editCategory.php?id=<?= $item['id'] ?>">Edit</a> |
            <a href="deleteCategory.php?id=<?= $item['id'] ?>" onclick="return confirm('Do you want to remove this category?');">Delete</a>
        </li>
    <?php endforeach; ?>
</ul>

<?php include 'footer.php'; ?>
