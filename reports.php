<?php
session_start();
if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name'])) {
    header('Location: login.php');
    exit();
}

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$initials = strtoupper($first_name[0] . $last_name[0]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'db-styles.php'; ?>
    <style>
        .report-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .report-card {
            flex: 1 1 calc(50% - 40px);
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .report-card h4 {
            margin-bottom: 20px;
        }
        .report-card .btn-group {
            display: flex;
            gap: 10px;
        }
        .report-card .btn {
            flex: 1;
        }
    </style>
</head>
<body>
    <?php include 'db-sidebar.php'; ?>
    <div class="content">
        <span class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></span>
        <div class="container">
            <div class="report-container">
            <div class="report-card">
                <h4>Export Products</h4>
                <div class="btn-group">
                    <button class="btn btn-primary" onclick="window.location.href='report_csv.php?export=products'">Excel</button>
                    <a href="report_pdf.php?export=products" class="btn btn-danger">PDF</a>
                </div>
            </div>
            <div class="report-card">
                <h4>Export Suppliers</h4>
                <div class="btn-group">
                    <button class="btn btn-primary" onclick="window.location.href='report_csv.php?export=suppliers'">Excel</button>
                    <a href="report_pdf.php?export=suppliers" class="btn btn-danger">PDF</a>
                </div>
            </div>
            <div class="report-card">
                <h4>Export Deliveries</h4>
                <div class="btn-group">
                    <button class="btn btn-primary" onclick="window.location.href='report_csv.php?export=deliveries'">Excel</button>
                    <a href="report_pdf.php?export=deliveries" class="btn btn-danger">PDF</a>
                </div>
            </div>
                <div class="report-card">
                    <h4>Export Purchase Orders</h4>
                    <div class="btn-group">
                        <button class="btn btn-primary" onclick="window.location.href='report_csv.php?export=purchase_orders'">Excel</button>
                        <a href="report_pdf.php?export=purchase_orders" class="btn btn-danger">PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'db-toggle.php'; ?>
</body>
</html>
