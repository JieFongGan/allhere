<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <title>
        <?php echo $pageTitle; ?>
    </title>

    <!-- styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
    <script src="../script/script.js"></script>
</head>

<body>
    <input type="checkbox" id="sidebar-toggle">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3 class="brand">
                <span class="ti-unlink"></span>
                <span>
                    <?php echo $companyname ?>
                </span>
            </h3>
            <label for="sidebar-toggle" class="ti-menu-alt"></label>
        </div>

        <div class="sidebar-menu">
            <ul>
                <li>
                    <a href="../dashboard/homepage.php">
                        <span class="ti-home"></span>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="../dashboard/inventory.php">
                        <span class="ti-package"></span>
                        <span>Inventory</span>
                    </a>
                </li>
                <li>
                    <a href="../dashboard/transaction.php">
                        <span class="ti-shopping-cart"></span>
                        <span>Transaction</span>
                    </a>
                </li>
                <li>
                    <a href="../dashboard/warehouse.php">
                        <span class="ti-truck"></span>
                        <span>Warehouse</span>
                    </a>
                </li>
                <?php if ($userrole !== 'User'): ?>
                    <li>
                        <a href="../dashboard/customer.php">
                            <span class="ti-agenda"></span>
                            <span>Customer</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="../dashboard/report.php">
                        <span class="ti-pie-chart"></span>
                        <span>Report</span>
                    </a>
                </li>
                <?php if ($userrole !== 'User'): ?>
                    <li>
                        <a href="../dashboard/settings.php">
                            <span class="ti-settings"></span>
                            <span>Setting</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>