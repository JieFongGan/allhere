<?php
ob_start(); // Start output buffering
$pageTitle = "Transactions";
include '../database/database-connect.php';
include '../contain/header.php';

// Prepare the SQL statement
$sql = "SELECT * FROM [Transaction]";
$stmt = $conn->prepare($sql);

// Execute the statement
$stmt->execute();

// Fetch the results
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination
$itemsPerPage = isset($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 10;
$totalItems = count($transactions);
$totalPages = ceil($totalItems / $itemsPerPage);

// Get the current page from the URL, default to 1 if not set
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$current_page = max(1, min($totalPages, $current_page));

// Calculate the offset
$offset = ($current_page - 1) * $itemsPerPage;

// Get a subset of transactions based on the offset and items per page
$subsetTransactions = array_slice($transactions, $offset, $itemsPerPage);

if (isset($_POST['Cnew'])) {
    header("Location: inventory-new.php");
    exit;
}

?>

<div class="main-content">
    <?php
    $pathtitle = "Transactions";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="button-and-search">
            <form method="POST">
                <button name="Cnew">Create New</button>
            </form>
            <input type="text" id="searchInput" placeholder="Search on current list..." onkeyup="searchTable()">
        </div>

        <div class="table-responsive">
            <table id="myTable" class="table-container" style="width:100%">
                <thead>
                    <tr>
                        <th>TransactionID</th>
                        <th>WarehouseID</th>
                        <th>CustomerID</th>
                        <th>TransactionType</th>
                        <th>TransactionDate</th>
                        <th>DeliveryStatus</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($subsetTransactions)): ?>
                        <tr>
                            <td colspan="7">No data available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subsetTransactions as $transaction): ?>
                            <tr>
                                <td><?= $transaction['TransactionID'] ?></td>
                                <td><?= $transaction['WarehouseID'] ?></td>
                                <td><?= $transaction['CustomerID'] ?></td>
                                <td><?= $transaction['TransactionType'] ?></td>
                                <td><?= $transaction['TransactionDate'] ?></td>
                                <td><?= $transaction['DeliveryStatus'] ?></td>
                                <td>
                                    <form method="GET" action="transaction-detailProduct.php">
                                        <input type="hidden" name="transactionID" value="<?= $transaction['TransactionID'] ?>">
                                        <button class="detail" type="submit">Detail</button>
                                    </form>
                                    <form method="GET" action="transaction-edit.php">
                                        <input type="hidden" name="transactionID" value="<?= $transaction['TransactionID'] ?>">
                                        <button class="edit" type="submit">Edit</button>
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