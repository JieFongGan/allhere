<?php
ob_start(); // Start output buffering
$pageTitle = "Inventory/Create";
include '../database/database-connect.php';
include '../contain/header.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $productName = $_POST['productName'];
    $categoryID = $_POST['category'];
    $warehouseID = !empty($_POST['productWarehouse']) ? $_POST['productWarehouse'] : null;
    $description = $_POST['productDescription'];
    $price = $_POST['productPrice'];
    $quantity = $_POST['productQuantity'];

    // Additional validation checks
    if (empty($productName)) {
        $errors[] = "Product name is required.";
    }

    if (empty($categoryID)) {
        $errors[] = "Category is required.";
    }

    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Price must be a valid positive number.";
    }

    // If there are validation errors, display them and stop further processing
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p class='error'>$error</p>";
        }
    } else {
        try {
            // Insert the new product into the database using PDO
            $insertSql = "INSERT INTO Product (Name, CategoryID, WarehouseID, Description, Price, Quantity, LastUpdatedDate) 
                          VALUES (:productName, :categoryID, :warehouseID, :description, :price, :quantity, NOW())";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bindParam(':productName', $productName);
            $insertStmt->bindParam(':categoryID', $categoryID);
            $insertStmt->bindParam(':warehouseID', $warehouseID);
            $insertStmt->bindParam(':description', $description);
            $insertStmt->bindParam(':price', $price);
            $insertStmt->bindParam(':quantity', $quantity);
            $insertStmt->execute();

            // Check if the insertion was successful
            if ($insertStmt->rowCount() > 0) {
                echo "Product created successfully.";
                header("Location: inventory.php");
                exit();
            } else {
                echo "Error creating product.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

?>

<div class="main-content">
    <?php
    $pathtitle = "Inventory/Create";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="form-container">
            <form action="" method="post">
                <div class="form-group">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($errors)) {
                        echo '<div class="error-container">';
                        echo '<p class="error">Please fix the following errors:</p>';
                        echo '<ul>';
                        foreach ($errors as $error) {
                            echo '<li>' . htmlspecialchars($error) . '</li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                    } else {
                        echo '<div class="error-container" style="display:none;"></div>';
                    }
                    ?>
                </div>
                <div class="form-group">
                    <label for="productName">Product name:</label>
                    <input type="text" id="productName" name="productName" placeholder="Please enter a product name"
                        required>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="" disabled selected>Please select a category</option>
                        <?php
                        $categorySql = "SELECT CategoryID, Name FROM Category";
                        $categoryStatement = $conn->prepare($categorySql);
                        $categoryStatement->execute();

                        $categoryResult = $categoryStatement->fetchAll(PDO::FETCH_ASSOC);

                        if ($categoryResult) {
                            foreach ($categoryResult as $category) {
                                ?>
                                <option value="<?= $category["CategoryID"] ?>">
                                    <?= $category["Name"] ?>
                                </option>
                                <?php
                            }
                        } else {
                            echo "0 results";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productWarehouse">Warehouse:</label>
                    <select id="productWarehouse" name="productWarehouse" required>
                        <option value="" disabled selected>Please select a warehouse</option>
                        <?php
                        $warehouseSql = "SELECT WarehouseID, Name FROM Warehouse";
                        $warehouseStatement = $conn->prepare($warehouseSql);
                        $warehouseStatement->execute();

                        $warehouseResult = $warehouseStatement->fetchAll(PDO::FETCH_ASSOC);

                        if ($warehouseResult) {
                            foreach ($warehouseResult as $warehouse) {
                                ?>
                                <option value="<?= $warehouse["WarehouseID"] ?>">
                                    <?= $warehouse["Name"] ?>
                                </option>
                                <?php
                            }
                        } else {
                            echo "0 results";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productDescription">Description:</label>
                    <textarea id="productDescription" name="productDescription"
                        placeholder="Please enter the description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="productPrice">Price (RM):</label>
                    <input type="text" id="productPrice" name="productPrice" placeholder="Please enter a price"
                        oninput="validateNumberInput(this)" required>
                </div>
                <div class="form-group">
                    <label for="productQuantity">Quantity:</label>
                    <input type="number" id="productQuantity" name="productQuantity"
                        placeholder="Please enter a quantity" oninput="validateNumberInput(this)" required>
                </div>
                <div class="form-group">
                    <button type="submit">Create</button>
                    <button type="button" class="cancel" onclick="window.location.href='inventory.php'">Cancel</button>
                </div>
            </form>
        </div>
    </main>
</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>

</html>