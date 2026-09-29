<?php
session_start(); // Start the session to manage user data
require 'connectionpage.php'; // Include the file that establishes the database connection

// Verify if the user is logged in and has admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) {
    header('Location: login.php'); // Redirect to login page if not an admin
    exit;
}

// Retrieve the admin ID from the URL
$id = $_GET['id'] ?? null;

// If no admin ID is provided, display an error message
if (!$id) {
    echo "No admin selected.";
    exit;
}

// Fetch the admin's information from the database
$stmt = $datapageConnection->prepare('SELECT * FROM users WHERE id = ? AND isAdmin = 1');
$stmt->execute([$id]);
$admin = $stmt->fetch();

// If the admin does not exist, show an error message
if (!$admin) {
    echo "Admin not found.";
    exit;
}

// Check if the form has been submitted to update admin details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email']; // Get the email from the form
    $name = $_POST['name']; // Get the name from the form
    // Hash the new password if provided; otherwise, retain the current password
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $admin['password'];

    // Update the admin's information in the database
    $update = $datapageConnection->prepare('UPDATE users SET email = ?, name = ?, password = ? WHERE id = ?');
    $update->execute([$email, $name, $password, $id]);

    // Redirect to the manage admins page after the update
    header('Location: manageAdmins.php');
    exit;
}

include 'header.php'; // Include the header file
?>

<h2>Edit Admin</h2>

<!-- Form for updating an existing admin's details -->
<form method="POST">
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required></label><br>
    <label>Password: <input type="password" name="password" placeholder="Leave blank to keep current password"></label><br>
    <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required></label><br>
    <input type="submit" value="Update Admin"> <!-- Submit button to update admin -->
</form>

<?php include 'footer.php'; // Include the footer file ?>