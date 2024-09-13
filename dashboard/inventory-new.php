<?php
ob_start(); // Start output buffering
$pageTitle = "Inventory/Create";
include '../database/database-connect.php';
include '../contain/header.php';

// Set default values for a new product
$productData = array(
    'ProductID' => '',
    'Name' => '',
    'CategoryID' => '',
    'WarehouseID' => '',
    'Description' => '',
    'Price' => '',
    'Quantity' => ''
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $productName = $_POST['productName'];
    $categoryID = $_POST['category'];
    $warehouseID = !empty($_POST['productWarehouse']) ? $_POST['productWarehouse'] : null;
    $description = $_POST['productDescription'];
    $price = $_POST['productPrice'];
    $quantity = $_POST['productQuantity'];

    // Insert new product into the database
    $insertSql = "INSERT INTO Product (Name, CategoryID, WarehouseID, Description, Price, Quantity, LastUpdatedDate)
                  VALUES (?, ?, ?, ?, ?, ?, GETDATE())";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->execute([$productName, $categoryID, $warehouseID, $description, $price, $quantity]);

    // Check if the insert was successful
    if ($insertStmt->rowCount() > 0) {
        echo "Product created successfully.";
        header("Location: inventory.php");
        exit();
    } else {
        echo "Error creating product.";
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
                    <label for="productName">Product Name:</label>
                    <input type="text" id="productName" name="productName" value="<?= $productData['Name'] ?>"
                        placeholder="Product name" required>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="" disabled>Please select a category</option>
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
                        <option value="" disabled>Please select a warehouse</option>
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

                        $conn = null; // Close the PDO connection
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productDescription">Description:</label>
                    <textarea id="productDescription" name="productDescription" placeholder="Description"
                        required></textarea>
                </div>
                <div class="form-group">
                    <label for="productPrice">Price (RM):</label>
                    <input type="number" step="0.01" min="0" id="productPrice" name="productPrice" placeholder="Price"
                        oninput="validateNumberInput(this)" required>
                </div>
                <div class="form-group">
                    <label for="productQuantity">Quantity:</label>
                    <input type="number" id="productQuantity" name="productQuantity" placeholder="Quantity"
                        oninput="validateNumberInput(this)" required>
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
