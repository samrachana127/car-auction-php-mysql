<?php
session_start();
require 'connectionpage.php'; // Include the file that establishes the database connection

// Verify if the user is logged in and has admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) {
    header('Location: login.php'); // Redirect to login if not an admin
    exit;
}

// Process the form submission for adding a new administrator
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); // Clean up the email input
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Securely hash the password
    $name = trim($_POST['name']); // Clean up the name input

    // Check if the provided email is already in use
    $checkEmailStmt = $datapageConnection->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $checkEmailStmt->execute([$email]);
    $emailExists = $checkEmailStmt->fetchColumn(); // Get the count of users with that email

    if ($emailExists) {
        $error = "This email is already registered."; // Set error message if email exists
    } else {
        // Insert the new admin details into the database
        $stmt = $datapageConnection->prepare('INSERT INTO users (email, password, name, isAdmin) VALUES (?, ?, ?, 1)');
        if ($stmt->execute([$email, $password, $name])) {
            // Redirect to the admin management page upon successful addition
            header('Location: manageAdmins.php');
            exit;
        } else {
            $error = "Failed to add the admin. Please try again."; // Set error message if insertion fails
        }
    }
}

include 'header.php'; // Include the header file
?>

<h2>Add Admin</h2>

<!-- Show error message if there was an issue with the email or adding the admin -->
<?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p> <!-- Display error message -->
<?php endif; ?>

<!-- Form for entering new admin details -->
<form method="POST">
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <label>Name: <input type="text" name="name" required></label><br>
    <input type="submit" value="Add Admin"> <!-- Submit button to add admin -->
</form>

<?php include 'footer.php'; // Include the footer file ?> 