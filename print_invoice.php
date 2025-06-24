<?php
include "header.php";
include "db_connection.php";

$order_id = mysqli_real_escape_string($conn, $_GET['id']);
$customer_id = $_SESSION['customer_id'];

// Get order details
$order_query = "SELECT * FROM orders WHERE order_id = ? AND customer_id = ?";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, "si", $order_id, $customer_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($order_result) == 0) {
    header("Location: confirmation.php");
    exit();
}

$order = mysqli_fetch_assoc($order_result);

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $items_query);
mysqli_stmt_bind_param($stmt, "s", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

$item_count = mysqli_num_rows($items_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Fix header overlap */
        body {
            padding-top: 70px;
        }
        
        /* Print-specific styles */
        @media print {
            @page {
                size: A4;
                margin: 15mm 15mm 15mm 15mm;
            }
            
            body {
                padding: 0 !important;
                margin: 0 !important;
                background: white !important;
                font-size: 12pt;
            }
            
            .no-print, .no-print * {
                display: none !important;
            }
            
            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .invoice-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
            
            /* Ensure tables don't break across pages */
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            /* Force color printing */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* Screen styles */
        .invoice-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .table-invoice {
            width: 100%;
            margin-bottom: 30px;
        }
        
        .table-invoice th {
            background-color: #f8f9fa !important;
        }
        
        .thank-you {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
        }
        
        /* Fix for printing badges */
        .badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    </style>
</head>
<body>
    <!-- Header is included via PHP -->
    
    <div class="container">
        <div class="invoice-container">
            <div id="printableArea">
                <div class="invoice-header">
                    <h1 class="invoice-title">INVOICE</h1>
                    <p class="text-muted">Order #<?php echo $order_id; ?></p>
                    <span class="badge 
                        <?php
                        switch ($order['status']) {
                            case 'Pending': echo 'bg-warning text-dark'; break;
                            case 'Completed': echo 'bg-success'; break;
                            case 'Processing': echo 'bg-primary'; break;
                            case 'Shipped': echo 'bg-info text-dark'; break;
                            case 'Delivered': echo 'bg-success text-white'; break;
                            case 'Cancelled': echo 'bg-danger text-white'; break;
                            default: echo 'bg-secondary text-white';
                        }
                        ?>">
                        <?php echo $order['status']; ?>
                    </span>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">From:</h5>
                        <p><strong>Nestify Home</strong></p>
                        <p>123 Pet Street</p>
                        <p>Baramati, Near Bus Stand 413102</p>
                        <p>Phone: +91-8007450432</p>
                        <p>Email: support@nestifyhome.com</p>
                        <p>GSTIN: 07ABCDE1234F1Z5</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">To:</h5>
                        <p><strong><?php echo htmlspecialchars($order['name']); ?></strong></p>
                        <p><?php echo htmlspecialchars($order['address']); ?></p>
                        <p><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['zip_code']); ?></p>
                        <p>Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                        <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
                    </div>
                </div>

                <div class="row mt-4 mb-4">
                    <div class="col-md-6">
                        <p><strong>Invoice Date:</strong> <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    </div>
                </div>

                <table class="table table-bordered table-invoice">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-right">Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                    <small class="text-muted">SKU: <?php echo htmlspecialchars($item['product_id']); ?></small>
                                </td>
                                <td class="text-right">₹<?php echo number_format($item['price'], 2); ?></td>
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <td class="text-right">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-weight-bold">Subtotal (<?php echo $item_count; ?> items):</td>
                            <td class="text-right">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right font-weight-bold">Shipping Fee:</td>
                            <td class="text-right">Free</td>
                        </tr>
                        <tr class="bg-light">
                            <td colspan="3" class="text-right font-weight-bold">Total Amount:</td>
                            <td class="text-right font-weight-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="thank-you">
                    <p>Thank you for your business!</p>
                    <strong>Nestify Home - Where Every Home Feels Special!</strong>
                    <p class="mt-2">For any questions regarding this invoice, please contact our customer support.</p>
                </div>
            </div>
            
            <div class="no-print text-center mt-4">
                <button class="btn btn-primary" onclick="printInvoice()">
                    <i class="fas fa-print me-2"></i> Print Invoice
                </button>
                <a href="download_invoice.php?id=<?php echo $order_id; ?>" class="btn btn-secondary ms-2">
                    <i class="fas fa-file-pdf me-2"></i> Download PDF
                </a>
                <a href="confirmation.php" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-arrow-left me-2"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>

    <script>
        function printInvoice() {
            // Store original content
            const originalContent = document.body.innerHTML;
            
            // Get printable area content
            const printContent = document.getElementById('printableArea').innerHTML;
            document.body.innerHTML = printContent;
            window.print();
            window.location.reload(); // Reload to restore original content
            
            // Replace body content with printable area
            document.body.innerHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Invoice #<?php echo $order_id; ?></title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        @page { size: A4; margin: 15mm; }
                        body { font-family: Arial; font-size: 12pt; padding: 0; margin: 0; }
                        .table { width: 100%; border-collapse: collapse; }
                        .table th { background-color: #f8f9fa !important; }
                        .text-right { text-align: right; }
                        .text-center { text-align: center; }
                        .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    </style>
                </head>
                <body onload="window.print()">${printContent}</body>
                </html>
            `;
            
            // Restore original content after printing
            setTimeout(() => {
                document.body.innerHTML = originalContent;
            }, 1000);
        }
    </script>
    
    <?php include "footer.php"; ?>
</body>
</html>