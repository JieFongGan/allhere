<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin'])) {
    $admin = $_SESSION['admin'];
} else {
    header("Location: adlogin.php");
    exit();
}

?>

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
            <li><a href="adlogout.php">Log Out</a></li>
        </ul>
    </div>

    <div class="container">
        <div class="content">
            <div class="search-container">
                <form action="adminuserlist.php" method="GET">
                    <input type="text" id="searchInput" name="search" 
                    placeholder="Search by Company Name, User ID, or Status">
                    <button type="submit">Search</button>
                </form>
            </div>
            <br>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User ID</th>
                            <th>Company Name</th>
                            <th>Status</th>
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

                    $searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
                    $query = "SELECT UserID, CompanyName, Status FROM [user] WHERE 
                                UserID LIKE '%$searchKeyword%' OR
                                CompanyName LIKE '%$searchKeyword%' OR
                                Status LIKE '%$searchKeyword%'";
                    $result = $conn->query($query);
                    

                    // Display the fetched data
                    $number = 1;
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $number . "</td>";
                        echo "<td>" . $row['UserID'] . "</td>";
                        echo "<td>" . $row['CompanyName'] . "</td>";
                        echo "<td>" . $row['Status'] . "</td>";
                        echo "<td>";
                        echo "<button style='display: inline-block;'><a href='aduseredit.php?userid=" . $row['UserID'] . "' style='color: inherit; text-decoration: none;'>Edit</a></button>";
                        echo "&nbsp;";
                        echo "</td>";
                        echo "</tr>";
                        $number++;
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
