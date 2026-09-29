<?php
session_start();
require 'connectionpage.php';

// Verify if the user is logged in and has admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) {
    header('Location: login.php');
    exit;
}

// Process the form submission to add a new category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];

    // Prepare and execute the SQL statement to insert the new category
    $stmt = $datapageConnection->prepare('INSERT INTO category (name) VALUES (?)');
    $stmt->execute([$name]);

    // Redirect back to the category management page after adding
    header('Location: adminCategories.php');
    exit;
}
?>

<?php include 'header.php'; ?>

<h2>Add New Category</h2>

<!-- Form for the admin to create a new category -->
<form method="POST">
    <label>Name: <input type="text" name="name" required></label>
    <input type="submit" value="Add">
</form>

<?php include 'footer.php'; ?>