<?php
session_start(); // Start the session
ob_start(); // Start output buffering
$pageTitle = "Transaction/New";
require_once("../database/database-connect.php");
include '../contain/header.php';

// Fetch warehouse data using PDO
$warehouseSql = "SELECT WarehouseID, Name FROM Warehouse";
$warehouseStmt = $conn->prepare($warehouseSql);
$warehouseStmt->execute();
$warehouseResult = $warehouseStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customer data using PDO
$customerSql = "SELECT CustomerID, Name, Remark FROM Customer";
$customerStmt = $conn->prepare($customerSql);
$customerStmt->execute();
$customerResult = $customerStmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize error messages
$errors = array();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate warehouse selection
    if (empty($_POST['productWarehouse'])) {
        $errors['productWarehouse'] = "Please select a warehouse";
    }

    // Validate transaction type selection
    if (empty($_POST['transactionType'])) {
        $errors['transactionType'] = "Please select a transaction type";
    }

    // Validate customer selection
    if (empty($_POST['selectedCustomer'])) {
        $errors['selectedCustomer'] = "Please select a customer";
    }

    // If there are no errors, proceed to the next page
    if (empty($errors)) {
        // Store data in session
        $_SESSION['selectedWarehouse'] = $_POST['productWarehouse'];
        $_SESSION['selectedTransactionType'] = $_POST['transactionType'];
        $_SESSION['selectedCustomer'] = $_POST['selectedCustomer'];

        // Redirect to the second page
        header("Location: transaction-newProduct.php");
        exit();
    }
}
?>

<div class="main-content">
    <?php
    $pathtitle = "Transaction/New";
    include '../contain/horizontal-bar.php';
    ?>
    <main>
        <div class="form-container">
            <form action="" method="post">
                <div class="form-group">
                    <label for="productWarehouse">To/From Warehouse:</label>
                    <select id="productWarehouse" name="productWarehouse">
                        <option value="" disabled selected>Please select a warehouse</option>
                        <?php
                        foreach ($warehouseResult as $warehouse) {
                            echo '<option value="' . $warehouse["WarehouseID"] . '">' . $warehouse["Name"] . '</option>';
                        }
                        ?>
                    </select>
                    <?php
                    if (isset($errors['productWarehouse'])) {
                        echo '<p class="error">' . $errors['productWarehouse'] . '</p>';
                    }
                    ?>
                </div>
                <div class="form-group">
                    <label for="transactionType">Transaction Type:</label>
                    <select id="transactionType" name="transactionType">
                        <option value="" disabled selected>Please select a transaction type</option>
                        <option value="Sales">Sales</option>
                        <option value="Purchase">Purchase</option>
                    </select>
                    <?php
                    if (isset($errors['transactionType'])) {
                        echo '<p class="error">' . $errors['transactionType'] . '</p>';
                    }
                    ?>
                </div>

                <div class="form-group">
                    <div class="table-responsive" id="customerSection">
                        <div class="form-group">
                            <h3>Customers</h3>
                            <table id="customerTable" class="table-container" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Remark</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="customerTableBody">
                                    <?php
                                    foreach ($customerResult as $customer) {
                                        echo '<tr>
                                            <td>' . $customer["CustomerID"] . '</td>
                                            <td>' . $customer["Name"] . '</td>
                                            <td>' . $customer["Remark"] . '</td>
                                            <td><input type="radio" name="selectedCustomer" value="' . $customer["CustomerID"] . '"></td>
                                          </tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                            if (isset($errors['selectedCustomer'])) {
                                echo '<p class="error">' . $errors['selectedCustomer'] . '</p>';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit">Next</button>
                        <button type="button" class="cancel"
                            onclick="window.location.href='transaction.php'">Cancel</button>
                    </div>
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