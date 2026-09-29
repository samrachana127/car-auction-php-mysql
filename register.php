<?php
// Include the database connection file
require 'connectionpage.php';

// This block executes when the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user input from the form fields
    $email = $_POST['email'];
    $name = $_POST['name'];

    // Securely hash the password for storage (recommended practice)
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    // Determine if the user is registering as an admin based on checkbox input
    $isAdmin = isset($_POST['isAdmin']) ? 1 : 0; 

    // Check if the provided email is already registered in the database
    $checkEmail = $datapageConnection->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $checkEmail->execute([$email]);
    $emailExists = $checkEmail->fetchColumn();

    // If the email is found, display an error message
    if ($emailExists > 0) {
        echo "<p style='color:red;'>This email is already registered. Please use a different email.</p>";
    } else {
        // Prepare the SQL statement to insert a new user into the database
        $addUsers = $datapageConnection->prepare('INSERT INTO users (email, password, name, isAdmin) VALUES (?, ?, ?, ?)');
        // Execute the SQL statement with the user-provided values
        $addUsers->execute([$email, $password, $name, $isAdmin]);

        // Redirect the user to the login page after successful registration
        header('Location: login.php');
        exit;
    }
}
?>

<?php include 'header.php'; ?>
<h2> Register</h2>
<!-- Registration form for new users -->
<form method="POST">
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Name: <input type="text" name="name" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <label>
        <input type="checkbox" name="isAdmin"> Register as Admin
    </label><br>
    <input type="submit" value="Register">
</form>
<?php include 'footer.php'; ?>