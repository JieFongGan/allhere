<?php
$pageTitle = "Customer/Edit";
include '../database/database-connect.php';
include '../contain/header.php';

if (isset($_GET['customerID'])) {
    $customerID = $_GET['customerID'];

    // Fetch customer information based on the customer ID
    $sql = "SELECT * FROM Customer WHERE CustomerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$customerID]);
    $customerData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customerData) {
        // Handle the case where no customer is found with the given ID
        echo "Customer not found.";
        exit();
    }
} else {
    // Handle the case where customer ID is not set
    echo "Customer ID not specified.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the customer ID is set in the form
    if (isset($_POST['customerID'])) {
        $customerID = $_POST['customerID'];

        // Retrieve other form data
        $customerName = $_POST['customerName'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $remark = $_POST['remark'];

        try {
            // Update the customer in the database
            $updateSql = "UPDATE Customer SET 
                      Name = ?, 
                      Contact = ?, 
                      Email = ?, 
                      Address = ?, 
                      Remark = ? 
                      WHERE CustomerID = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->execute([$customerName, $contact, $email, $address, $remark, $customerID]);

            // Check if the update was successful
            if ($updateStmt->rowCount() > 0) {
                echo "Customer updated successfully.";
                header("Location: customer.php");
                exit();
            } else {
                echo "Error updating customer.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<div class="main-content">
    <?php
    $pathtitle = "Customer/Edit";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="form-container">
            <form action="" method="post">
                <div class="form-group">
                    <label for="customerID">Customer ID:</label>
                    <input type="text" id="customerID" name="customerID" value="<?= $customerData['CustomerID'] ?>"
                        readonly>
                </div>
                <div class="form-group">
                    <label for="customerName">Customer Name:</label>
                    <input type="text" id="customerName" name="customerName" value="<?= $customerData['Name'] ?>"
                        placeholder="Customer name" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" value="<?= $customerData['Contact'] ?>"
                        placeholder="Contact information" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= $customerData['Email'] ?>"
                        placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?= $customerData['Address'] ?>"
                        placeholder="Address" required>
                </div>
                <div class="form-group">
                    <label for="remark">Remark:</label>
                    <input type="text" id="remark" name="remark" value="<?= $customerData['Remark'] ?>"
                        placeholder="Remark" required>
                </div>
                <div class="form-group">
                    <input type="hidden" name="customerID" value="<?= $customerData['CustomerID'] ?>">
                    <button type="submit">Update</button>
                    <button type="button" class="cancel" onclick="window.location.href='customer.php'">Cancel</button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>

</html>
