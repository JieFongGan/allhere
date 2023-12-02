<?php
ob_start(); // Start output buffering
$pageTitle = "Monthly Report";
include("../database/database-connect.php");
include '../contain/header.php';

try {
    // SQL query for Monthly Sales Report
    $sql = "
    SELECT
        CONCAT(YEAR(TransactionDate), '-', FORMAT(MONTH(TransactionDate), '00')) AS Month,
        COUNT(DISTINCT t.TransactionID) AS TotalTransactions,
        COUNT(td.TransactionDetailID) AS TotalTransactionDetails,
        SUM(td.Quantity) AS TotalItemsSold
    FROM
        [Transaction] t
    JOIN
        TransactionDetail td ON t.TransactionID = td.TransactionID
    WHERE
        t.TransactionType = 'Sales'   -- Filter for sales transactions
    GROUP BY
        CONCAT(YEAR(TransactionDate), '-', FORMAT(MONTH(TransactionDate), '00'))
    ORDER BY
        Month;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch the result as an associative array
    $monthlyReport = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
    exit();
} finally {
    // Close the database connection
    $conn = null;
}
?>

<div class="main-content">
    <?php
    $pathtitle = "Monthly Report";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <!-- Display Monthly Report Data -->
        <h2>Monthly Report</h2>

        <?php if (!empty($monthlyReport)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Transactions</th>
                        <th>Total Transaction Details</th>
                        <th>Total Items Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthlyReport as $row): ?>
                        <tr>
                            <td><?= $row['Month'] ?></td>
                            <td><?= $row['TotalTransactions'] ?></td>
                            <td><?= $row['TotalTransactionDetails'] ?></td>
                            <td><?= $row['TotalItemsSold'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No data available for the monthly report.</p>
        <?php endif; ?>
    </main>
</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>
</html>
