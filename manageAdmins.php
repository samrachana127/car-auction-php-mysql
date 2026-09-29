<?php
session_start();
require 'connectionpage.php';

// Ensure the user is authenticated and has admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] !== 1) {
    header('Location: login.php');
    exit;
}

// Check if an admin account should be deleted
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $adminId = $_GET['id'];

    // Remove the admin from the database
    $deleteStmt = $datapageconnection->prepare('DELETE FROM users WHERE id = ? AND isAdmin = 1');
    $deleteStmt->execute([$adminId]);

    // Redirect after deletion
    header('Location: manageAdmins.php');
    exit;
}

// Retrieve all admin user records
$adminQuery = $datapageConnection->query('SELECT * FROM users WHERE isAdmin = 1');
$adminList = $adminQuery->fetchAll();

include 'header.php';
?>

<h2>Administrator Management</h2>

<!-- Display list of admin users -->
<ul>
    <?php foreach ($adminList as $admin): ?>
        <li>
            <?= htmlspecialchars($admin['name']) ?> (<?= htmlspecialchars($admin['email']) ?>)
            <a href="editAdmin.php?id=<?= $admin['id'] ?>">Edit</a>
            <a href="manageAdmins.php?action=delete&id=<?= $admin['id'] ?>" onclick="return confirm('Are you sure you want to remove this admin?');">Delete</a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Link to create a new admin account -->
<a href="addAdmin.php">Create New Admin</a>

<?php include 'footer.php'; ?>
