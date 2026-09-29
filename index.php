<?php
session_start(); // Start a new session or resume the existing one
require 'connectionpage.php'; // Include the script for database connection

// Optional: Uncomment the following lines to restrict access to logged-in users only
// if (!isset($_SESSION['user'])) {
//     header('Location: login.php'); // Redirect to login page if not authenticated
//     exit;
// }

// Initialize an array to hold search results and a variable for the search term
$searchResults = [];
$searchTerm = '';

// Check if the search form has been submitted via GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search'])) {
    $searchTerm = trim($_GET['search']); // Clean the search term by removing unnecessary spaces
    
    // Prepare a SQL statement to search for auctions based on title or description
    $stmt = $datapageConnection->prepare('
        SELECT auctions.*, users.name AS seller 
        FROM auctions 
        JOIN users ON auctions.user_id = users.id 
        WHERE title LIKE ? OR description LIKE ? 
        ORDER BY 
            CASE 
                WHEN title LIKE ? THEN 1 
                WHEN description LIKE ? THEN 2 
                ELSE 3 
            END, title ASC
    ');
    
    // Create a wildcard search term for SQL LIKE clause
    $likeTerm = '%' . $searchTerm . '%';
    $stmt->execute([$likeTerm, $likeTerm, $likeTerm, $likeTerm]); // Execute the query with parameters
    
    // Fetch all results that match the search criteria
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'header.php'; // Include the header section of the page
?>

<main>
    <h2>Search Results</h2>

    <?php if ($searchResults): ?>
        <h3>Results for "<?= htmlspecialchars($searchTerm) ?>"</h3>
        <ul class="carList">
            <?php foreach ($searchResults as $car): ?>
                <li>
                    <img src="<?= htmlspecialchars($car['image_path'] ?? 'default-car.png') ?>" 
                         alt="Image of <?= htmlspecialchars($car['title']) ?>" 
                         style="width: 100px; height: auto;"> <!-- Display car image or default if not available -->
                    <article>
                        <h2><?= htmlspecialchars($car['title']) ?></h2>
                        <h3>Seller: <?= htmlspecialchars($car['seller']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($car['description'])) ?></p> <!-- Convert newlines to <br> -->
                        <p class="price">Current bid: £<?= number_format($car['current_bid'] ?? 0, 2) ?></p> <!-- Format the current bid -->
                        <a class="more auctionLink" href="auction.php?id=<?= htmlspecialchars($car['id']) ?>">More &gt;&gt;</a> <!-- Link to auction details -->
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No results found for "<?= htmlspecialchars($searchTerm) ?>".</p> <!-- Message when no results are found -->
    <?php endif; ?>
</main>

<?php include 'footer.php'; // Include the footer section of the page ?>