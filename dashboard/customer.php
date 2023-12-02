<?php
ob_start(); // Start output buffering
$pageTitle = "Customers";
include '../database/database-connect.php';
include '../contain/header.php';

// Prepare the SQL statement for customers
$sql = "SELECT * FROM Customer";
$stmt = $conn->prepare($sql);

// Execute the statement
$stmt->execute();

// Fetch the results
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination
$itemsPerPage = isset($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 10;
$totalItems = count($customers);
$totalPages = ceil($totalItems / $itemsPerPage);

// Get the current page from the URL, default to 1 if not set
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$current_page = max(1, min($totalPages, $current_page));

// Calculate the offset
$offset = ($current_page - 1) * $itemsPerPage;

$subsetCustomers = array_slice($customers, $offset, $itemsPerPage);

if (isset($_POST['Cnew'])) {
    header("Location: customer-new.php");
    exit();
}

if (isset($_POST['deleteCustomer'])) {
    // Using prepared statement to prevent SQL injection
    $customerIDToDelete = $_POST['deleteCustomer'];
    $deleteSql = "DELETE FROM Customer WHERE CustomerID = ?";
    $stmtDeleteCustomer = $conn->prepare($deleteSql);
    $stmtDeleteCustomer->bindParam(1, $customerIDToDelete, PDO::PARAM_INT);
    $stmtDeleteCustomer->execute();

    if ($stmtDeleteCustomer->rowCount() > 0) {
        header("Location: customer.php");
        exit();
    } else {
        echo "Error: " . $stmtDeleteCustomer->errorInfo()[2];
    }

    $stmtDeleteCustomer->closeCursor();
}
?>

<div class="main-content">
    <?php
    $pathtitle = "Customers";
    include '../contain/horizontal-bar.php';
    ?>

    <?php if ($userrole == 'User'): ?>
        <br><br><br>
        <div class="button-and-search">
        <h3>Sorry, user cannot access this page.</h3>
        </div>
    <?php endif; ?>

    <?php if ($userrole !== 'User'): ?>

    <main>
        <div class="button-and-search">
            <form method="POST">
                <button name="Cnew">Create New</button>
            </form>
            <input type="text" id="searchInput" placeholder="Search on the current list..." onkeyup="searchTable()">
        </div>

        <div class="table-responsive">
            <table id="myTable" class="table-container" style="width:100%">
                <thead>
                    <tr>
                        <th>CustomerID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Remark</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($subsetCustomers)): ?>
                        <tr>
                            <td colspan="7">No data available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subsetCustomers as $customer): ?>
                            <tr>
                                <td>
                                    <?= $customer['CustomerID'] ?>
                                </td>
                                <td>
                                    <?= $customer['Name'] ?>
                                </td>
                                <td>
                                    <?= $customer['Contact'] ?>
                                </td>
                                <td>
                                    <?= $customer['Email'] ?>
                                </td>
                                <td>
                                    <?= $customer['Address'] ?>
                                </td>
                                <td>
                                    <?= $customer['Remark'] ?>
                                </td>
                                <td>
                                    <form method="GET" action="customer-edit.php">
                                        <input type="hidden" name="customerID" value="<?= $customer['CustomerID'] ?>">
                                        <button class="edit" type="submit">edit</button>
                                    </form>
                                    <?php if ($userrole !== 'Manager'): ?>

                                    <form method="POST">
                                        <button class="delete" name="deleteCustomer" type="submit"
                                            onclick="return confirm('Are you sure you want to delete this customer?')">delete
                                        </button>
                                        <input type="hidden" name="deleteCustomer" value="<?= $customer['CustomerID'] ?>">
                                    </form>
                                    <?php endif; ?>
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
    <?php endif; ?>

</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>

</html>