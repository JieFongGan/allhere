<?php
ob_start(); // Start output buffering
$pageTitle = "Settings/category";
require_once("../database/database-connect.php");
include '../contain/header.php';

// Fetch categories using PDO
$sql = "SELECT * FROM Category";
$result = $conn->query($sql);

$categories = [];

if ($result !== false) {
    $categories = $result->fetchAll(PDO::FETCH_ASSOC);
}

/// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['deleteCategory'])) {
        $categoryIDToDelete = $_POST['deleteCategory'];
        $deleteSql = "DELETE FROM Category WHERE CategoryID = :categoryIDToDelete";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bindParam(':categoryIDToDelete', $categoryIDToDelete, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Refresh the categories list after successful deletion
            header("Location: settings-category.php");
            exit();
        } else {
            echo "Error deleting category: " . $stmt->errorInfo()[2];
        }
    }

    if (isset($_POST['newCategory'])) {
        $newCategory = $_POST["newCategory"];
        $description = $_POST["description"];

        // Check if the category already exists
        if (!in_array($newCategory, array_column($categories, 'Name'))) {
            // Insert the new category into the database
            $insertSql = "INSERT INTO Category (Name, Description) VALUES (:newCategory, :description)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bindParam(':newCategory', $newCategory, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Refresh the categories list after successful insertion
                header("Location: settings-category.php");
                exit();
            } else {
                echo "Error adding new category: " . $stmt->errorInfo()[2];
            }
        } else {
            echo "Category already exists!";
        }
    }
}

// Close the database connection
$conn = null;
?>

<div class="main-content">
    <?php
    $pathtitle = "Settings/category";
    include '../contain/horizontal-bar.php';
    ?>
    <main>
        <div class="form-container">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <div class="form-group">
                    <label for="newCategory">New Category Name :</label>
                    <input type="text" id="newCategory" name="newCategory" placeholder="Enter a new category" required>
                </div>
                <div class="form-group">
                    <label for="description">New Category Description:</label>
                    <textarea id="description" name="description" placeholder="Please enter the description" required></textarea>
                </div>
                <div class="form-group">
                    <button type="submit">Add Category</button>
                </div>
            </form>

            <h2>All Categories</h2>
            <?php if (empty($categories)): ?>
                <p>No categories available.</p>
            <?php else: ?>
                <ul class="category-list">
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                                <div class="category-details">
                                    <strong class="category-name">
                                        <?= $category['Name'] ?>
                                    </strong>
                                    <?php if (!empty($category['Description'])): ?>
                                        <p class="category-description">
                                            <?= $category['Description'] ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" name="deleteCategory" value="<?= $category['CategoryID'] ?>">
                                <button type="submit" class="delete-button"
                                    onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>
</html>
