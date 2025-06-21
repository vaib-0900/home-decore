    <?php
    include "header.php";
    include "db_connection.php";


    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
    $customer_id = $_SESSION['customer_id'];

    // Get order details with prepared statement
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

    // Calculate item count
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
            @media print {
                body {
                    padding: 0;
                    margin: 0;
                    background: white;
                }

                .no-print {
                    display: none !important;
                }

                .container {
                    width: 100%;
                    max-width: 100%;
                    padding: 0;
                }

                .invoice-container {
                    box-shadow: none;
                    border: none;
                    padding: 0;
                    margin: 0;
                }
            }

            body {
                background-color: #f8f9fa;
            }

            .invoice-container {
                max-width: 800px;
                margin: 30px auto;
                padding: 30px;
                background: white;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 5px;
            }

            .invoice-header {
                text-align: center;
                margin-bottom: 40px;
                padding-bottom: 20px;
                border-bottom: 1px solid #eee;
            }

            .invoice-title {
                font-size: 28px;
                font-weight: bold;
                color: #333;
                margin-bottom: 5px;
            }

            .invoice-subtitle {
                color: #6c757d;
                font-size: 16px;
            }

            .company-info,
            .customer-info {
                margin-bottom: 30px;
            }

            .info-title {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 15px;
                padding-bottom: 5px;
                border-bottom: 1px solid #eee;
            }

            .table-invoice {
                width: 100%;
                margin-bottom: 30px;
            }

            .table-invoice th {
                background-color: #f8f9fa;
                padding: 10px;
                text-align: left;
                border-bottom: 2px solid #dee2e6;
            }

            .table-invoice td {
                padding: 10px;
                vertical-align: top;
                border-bottom: 1px solid #dee2e6;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            .total-row {
                font-weight: bold;
                background-color: #f8f9fa;
            }

            .thank-you {
                text-align: center;
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid #eee;
                font-style: italic;
                color: #6c757d;
            }

            .status-badge {
                font-size: 14px;
                padding: 5px 10px;
                border-radius: 20px;
            }
        </style>
    </head>

    <body>
        <div class="container mt-5">
            <div class="invoice-container ">
                <!-- Invoice Header -->
                <div id="printableArea">
                    <div class="invoice-header">
                    <h1 class="invoice-title">INVOICE</h1>
                    <p class="invoice-subtitle">Order #<?php echo $order_id; ?></p>
                    <span class="badge 
                        <?php
                        switch ($order['status']) {
                            case 'Pending':
                                echo 'bg-warning text-dark';
                                break;
                            case 'Completed':
                                echo 'bg-success text-dark';
                                break;
                            case 'Processing':
                                echo 'bg-primary text-white';
                                break;
                            case 'Shipped':
                                echo 'bg-info text-dark';
                                break;
                            case 'Delivered':
                                echo 'bg-success text-white';
                                break;
                            case 'Cancelled':
                                echo 'bg-danger text-white';
                                break;
                            default:
                                echo 'bg-secondary text-white';
                        }
                        ?> status-badge">
                        <?php echo $order['status']; ?>
                    </span>
                </div>

                <div class="row mt-5">
                    <div class="col-md-6 company-info">
                        <h5 class="info-title">From:</h5>
                        <p><strong>Nestify Home</strong></p>
                        <p>123 Pet Street</p>
                        <p>Baramati, Near Bus Stand 413102</p>
                        <p>Phone: +91-8007450432</p>
                        <p>Email: support@nestifyhome.com</p>
                        <p>GSTIN: 07ABCDE1234F1Z5</p>
                    </div>    <div class="col-md-6 customer-info">
                        <h5 class="info-title">To:</h5>
                        <p><strong><?php echo htmlspecialchars($order['name']); ?></strong></p>
                        <p><?php echo htmlspecialchars($order['address']); ?></p>
                        <p><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['zip_code']); ?></p>
                        <p>Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                        <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Invoice Date:</strong> <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    </div>
                </div>

                <!-- Order Items -->
                <table class="table table-invoice">
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
                                <td class="text-right">Rs.<?php echo number_format($item['price'], 2); ?></td>
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <td class="text-right">Rs.<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Subtotal (<?php echo $item_count; ?> items):</strong></td>
                            <td class="text-right">Rs.<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Shipping Fee:</strong></td>
                            <td class="text-right">Free</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                            <td class="text-right">Rs.<?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Thank You Message -->
                <div class="thank-you">
                        Thank you for your business! <br>
                        <strong>Nestify Home - Where Every Home Feels Special!</strong>
                        <p>For any questions regarding this invoice, please contact our customer support.</p>
                </div>

                </div>
                <!-- Print Button -->
                <div class="no-print text-center mt-4">
                    <button class="btn btn-primary" onclick="printTable();">
                        <i class="fas fa-print me-2"></i> Print Invoice
                    </button>

                    <a href="download_invoice.php?id=<?php echo $order_id; ?>" class="btn btn-secondary ms-2">
                        <i class="fas fa-file-download me-2"></i> Download PDF
                    </a>
                    <a href="confirmation.php" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left me-2"></i> Back to Orders
                    </a>
                </div>
            </div>
        </div>

        <script>
            function printTable() {
                var printContent = document.getElementById("printableArea").innerHTML;
                document.body.innerHTML = printContent;
                window.print();
                window.location.reload();
            }
        </script>
    </body>


    </html>

    <?php
    include "footer.php"; ?>