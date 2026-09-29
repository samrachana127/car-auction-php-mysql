<?php
session_start(); // Start the session to manage user data
require 'connectionpage.php'; // Include the database connection file

// Verify if the user is logged in and has admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) {
    header('Location: login.php'); // Redirect to login page if not an admin
    exit;
}

// Retrieve the category ID from the URL
$id = $_GET['id'] ?? null;

// If a category ID is provided, proceed to delete the category
if ($id) {
    // Check if the category exists in the database
    $checkStmt = $datapageConnection->prepare('SELECT * FROM categories WHERE id = ?');
    $checkStmt->execute([$id]);
    $category = $checkStmt->fetch();

    if ($category) {
        // Delete all auctions that are linked to this category
        $deleteAuctionsStmt = $datapageConnection->prepare('DELETE FROM auctions WHERE category_id = ?');
        $deleteAuctionsStmt->execute([$id]);

        // Now delete the category itself
        $stmt = $datapageConnection->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);

        // Set a success message to indicate successful deletion
        $_SESSION['message'] = "Category and its associated auctions have been deleted successfully.";
    } else {
        // Set an error message if the category does not exist
        $_SESSION['error'] = "The specified category was not found.";
    }
} else {
    // Set an error message if no category ID was provided
    $_SESSION['error'] = "No category ID was specified.";
}

// Redirect the user back to the category management page
header('Location: adminCategories.php');
exit;
?>