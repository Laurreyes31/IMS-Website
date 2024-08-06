<?php
session_start();
if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name'])) {
    header('Location: login.php');
    exit();
}

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$initials = strtoupper($first_name[0] . $last_name[0]);

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'inventory';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Query for pie chart
$sql = "SELECT status, COUNT(*) as count FROM order_product GROUP BY status";
$result = $conn->query($sql);

$statuses = [];
while ($row = $result->fetch_assoc()) {
    $statuses[] = [
        'name' => $row['status'],
        'y' => (int)$row['count']
    ];
}

$statuses_json = json_encode($statuses);

// Query for bar chart
$sql2 = "SELECT s.supplier_name, COUNT(p.id) as product_count FROM suppliers s 
         JOIN order_product op ON s.id = op.supplier 
         JOIN products p ON op.product = p.id 
         GROUP BY s.supplier_name";
$result2 = $conn->query($sql2);

$suppliers = [];
while ($row = $result2->fetch_assoc()) {
    $suppliers[] = [
        'name' => $row['supplier_name'],
        'y' => (int)$row['product_count']
    ];
}

$suppliers_json = json_encode($suppliers);

// Query for delivery history spline chart
$sql3 = "SELECT DATE(date_received) as date_received, SUM(qty_received) as total_received 
         FROM order_product_history 
         GROUP BY DATE(date_received) 
         ORDER BY date_received";
$result3 = $conn->query($sql3);

$dates = [];
$totals = [];

while ($row = $result3->fetch_assoc()) {
    $dates[] = $row['date_received'];
    $totals[] = (int)$row['total_received'];
}

$dates_json = json_encode($dates);
$totals_json = json_encode($totals);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include 'db-styles.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
</head>
<body>
    <?php include 'db-sidebar.php'; ?>
    <div class="content">
        <span class="toggle-btn" id="toggleBtn"><i class="fas fa-bars"></i></span>
        <div class="container mt-4">
            <h2>Purchase Orders By Status</h2>
            <div id="orderStatusChart"></div>
            <p>Here is the breakdown of the purchase orders by status.</p>
            
            <h2>Product Count Assigned To Supplier</h2>
            <div id="supplierProductCountChart"></div>
            <p>Here is the breakdown of the product count by supplier.</p>
            
            <h2>Delivery History Per Day</h2>
            <div id="deliveryHistoryChart"></div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var statuses = <?php echo $statuses_json; ?>;
            Highcharts.chart('orderStatusChart', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Purchase Orders By Status'
                },
                plotOptions: {
                    pie: {
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y}'
                        }
                    }
                },
                series: [{
                    name: 'Orders',
                    colorByPoint: true,
                    data: statuses
                }]
            });

            var suppliers = <?php echo $suppliers_json; ?>;
            Highcharts.chart('supplierProductCountChart', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Product Count Assigned To Supplier'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Product Count'
                    }
                },
                series: [{
                    name: 'Suppliers',
                    colorByPoint: true,
                    data: suppliers,
                    showInLegend: false
                }]
            });

            var dates = <?php echo $dates_json; ?>;
            var totals = <?php echo $totals_json; ?>;
            Highcharts.chart('deliveryHistoryChart', {
                chart: {
                    type: 'spline'
                },
                title: {
                    text: 'Delivery History Per Day'
                },
                xAxis: {
                    categories: dates,
                    title: {
                        text: 'Date'
                    }
                },
                yAxis: {
                    title: {
                        text: 'Product Delivered'
                    }
                },
                series: [{
                    name: 'Product Delivered',
                    data: totals
                }]
            });
        });
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'db-toggle.php'; ?>
</body>
</html>
