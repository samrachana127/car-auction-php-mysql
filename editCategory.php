<?php
session_start(); // Start the session to manage user data
require 'connectionpage.php'; // Include the database connection file

// Ensure that only admins can access this page
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) {
    header('Location: login.php'); // Redirect to login page if the user is not an admin
    exit;
}

// Retrieve the category ID from the URL
$id = $_GET['id'] ?? null;

// If no category ID is provided, display an error message
if (!$id) {
    echo "No category selected.";
    exit;
}

// Fetch the category information from the database to display the current value
$stmt = $datapageConnection->prepare('SELECT * FROM categories WHERE id = ?');
$stmt->execute([$id]);
$category = $stmt->fetch();

// Check if the category exists in the database
if (!$category) {
    echo "Category not found.";
    exit;
}

// Process the form submission to update the category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['name']); // Get the new category name from the form

    // Validate the new category name
    if (empty($newName)) {
        $errorMessage = "Category name cannot be empty."; // Error if the name is empty
    } elseif (strlen($newName) < 3) {
        $errorMessage = "Category name must be at least 3 characters long."; // Error if the name is too short
    } else {
        // Update the category name in the database
        $update = $datapageConnection->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $update->execute([$newName, $id]);

        // Redirect to the category management page after updating
        header('Location: adminCategories.php');
        exit;
    }
}
?>

<?php include 'header.php'; // Include the header file ?>

<h2>Edit Category</h2>

<!-- Display error message if it exists -->
<?php if (isset($errorMessage)): ?>
    <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
<?php endif; ?>

<!-- Form to edit the current category name -->
<form method="POST">
    <label>Name:
        <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
    </label>
    <input type="submit" value="Update"> <!-- Submit button to update the category -->
</form>

<?php include 'footer.php'; // Include the footer file ?>