<?php
ob_start(); // Start output buffering
$pageTitle = "Inventory/Edit";
include '../database/database-connect.php';
include '../contain/header.php';

// Check if the product ID is set in the URL
if (isset($_GET['productID'])) {
    $productID = $_GET['productID'];

    // Fetch product information based on the product ID
    $sql = "SELECT * FROM Product WHERE ProductID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$productID]);
    $productData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$productData) {
        // Handle the case where no product is found with the given ID
        echo "Product not found.";
        exit();
    }
} else {
    // Handle the case where product ID is not set
    echo "Product ID not specified.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the product ID is set in the form
    if (isset($_POST['productID'])) {
        $productID = $_POST['productID'];

        // Retrieve other form data
        $productName = $_POST['productName'];
        $categoryID = $_POST['category'];
        $warehouseID = !empty($_POST['productWarehouse']) ? $_POST['productWarehouse'] : null;
        $description = $_POST['productDescription'];
        $price = $_POST['productPrice'];
        $quantity = $_POST['productQuantity'];

        $updateSql = "UPDATE Product SET 
              Name = ?, 
              CategoryID = ?, 
              WarehouseID = ?, 
              Description = ?, 
              Price = ?, 
              Quantity = ?, 
              LastUpdatedDate = GETDATE() 
              WHERE ProductID = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->execute([$productName, $categoryID, $warehouseID, $description, $price, $quantity, $productID]);

        // Check if the update was successful
        if ($updateStmt->rowCount() > 0) {
            echo "Product updated successfully.";
            header("Location: inventory.php");
            exit();
        } else {
            echo "Error updating product.";
        }
    }
}

?>

<div class="main-content">
    <?php
    $pathtitle = "Inventory/Edit";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="form-container">
            <form action="" method="post">
                <div class="form-group">
                    <label for="productId">Product ID:</label>
                    <input type="text" id="productID" name="productID" value="<?= $productData['ProductID'] ?>"
                        readonly>
                </div>
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
                                $selected = ($category['CategoryID'] == $productData['CategoryID']) ? 'selected' : '';
                                ?>
                                <option value="<?= $category["CategoryID"] ?>" <?= $selected ?>>
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
                                $selected = ($warehouse['WarehouseID'] == $productData['WarehouseID']) ? 'selected' : '';
                                ?>
                                <option value="<?= $warehouse["WarehouseID"] ?>" <?= $selected ?>>
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
                        required><?= $productData['Description'] ?></textarea>
                </div>
                <div class="form-group">
                    <label for="productPrice">Price (RM):</label>
                    <input type="number" step="0.01" min="0" id="productPrice" name="productPrice" value="<?= $productData['Price'] ?>"
                        placeholder="Price" oninput="validateNumberInput(this)" required>
                </div>
                <div class="form-group">
                    <label for="productQuantity">Quantity:</label>
                    <input type="number" id="productQuantity" name="productQuantity"
                        value="<?= $productData['Quantity'] ?>" placeholder="Quantity"
                        oninput="validateNumberInput(this)" required>
                </div>
                <div class="form-group">
                    <input type="hidden" name="productID" value="<?= $productData['ProductID'] ?>">
                    <button type="submit">Update</button>
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