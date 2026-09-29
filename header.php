<?php
// Retrieve categories from the database for the navigation menu
$categoriesQuery = $datapageConnection->prepare('SELECT * FROM categories ORDER BY name');
$categoriesQuery->execute();
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>
<body>
    <header>
        <h1>
            <span class="C">C</span>
            <span class="a">a</span>
            <span class="r">r</span>
            <span class="b">b</span>
            <span class="u">u</span>
            <span class="y">y</span>
        </h1>

        <?php if (basename($_SERVER['PHP_SELF']) !== 'login.php'): ?>
            <form action="search.php" method="GET" aria-label="Search for a car">
                <input type="text" name="search" placeholder="Search for a car" required aria-label="Search term" />
                <input type="submit" value="Search" />
            </form>
        <?php endif; ?>
    </header>

    <?php if (basename($_SERVER['PHP_SELF']) !== 'login.php'): ?>
        <nav>
            <ul>
                <?php foreach ($categories as $category): ?>
                    <li>
                        <a class="categoryLink" href="category.php?id=<?= htmlspecialchars($category['id']) ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <li><a class="categoryLink" href="#">More</a></li>
            </ul>
        </nav>

        <nav>
            <ul>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="addAuction.php">Add Auction</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <img src="banners/1.jpg" alt="Banner" />
    <main>
        <!-- Main content will be added here -->
    </main>
</body>
</html>