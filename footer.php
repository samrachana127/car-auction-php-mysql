<?php
// Fetch categories for the navigation
$categoriesStmt = $datapageConnection->prepare('SELECT * FROM categories ORDER BY name');
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();
?> 
<footer>
		&copy; Carbuy 2024
	</footer>
</body>
</html>
