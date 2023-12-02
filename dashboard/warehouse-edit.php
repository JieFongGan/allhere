<?php
ob_start(); // Start output buffering
$pageTitle = "Warehouse/Edit";
include("../database/database-connect.php");
include '../contain/header.php';

if (isset($_GET['warehouseID'])) {
    $warehouseID = $_GET['warehouseID'];

    // Fetch warehouse information based on the warehouse ID
    $sql = "SELECT * FROM Warehouse WHERE WarehouseID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $warehouseID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $warehouseData = $result->fetch_assoc();
    } else {
        // Handle the case where no warehouse is found with the given ID
        echo "Warehouse not found.";
        exit();
    }

    $stmt->close();
} else {
    // Handle the case where warehouse ID is not set
    echo "Warehouse ID not specified.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the warehouse ID is set in the form
    if (isset($_POST['warehouseID'])) {
        $warehouseID = $_POST['warehouseID'];

        // Retrieve other form data
        $warehouseName = $_POST['warehouseName'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];

        // Update the warehouse in the database
        $updateSql = "UPDATE Warehouse SET 
                      Name = ?, 
                      Address = ?, 
                      Contact = ?, 
                      Email = ? 
                      WHERE WarehouseID = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ssssi", $warehouseName, $address, $contact, $email, $warehouseID);
        $updateStmt->execute();

        // Check if the update was successful
        if ($updateStmt->affected_rows > 0) {
            echo "Warehouse updated successfully.";
            header("Location: warehouse.php");
            exit();
        } else {
            echo "Error updating warehouse: " . $updateStmt->error;
        }

        $updateStmt->close();
    }
}

?>

<div class="main-content">
    <?php
    $pathtitle = "Warehouse/Edit";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="form-container">
            <form action="" method="post">
                <div class="form-group">
                    <label for="warehouseID">Warehouse ID:</label>
                    <input type="text" id="warehouseID" name="warehouseID" value="<?= $warehouseData['WarehouseID'] ?>"
                        readonly>
                </div>
                <div class="form-group">
                    <label for="warehouseName">Warehouse Name:</label>
                    <input type="text" id="warehouseName" name="warehouseName" value="<?= $warehouseData['Name'] ?>"
                        placeholder="Warehouse name" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" value="<?= $warehouseData['Address'] ?>"
                        placeholder="Address" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" value="<?= $warehouseData['Contact'] ?>"
                        placeholder="Contact information" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= $warehouseData['Email'] ?>"
                        placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="hidden" name="warehouseID" value="<?= $warehouseData['WarehouseID'] ?>">
                    <button type="submit">Update</button>
                    <button type="button" class="cancel" onclick="window.location.href='warehouse.php'">Cancel</button>
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


