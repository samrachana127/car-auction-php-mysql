<?php
session_start();
require 'connectionpage.php'; // Include the database connection file

// Verify if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login page if not authenticated
    exit();
}

// Initialize an error variable to store any error messages
$error = null;

// Process the form submission when the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']); // Clean the title input
    $description = trim($_POST['description']); // Clean the description input
    $category_id = $_POST['category']; // Get the selected category ID
    $end_date = $_POST['endDate']; // Get the end date
    $user_id = $_SESSION['user']['id']; // Get the logged-in user's ID

    // Handle the image upload
    $imagePath = null; // Initialize the image path variable
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name']; // Temporary file path
        $imageName = basename($_FILES['image']['name']); // Original file name
        
        // Validate the uploaded file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $error = "Error: Only JPG, PNG, and GIF formats are allowed.";
        } elseif ($_FILES['image']['size'] > 2000000) { // Check file size limit (2MB)
            $error = "Error: File size must be under 2MB.";
        } else {
            // Generate a unique file name for the image
            $imageName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $imageName); // Sanitize the filename
            $imagePath = "public/images/auctions/" . $imageName; // Set the path for the image

            // Create the directory if it doesn't exist
            if (!is_dir('public/images/auctions')) {
                mkdir('public/images/auctions', 0755, true); // Create directory with appropriate permissions
            }

            // Move the uploaded image to the specified directory
            if (!move_uploaded_file($imageTmpPath, $imagePath)) {
                $error = "Error occurred while uploading the image.";
            }
        }
    } else {
        $error = "Error: No image was uploaded or there was an issue with the upload.";
    }

    // If there are no errors, insert the new auction into the database
    if (empty($error)) {
        $stmt = $datapageConnection->prepare('INSERT INTO auctions (title, description, category_id, end_date, user_id, image_path) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $description, $category_id, $end_date, $user_id, $imagePath]);

        // Redirect to the auction page after successful addition
        header("Location: auction.php?id=" . $datapageConnection->lastInsertId());
        exit();
    }
}

// Retrieve categories for the dropdown menu
$categories = $datapageConnection->query('SELECT * FROM categories')->fetchAll();

include 'header.php'; // Include the header file
?>

<main>
    <h1>Create Auction</h1>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p> <!-- Display error message if any -->
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data"> <!-- Form for auction creation -->
        Title: <input type="text" name="title" value="<?= htmlspecialchars($title ?? '') ?>" required>
        Description: <textarea name="description" required><?= htmlspecialchars($description ?? '') ?></textarea>
        Category: 
        <select name="category" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= (isset($category_id) && $category_id == $category['id']) ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        End Date: <input type="datetime-local" name="endDate" value="<?= htmlspecialchars($end_date ?? '') ?>" required>
        Image: <input type="file" name="image" accept="image/*" required />
        <input type="submit" value="Add Auction"> <!-- Submit button -->
    </form>
</main>

<?php include 'footer.php'; // Include the footer file ?>