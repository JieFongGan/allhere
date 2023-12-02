<?php
session_start();
ob_start(); // Start output buffering

$pageTitle = "Transactions/New-product";
require_once("../database/database-connect.php");
include '../contain/header.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Initialize errors array
    $errors = array();

    // Retrieve form data
    $quantities = $_POST['quantities'] ?? [];
    $selectedProducts = $_POST['selectedProducts'] ?? [];

    // Validate if products are selected
    if (empty($selectedProducts)) {
        $errors['selectedProducts'] = "Please select at least one product";
    }

    // Validate quantities
    foreach ($quantities as $productId => $quantity) {
        if (!is_numeric($quantity) || $quantity < 0) {
            $errors['quantities'][$productId] = "Quantity must be a non-negative numeric value";
        }
    }

    // If there are no errors, proceed with database operations
    if (empty($errors)) {
        // Insert transaction information
        $insertTransactionSql = "INSERT INTO [Transaction] (WarehouseID, TransactionType, CustomerID, TransactionDate, DeliveryStatus) VALUES (?, ?, ?, NOW(), 'Pending')";
        $stmt = $conn->prepare($insertTransactionSql);
        $stmt->execute([$_SESSION['selectedWarehouse'], $_SESSION['selectedTransactionType'], $_SESSION['selectedCustomer']]);
        $stmt->close();

        // Get the ID of the last inserted transaction
        $lastTransactionId = $conn->lastInsertId();

        // Insert selected products and quantities into a transaction details table
        $insertDetailsSql = "INSERT INTO TransactionDetail (TransactionID, ProductID, Quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertDetailsSql);

        // Loop through all products to check if the checkbox is selected
        foreach ($selectedProducts as $key => $productId) {
            $quantity = $quantities[$productId] ?? 0; // Use the product ID as the index

            // Check if the checkbox for this product is checked
            if (isset($_POST['selectedProducts'][$productId])) {
                // Process the selected product
                $stmt->execute([$lastTransactionId, $productId, $quantity]);

                // Update the product quantity based on the transaction type
                $updateQuantitySql = "UPDATE Product SET Quantity = Quantity ";

                if ($_SESSION['selectedTransactionType'] === 'Sales') {
                    // If the transaction type is Sales, subtract the quantity
                    $updateQuantitySql .= "- ?";
                } else {
                    // If the transaction type is Purchase, add the quantity
                    $updateQuantitySql .= "+ ?";
                }

                $updateQuantitySql .= " WHERE ProductID = ?";

                $stmtUpdateQuantity = $conn->prepare($updateQuantitySql);
                $stmtUpdateQuantity->execute([$quantity, $productId]);
                $stmtUpdateQuantity->close();
            }
        }

        $stmt->close();

        // Redirect to the next page or display a success message
        header("Location: transaction.php");
        exit();
    }
}

// If session variables are not set, redirect to the first page
if (!isset($_SESSION['selectedWarehouse']) || !isset($_SESSION['selectedTransactionType']) || !isset($_SESSION['selectedCustomer'])) {
    header("Location: transaction-new.php");
    exit();
}

// Fetch selected warehouse
$selectedWarehouse = $_SESSION['selectedWarehouse'];

// Fetch product data based on the selected warehouse
$productSql = "SELECT ProductID, Name, Price FROM Product WHERE WarehouseID = ?";
$stmt = $conn->prepare($productSql);
$stmt->execute([$selectedWarehouse]);
$productResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->close();
?>

<div class="main-content">
    <?php
    $pathtitle = "Transaction/New-product";
    include '../contain/horizontal-bar.php';
    ?>
    <main>
        <form action="" method="post">
            <div class="table-responsive" id="productSection">
                <div class="form-group">
                    <?php
                    if (!empty($errors['selectedProducts'])) {
                        echo '<p class="error">' . $errors['selectedProducts'] . '</p>';
                    }
                    ?>
                    <h3>Products</h3>
                    <table id="productTable" class="table-container" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <?php
                            if ($productResult && count($productResult) > 0) {
                                foreach ($productResult as $product) {
                                    echo '<tr>
                            <td>' . $product["ProductID"] . '</td>
                            <td>' . $product["Name"] . '</td>
                            <td>' . $product["Price"] . '</td>
                            <td><input type="number" name="quantities[' . $product["ProductID"] . ']" value="0" min="0"></td>
                            <td><input type="checkbox" name="selectedProducts[' . $product["ProductID"] . ']" value="' . $product["ProductID"] . '"></td>
                          </tr>';
                                }
                            } else {
                                echo "<tr><td colspan='4'>No products found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <button type="submit">Add</button>
                    <button type="button" class="cancel"
                        onclick="window.location.href='transaction.php'">Cancel</button>
                </div>
            </div>
        </form>
    </main>
</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>

</html>