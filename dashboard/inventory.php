<?php
ob_start(); // Start output buffering
$pageTitle = "Inventory";
include '../database/database-connect.php';
include '../contain/header.php';

// Prepare the SQL statement for inventory
$sql = "SELECT * FROM Product";
$stmt = $conn->prepare($sql);

// Execute the statement
$stmt->execute();

// Fetch the results
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination
$itemsPerPage = isset($_GET['itemsPerPage']) ? (int) $_GET['itemsPerPage'] : 10;
$totalItems = count($products);
$totalPages = ceil($totalItems / $itemsPerPage);

// Get the current page from the URL, default to 1 if not set
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$current_page = max(1, min($totalPages, $current_page));

// Calculate the offset
$offset = ($current_page - 1) * $itemsPerPage;

$subsetProducts = array_slice($products, $offset, $itemsPerPage);

if (isset($_POST['Pnew'])) {
    header("Location: inventory-new.php");
    exit();
}

if (isset($_POST['deleteProduct'])) {
    // Using prepared statement to prevent SQL injection
    $productIDToDelete = $_POST['deleteProduct'];
    $deleteSql = "DELETE FROM Product WHERE ProductID = ?";
    $stmtDeleteProduct = $conn->prepare($deleteSql);
    $stmtDeleteProduct->bindParam(1, $productIDToDelete, PDO::PARAM_INT);
    $stmtDeleteProduct->execute();

    if ($stmtDeleteProduct->rowCount() > 0) {
        header("Location: inventory.php");
        exit();
    } else {
        echo "Error: " . $stmtDeleteProduct->errorInfo()[2];
    }

    $stmtDeleteProduct->closeCursor();
}

?>

<div class="main-content">
    <?php
    $pathtitle = "Inventory";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="button-and-search">
            <form method="POST">
                <button name="Pnew">Create New</button>
            </form>
            <input type="text" id="searchInput" placeholder="Search on the current list..." onkeyup="searchTable()">
        </div>

        <div class="table-responsive">
            <table id="myTable" class="table-container" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>ProductID</th>
                        <th>WarehouseID</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($subsetProducts)): ?>
                        <tr>
                            <td colspan="7">No data available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subsetProducts as $product): ?>
                            <tr>
                                <td>
                                    <?= $product['Name'] ?>
                                </td>
                                <td>
                                    <?= $product['ProductID'] ?>
                                </td>
                                <td>
                                    <?= $product['WarehouseID'] ?>
                                </td>
                                <td>
                                    <?= $product['Description'] ?>
                                </td>
                                <td>
                                    <?= $product['Price'] ?>
                                </td>
                                <td>
                                    <?= $product['Quantity'] ?>
                                </td>
                                <td>
                                    <form method="GET" action="inventory-edit.php">
                                        <input type="hidden" name="productID" value="<?= $product['ProductID'] ?>">
                                        <button class="edit" type="submit">edit</button>
                                    </form>
                                    <form method="POST">
                                        <button class="delete" name="deleteProduct" type="submit"
                                            onclick="return confirm('Are you sure you want to delete this product?')">delete
                                        </button>
                                        <input type="hidden" name="deleteProduct" value="<?= $product['ProductID'] ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <form method="GET" class="pagination-form">
            <label for="itemsPerPage">Items per page:</label>
            <select id="itemsPerPage" name="itemsPerPage" onchange="this.form.submit()">
                <option value="10" <?= $itemsPerPage == 10 ? 'selected' : '' ?>>10</option>
                <option value="20" <?= $itemsPerPage == 20 ? 'selected' : '' ?>>20</option>
                <option value="50" <?= $itemsPerPage == 50 ? 'selected' : '' ?>>50</option>
                <option value="100" <?= $itemsPerPage == 100 ? 'selected' : '' ?>>100</option>
            </select>
        </form>

        <div id="pagination" class="pagination">
            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <a href="?page=<?= $page ?>" <?= $page == $current_page ? 'class="active"' : '' ?>>
                    <?= $page ?>
                </a>
            <?php endfor; ?>
        </div>
    </main>
</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>

</html>