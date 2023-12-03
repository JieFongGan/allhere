<!DOCTYPE html>
<html>

<head>
    <title>Company List</title>
    <style>
        /* CSS beautification */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f1f1f1;
        }

        .container {
            width: 800px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
        }

        .sidebar {
            width: 200px;
            padding: 20px;
            background-color: #f2f2f2;
            transition: width 0.3s ease-in-out;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        /* Side menu */
        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .sidebar li {
            margin-bottom: 10px;
        }

        .sidebar a {
            text-decoration: none;
            color: #333;
        }

        .sidebar a:hover {
            color: #666;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Make table scrollable after 10 entries */
        .table-container {
            max-height: 300px;
            /* Set the maximum height as per your requirement */
            overflow-y: auto;
        }

        /* Button styling */
        button {
            padding: 10px 20px;
            background-color: blue;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: blue;
        }

        /* Animation */
        .sidebar:hover {
            width: 250px;
        }

        /* Additional CSS */
        .container {
            animation: slide-in 0.5s ease-in-out;
        }

        .pagination {
            display: flex;
            list-style: none;
            margin: 20px 0;
            padding: 0;
        }

        .pagination a {
            display: block;
            padding: 10px 15px;
            margin-right: 5px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            background-color: #f2f2f2;
            border-radius: 4px;
            transition: background-color 0.3s ease-in-out;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        @keyframes slide-in {
            0% {
                opacity: 0;
                transform: translateX(-100px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .content {
            animation: fade-in 0.5s ease-in-out;
        }

        @keyframes fade-in {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .search-container {
            text-align: left;
        }

        #searchInput {
            width: 300px;
            padding: 8px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <ul>
            <li><a href="admincomplist.php">Company List</a></li>
            <li><a href="adminuserlist.php">User List</a></li>
            <li><a href="adlogin.php">Log Out</a></li>
        </ul>
    </div>
    <div class="container">
        <div class="content">
            <button><a href="adauthcode.php" style="color: inherit; text-decoration: none;">Create New
                    AuthCode</a></button>
            <br>
            <br>
            <div class="search-container">
                <form action="admincomplist.php" method="GET">
                    <input type="text" id="searchInput" name="search"
                        placeholder="Search by Company Name, Status or Auth Code">
                    <button type="submit">Search</button>
                </form>
            </div>

            <br>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Status</th>
                            <th>Auth Code</th>
                            <th>Modify</th>
                        </tr>
                    </thead>

                    <?php
                    $conn = new PDO(
                        "sqlsrv:server = tcp:allhereserver.database.windows.net,1433; Database = allheredb",
                        "sqladmin",
                        "#Allhere",
                        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                    );
                
                    // Fetch data from the database with search conditions
                    $searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
                    $query = "SELECT CompanyName, Status, AuthCode FROM company WHERE 
                                CompanyName LIKE '%$searchKeyword%' OR
                                Status LIKE '%$searchKeyword%' OR
                                AuthCode LIKE '%$searchKeyword%'";
                    $result = $conn->query($query);
                
                    // Display the fetched data
                    $number = 1;
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $number . "</td>";
                        echo "<td>" . $row['CompanyName'] . "</td>";
                        echo "<td>" . $row['Status'] . "</td>";
                        echo "<td>" . $row['AuthCode'] . "</td>";
                        echo "<td>";
                        echo "<button style='display: inline-block;'><a href='adcompedit.php?authcode=" . $row['AuthCode'] . "' style='color: inherit; text-decoration: none;'>Edit</a></button>";
                        echo "&nbsp;";
                        echo "<form method='post' action='' style='display: inline-block;' onsubmit='return confirmDelete();'>";
                        echo "<input type='hidden' name='AuthCode' value='" . $row['AuthCode'] . "'>";
                        echo "<button type='submit' name='delete'>Delete</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                        $number++;
                    }
                
                    // Delete button functionality
                    if (isset($_POST['delete'])) {
                        $authCode = $_POST['AuthCode'];
                
                        // Select the CompanyName based on the provided AuthCode
                        $query = 'SELECT CompanyName FROM company WHERE AuthCode = :authCode';
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':authCode', $authCode);
                        $stmt->execute();
                
                        // Check if the query was successful
                        if ($stmt) {
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            $companyName = $row['CompanyName'];
                
                            if ($companyName === null) {
                                echo "Error retrieving CompanyName: " . $conn->errorInfo();
                            }
                
                            // Display confirmation dialog using JavaScript
                            $confirmationMessage = ($companyName !== null) ? "Are you sure you want to delete $companyName and its users?" : "Are you sure you want to delete AuthCode: $authCode?";
                            echo "<script>
                                    if (confirm('$confirmationMessage')) {
                                        window.location.href = 'admincomplist.php?deleteAuthCode=$authCode';
                                    }
                                </script>";
                        } else {
                            echo "Error retrieving CompanyName: " . $conn->errorInfo();
                        }
                    }
                
                    // Check if deleteAuthCode is set in the URL
                    if (isset($_GET['deleteAuthCode'])) {
                        $authCodeToDelete = $_GET['deleteAuthCode'];
                
                        // Retrieve CompanyName before deletion
                        $query = "SELECT CompanyName FROM company WHERE AuthCode = :authCodeToDelete";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':authCodeToDelete', $authCodeToDelete);
                        $stmt->execute();
                
                        if ($stmt) {
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            $companyName = $row['CompanyName'];
                
                            // Delete all users from the user table with the specified CompanyName
                            $deleteUsersQuery = "DELETE FROM user WHERE CompanyName = :companyName";
                            $deleteUsersStmt = $conn->prepare($deleteUsersQuery);
                            $deleteUsersStmt->bindParam(':companyName', $companyName);
                            $deleteUsersResult = $deleteUsersStmt->execute();
                
                            if (!$deleteUsersResult) {
                                echo "Error deleting users: " . $conn->errorInfo();
                            }
                        }
                
                        // Delete the record from the company table
                        $deleteQueryCompany = "DELETE FROM company WHERE AuthCode = :authCodeToDelete";
                        $deleteStmtCompany = $conn->prepare($deleteQueryCompany);
                        $deleteStmtCompany->bindParam(':authCodeToDelete', $authCodeToDelete);
                        $deleteResultCompany = $deleteStmtCompany->execute();
                
                        if ($deleteResultCompany) {
                            echo "Record deleted successfully.";
                
                            // Check if $companyName is not NULL and delete the corresponding database
                            if ($companyName !== null) {
                                $deleteDatabaseQuery = "DROP DATABASE $companyName";
                                $deleteDatabaseStmt = $conn->prepare($deleteDatabaseQuery);
                                $deleteDatabaseResult = $deleteDatabaseStmt->execute();
                
                                if ($deleteDatabaseResult) {
                                    echo "Database $companyName deleted successfully.";
                                } else {
                                    echo "Error deleting database $companyName: " . $conn->errorInfo();
                                }
                            }
                
                            echo "<script>window.location.href='admincomplist.php';</script>";
                        } else {
                            echo "Error deleting record: " . $conn->errorInfo();
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>


</body>

</html>