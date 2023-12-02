<?php
ob_start(); // Start output buffering
$pageTitle = "Weekly Report";
include("../database/database-connect.php");
include '../contain/header.php';

try {
    // SQL query for Weekly Sales Report
    $sql = "
    SELECT
    YEAR(TransactionDate) AS Year,
    DATEPART(WEEK, TransactionDate) AS Week,
    COUNT(DISTINCT t.TransactionID) AS TotalTransactions,
    COUNT(td.TransactionDetailID) AS TotalTransactionDetails,
    SUM(td.Quantity) AS TotalItemsSold
    FROM
        [Transaction] t
    JOIN
        TransactionDetail td ON t.TransactionID = td.TransactionID
    WHERE
        t.TransactionType = 'Sales'
    GROUP BY
        YEAR(TransactionDate), DATEPART(WEEK, TransactionDate)
    ORDER BY
        Year, DATEPART(WEEK, TransactionDate);
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch the result as an associative array
    $weeklyReport = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    $pathtitle = "Weekly Report";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <!-- Display Weekly Report Data -->
        <h2>Weekly Report</h2>

        <?php if (!empty($weeklyReport)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Week</th>
                        <th>Total Transactions</th>
                        <th>Total Transaction Details</th>
                        <th>Total Items Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weeklyReport as $row): ?>
                        <tr>
                            <td>
                                <?= $row['Year'] ?>
                            </td>
                            <td>
                                <?= $row['Week'] ?>
                            </td>
                            <td>
                                <?= $row['TotalTransactions'] ?>
                            </td>
                            <td>
                                <?= $row['TotalTransactionDetails'] ?>
                            </td>
                            <td>
                                <?= $row['TotalItemsSold'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No data available for the weekly report.</p>
        <?php endif; ?>
    </main>
</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>

</html>