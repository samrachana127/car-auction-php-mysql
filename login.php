<?php
session_start();  // Start the session to manage user data

// Check if there is a logout message to display
if (isset($_SESSION['logout_message'])) {
    $logoutMessage = $_SESSION['logout_message']; // Store the message
    unset($_SESSION['logout_message']); // Clear the message after displaying it
}

// Include the database connection
require 'connectionpage.php';  // Ensure the path to the connection file is correct

// Process the form submission only if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted data from the form
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);  // Store the password in a variable

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        // Check if the email exists in the users table
        $checkUser  = $datapageConnection->prepare('SELECT * FROM users WHERE email = ?');
        $checkUser ->execute([$email]);

        // Fetch the user details if found
        $user = $checkUser ->fetch(PDO::FETCH_ASSOC); // Fetch as an associative array

        // If the user is found and the password matches, log them in
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);
            
            // Store the user data in the session to keep them logged in
            $_SESSION['user'] = $user;

            // Check if the user has admin privileges
            if ($user['isAdmin']) {
                // Redirect to the admin dashboard
                header('Location: adminCategory.php');  // Change to your admin page
            } else {
                // Redirect to the user homepage
                header('Location: index.php');  // Change to your user homepage
            }
            exit;
        } else {
            // If email/password doesn't match, display an error message
            $error = 'Email or password is incorrect.';
        }
    }
}
?>

<?php include 'header.php'; ?>
<h2>Log In</h2>

<!-- Display error message if login fails -->
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<!-- Login form -->
<form method="POST">
    <label>Email:
        <input type="email" name="email" required>
    </label><br>
    <label>Password:
        <input type="password" name="password" required>
    </label><br>
    <input type="submit" name="submit" value="Login">
</form>

<?php include 'footer.php'; ?>