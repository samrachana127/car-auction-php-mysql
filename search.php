<?php
session_start(); // Start the session to manage user data
require 'connectionpage.php'; // Include the script for database connection

// Optional: Uncomment the following lines to require user login for search functionality
// if (!isset($_SESSION['user'])) {
//     header('Location: login.php'); // Redirect to login page if user is not authenticated
//     exit;
// }

// Initialize an array to hold search results and a variable for the search term
$searchResults = [];
$searchTerm = '';

// Handle the search form submission
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search'])) {
    $searchTerm = trim($_GET['search']); // Clean the search term by removing extra spaces
    
    // Prepare the SQL statement to search for cars, ordered by relevance
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
    
    // Create the search term with wildcards for SQL LIKE clause
    $likeTerm = '%' . $searchTerm . '%';
    $stmt->execute([$likeTerm, $likeTerm, $likeTerm, $likeTerm]); // Execute the query with parameters
    
    // Fetch the results from the database
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
                    <?php if (!empty($car['image_path'])): ?>
                        <img src="<?= htmlspecialchars($car['image_path']) ?>" alt="Image of <?= htmlspecialchars($car['title']) ?>" style="width: 100px; height: auto;">
                    <?php else: ?>
                        <img src="default-car.png" alt="Default image" style="width: 100px; height: auto;"> <!-- Default image if no specific image is available -->
                    <?php endif; ?>
                    <article>
                        <h2><?= htmlspecialchars($car['title']) ?></h2>
                        <h3>Seller: <?= htmlspecialchars($car['seller']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($car['description'])) ?></p>
                        <p class="price">Current bid: £<?= number_format($car['current_bid'] ?? 0, 2) ?></p> <!-- Format the current bid amount -->
                        <a class="more auctionLink" href="auction.php?id=<?= htmlspecialchars($car['id']) ?>">View More &gt;&gt;</a>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No results found for "<?= htmlspecialchars($searchTerm) ?>".</p> <!-- Message when no results are found -->
    <?php endif; ?>
</main>

<?php include 'footer.php'; // Include the footer section of the page ?>