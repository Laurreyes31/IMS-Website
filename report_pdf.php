<?php
session_start();
if (!isset($_SESSION['first_name']) || !isset($_SESSION['last_name'])) {
    header('Location: login.php');
    exit();
}

require('C:/xampp/htdocs/salfo/fpdf/fpdf.php'); // Change this to the correct path to fpdf.php

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Add a title
        $this->SetFont('Arial', 'B', 14);
        if ($_GET['export'] == 'products') {
            $this->Cell(0, 10, 'Product Reports', 0, 1, 'C');
        } elseif ($_GET['export'] == 'purchase_orders') {
            $this->Cell(0, 10, 'Purchase Order Reports', 0, 1, 'C');
        } elseif ($_GET['export'] == 'suppliers') {
            $this->Cell(0, 10, 'Supplier Reports', 0, 1, 'C');
        } elseif ($_GET['export'] == 'deliveries') {
            $this->Cell(0, 10, 'Delivery Report', 0, 1, 'C');
        }
        $this->Ln(10);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Centered table
    function CenteredTable($header, $data)
    {
        // Calculate table width
        $tableWidth = array_sum($header);

        // Calculate left margin to center the table
        $leftMargin = ($this->w - $tableWidth) / 2;

        // Set left margin
        $this->SetLeftMargin($leftMargin);
        $this->SetX($leftMargin);

        // Table header
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255, 255, 255);
        foreach ($this->headers as $col) {
            $this->Cell($header[$col], 10, $col, 1, 0, 'C', true);
        }
        $this->Ln();

        // Table data
        $this->SetFont('Arial', '', 10); // Reduced font size
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $fill = false;

        foreach ($data as $row) {
            foreach ($header as $col => $width) {
                $this->Cell($width, 10, $row[strtolower(str_replace(' ', '_', $col))], 1, 0, 'C', $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }

        // Reset left margin
        $this->SetLeftMargin(10);
    }
}

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'inventory';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_GET['export'] == 'products') {
    $sql = "
        SELECT 
            p.id, 
            p.product_name, 
            p.stock, 
            CONCAT(u.first_name, ' ', u.last_name) AS created_by, 
            DATE_FORMAT(p.created_at, '%b %d, %Y %h:%i:%s %p') AS created_at, 
            DATE_FORMAT(p.updated_at, '%b %d, %Y %h:%i:%s %p') AS updated_at 
        FROM 
            products p 
        JOIN 
            users u 
        ON 
            p.created_by = u.id";

    $headerWidths = [
        'ID' => 10,
        'Product Name' => 45,
        'Stock' => 20,
        'Created By' => 35,
        'Created At' => 50,
        'Updated At' => 50
    ];
    $headers = ['ID', 'Product Name', 'Stock', 'Created By', 'Created At', 'Updated At'];
} elseif ($_GET['export'] == 'purchase_orders') {
    $sql = "
        SELECT 
            o.id,
            o.quantity_ordered,
            o.quantity_received,
            (o.quantity_ordered - o.quantity_received) AS quantity_remaining,
            o.status,
            o.batch,
            s.supplier_name AS supplier,
            p.product_name AS product,
            CONCAT(u.first_name, ' ', u.last_name) AS created_by 
        FROM 
            order_product o 
        JOIN 
            suppliers s 
        ON 
            o.supplier = s.id 
        JOIN 
            products p 
        ON 
            o.product = p.id 
        JOIN 
            users u 
        ON 
            o.created_by = u.id";

    $headerWidths = [
        'ID' => 10,
        'Quantity Ordered' => 40,
        'Quantity Received' => 40,
        'Quantity Remaining' => 40,
        'Status' => 25,
        'Batch' => 25,
        'Supplier' => 30,
        'Product' => 35,
        'Created By' => 35
    ];
    $headers = [
        'ID',
        'Quantity Ordered',
        'Quantity Received',
        'Quantity Remaining',
        'Status',
        'Batch',
        'Supplier',
        'Product',
        'Created By'
    ];
} elseif ($_GET['export'] == 'suppliers') {
    $sql = "
        SELECT 
            s.id,
            s.supplier_name,
            DATE_FORMAT(s.created_at, '%b %d, %Y %h:%i:%s %p') AS created_at,
            s.supplier_location,
            s.email,
            CONCAT(u.first_name, ' ', u.last_name) AS created_by
        FROM 
            suppliers s
        JOIN 
            users u 
        ON 
            s.created_by = u.id";

    $headerWidths = [
        'ID' => 10,
        'Supplier Name' => 40,
        'Created At' => 50,
        'Supplier Location' => 40,
        'Email' => 50,
        'Created By' => 40
    ];
    $headers = [
        'ID',
        'Supplier Name',
        'Created At',
        'Supplier Location',
        'Email',
        'Created By'
    ];
} elseif ($_GET['export'] == 'deliveries') {
    $sql = "
        SELECT 
            o.created_at AS date_received,
            o.quantity_received,
            p.product_name,
            s.supplier_name,
            o.batch,
            CONCAT(u.first_name, ' ', u.last_name) AS created_by
        FROM 
            order_product o 
        JOIN 
            suppliers s 
        ON 
            o.supplier = s.id 
        JOIN 
            products p 
        ON 
            o.product = p.id 
        JOIN 
            users u 
        ON 
            o.created_by = u.id";

    $headerWidths = [
        'Date Received' => 40,
        'Quantity Received' => 40,
        'Product Name' => 45,
        'Supplier Name' => 40,
        'Batch' => 30,
        'Created By' => 40
    ];
    $headers = [
        'Date Received',
        'Quantity Received',
        'Product Name',
        'Supplier Name',
        'Batch',
        'Created By'
    ];
}

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();

$pdf = new PDF('L'); // Landscape orientation
$pdf->headers = $headers;
$pdf->AddPage();
$pdf->CenteredTable($headerWidths, $data);
if ($_GET['export'] == 'products') {
    $pdf->Output('D', 'products.pdf');
} elseif ($_GET['export'] == 'purchase_orders') {
    $pdf->Output('D', 'purchase_orders.pdf');
} elseif ($_GET['export'] == 'suppliers') {
    $pdf->Output('D', 'suppliers.pdf');
} elseif ($_GET['export'] == 'deliveries') {
    $pdf->Output('D', 'deliveries.pdf');
}
?>
