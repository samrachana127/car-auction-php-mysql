<?php
session_start(); // Initialize the session to handle user data
require 'connectionpage.php'; // Include the script for database connection

// Get the category ID from the URL and ensure it's a valid number
$categoryId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

// Redirect to the homepage if the category ID is invalid
if (!$categoryId) {
    header('Location: index.php');
    exit; // Stop further execution
}

// Query to fetch category details from the database
$categoryQuery = $datapageConnection->prepare('SELECT * FROM categories WHERE id = ?');
$categoryQuery->execute([$categoryId]);
$categoryDetails = $categoryQuery->fetch(PDO::FETCH_ASSOC);

// Redirect to the homepage if the category does not exist
if (!$categoryDetails) {
    header('Location: index.php');
    exit; // Stop further execution
}

// Query to retrieve auctions linked to the selected category
$auctionsQuery = $datapageConnection->prepare('
    SELECT auctions.*, users.name AS seller 
    FROM auctions 
    JOIN users ON auctions.user_id = users.id 
    WHERE auctions.category_id = ? 
    ORDER BY end_date ASC
');
$auctionsQuery->execute([$categoryId]);
$auctionList = $auctionsQuery->fetchAll(PDO::FETCH_ASSOC); // Fetch all auctions

include 'header.php'; // Include the header section
?>

<main>
    <h1>Auctions in the Category: <?= htmlspecialchars($categoryDetails['name']) ?></h1>

    <?php if ($auctionList): ?>
        <ul class="carList">
            <?php foreach ($auctionList as $auction): ?>
                <li>
                    <img src="<?= htmlspecialchars($auction['image_path'] ?? 'car.png') ?>" alt="Image of <?= htmlspecialchars($auction['title']) ?>"> <!-- Fallback image if none is provided -->
                    <article>
                        <h2><?= htmlspecialchars($auction['title']) ?></h2>
                        <h3>Seller: <?= htmlspecialchars($auction['seller']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
                        <p class="price">
                            Current bid: £<?= number_format($auction['currentBid'] ?? 0, 2) ?>
                        </p>
                        <a class="more auctionLink" href="auction.php?id=<?= htmlspecialchars($auction['id']) ?>">View Details &gt;&gt;</a>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>There are currently no auctions available in this category.</p> <!-- Message when no auctions are found -->
    <?php endif; ?>
</main>

<?php include 'footer.php'; // Include the footer section ?>