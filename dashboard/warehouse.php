<?php
ob_start(); // Start output buffering
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = "Warehouses";
include '../database/database-connect.php';
include '../contain/header.php';

// Prepare the SQL statement for warehouses
$sql = "SELECT * FROM Warehouse";
$stmt = $conn->prepare($sql);

// Execute the statement
$stmt->execute();

// Fetch the results
$warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination
$itemsPerPage = isset($_GET['itemsPerPage']) ? (int) $_GET['itemsPerPage'] : 10;
$totalItems = count($warehouses);
$totalPages = ceil($totalItems / $itemsPerPage);

// Get the current page from the URL, default to 1 if not set
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$current_page = max(1, min($totalPages, $current_page));

// Calculate the offset
$offset = ($current_page - 1) * $itemsPerPage;

$subsetWarehouses = array_slice($warehouses, $offset, $itemsPerPage);

if (isset($_POST['Wnew'])) {
    header("Location: warehouse-new.php");
    exit();
}

if (isset($_POST['deleteWarehouse'])) {
    // Using prepared statement to prevent SQL injection
    $warehouseIDToDelete = $_POST['deleteWarehouse'];
    $deleteSql = "DELETE FROM Warehouse WHERE WarehouseID = ?";
    $stmtDeleteWarehouse = $conn->prepare($deleteSql);
    $stmtDeleteWarehouse->bindParam(1, $warehouseIDToDelete, PDO::PARAM_INT);
    $stmtDeleteWarehouse->execute();

    if ($stmtDeleteWarehouse->rowCount() > 0) {
        header("Location: warehouse.php");
        exit();
    } else {
        echo "Error: " . $stmtDeleteWarehouse->errorInfo()[2];
    }

    $stmtDeleteWarehouse->closeCursor();
}

?>

<div class="main-content">
    <?php
    $pathtitle = "Warehouses";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="button-and-search">
            <form method="POST">
                <button name="Wnew">Create New</button>
            </form>
            <input type="text" id="searchInput" placeholder="Search on the current list..." onkeyup="searchTable()">
        </div>

        <div class="table-responsive">
            <table id="myTable" class="table-container" style="width:100%">
                <thead>
                    <tr>
                        <th>WarehouseID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($subsetWarehouses)): ?>
                        <tr>
                            <td colspan="5">No data available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subsetWarehouses as $warehouse): ?>
                            <tr>
                                <td>
                                    <?= $warehouse['WarehouseID'] ?>
                                </td>
                                <td>
                                    <?= $warehouse['Name'] ?>
                                </td>
                                <td>
                                    <?= $warehouse['Location'] ?>
                                </td>
                                <td>
                                    <?= $warehouse['Capacity'] ?>
                                </td>
                                <td>
                                    <form method="GET" action="warehouse-edit.php">
                                        <input type="hidden" name="warehouseID" value="<?= $warehouse['WarehouseID'] ?>">
                                        <button class="edit" type="submit">edit</button>
                                    </form>
                                    <form method="POST">
                                        <button class="delete" name="deleteWarehouse" type="submit"
                                            onclick="return confirm('Are you sure you want to delete this warehouse?')">delete
                                        </button>
                                        <input type="hidden" name="deleteWarehouse" value="<?= $warehouse['WarehouseID'] ?>">
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