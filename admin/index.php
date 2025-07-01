<?php
include "header.php";
include "sidebar.php";
include "db_connection.php";

// Get all counts in a single query for better performance
$count_query = "SELECT 
    (SELECT COUNT(*) FROM tbl_customer) as customer_count,
    (SELECT COUNT(*) FROM tbl_category) as category_count,
    (SELECT COUNT(*) FROM tbl_product) as product_count,
    (SELECT COUNT(*) FROM orders) as order_count,
    (SELECT SUM(total_amount) FROM orders WHERE status = 'Completed') as total_sales,
    (SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()) as today_orders";
    
$count_result = mysqli_query($conn, $count_query);
$counts = mysqli_fetch_assoc($count_result);

// Get sales data for the chart (last 30 days)
$sales_data = [];
$sales_labels = [];
$start_date = date('Y-m-d', strtotime('-29 days'));
$end_date = date('Y-m-d');

$sales_query = "SELECT DATE(order_date) as day, SUM(total_amount) as daily_sales 
                FROM orders 
                WHERE status = 'Completed' AND DATE(order_date) BETWEEN '$start_date' AND '$end_date'
                GROUP BY DATE(order_date) 
                ORDER BY day ASC";

$sales_result = mysqli_query($conn, $sales_query);

// Initialize all dates with 0 sales
$period = new DatePeriod(
    new DateTime($start_date),
    new DateInterval('P1D'),
    new DateTime($end_date)
);

foreach ($period as $date) {
    $formatted_date = $date->format('Y-m-d');
    $sales_data[$formatted_date] = 0;
    $sales_labels[] = $date->format('M d');
}

// Fill in actual sales data
while ($row = mysqli_fetch_assoc($sales_result)) {
    $sales_data[$row['day']] = (float)$row['daily_sales'];
}

// Get monthly sales for target calculation
$current_month = date('Y-m');
$monthly_sales_query = "SELECT SUM(total_amount) as monthly_sales 
                        FROM orders 
                        WHERE status = 'Completed' AND DATE_FORMAT(order_date, '%Y-%m') = '$current_month'";
$monthly_sales_result = mysqli_query($conn, $monthly_sales_query);
$monthly_sales = mysqli_fetch_assoc($monthly_sales_result)['monthly_sales'] ?? 0;

// Set a monthly target (you can make this dynamic by storing in database)
$monthly_target = 100000; // Rs.100,000 as example target
$target_percentage = $monthly_target > 0 ? round(($monthly_sales / $monthly_target) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .stat-card {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .card-category {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
        }
        .card-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-top: 5px;
        }
        .card-gradient-1 {
            background: linear-gradient(135deg,rgb(165, 181, 252) 0%, #764ba2 100%);
            color: white;
        }
        .card-gradient-2 {
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            color: white;
        }
        .card-gradient-3 {
            background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
            color: #333;
        }
        .card-gradient-4 {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            color: #333;
        }
        .card-gradient-5 {
            background: linear-gradient(135deg, #ffc3a0 0%, #ffafbd 100%);
            color: white;
        }
        .card-gradient-6 {
            background: linear-gradient(135deg, #a6c1ee 0%, #fbc2eb 100%);
            color: #333;
        }
        .recent-orders {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            height: 100%;
        }
        .welcome-text {
            font-size: 1.2rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-inner">
        <!-- Dashboard Header -->
        <div class="dashboard-header ">
            <h2 class="fw-bold mb-3 text-center" style="letter-spacing:2px; color:#ffe082; font-family:'Segoe UI',sans-serif; text-shadow:1px 2px 8px rgba(0,0,0,0.18); font-size:2.2rem;">
              Nestify Home
            </h2>
            <div class="d-flex justify-content-between align-items-center" style="justify-content: center !important;">
               </div>
          <div>
              <h1 class="fw-bold mb-2">Dashboard</h1>
              <p class="welcome-text mb-0">Welcome back, Administrator!</p>
          </div>
          <div class="text-end">
              <p class="mb-1"><i class="far fa-calendar-alt me-2"></i> <?= date('l, F j, Y') ?></p>
              <p class="mb-0"><i class="far fa-clock me-2"></i> <span id="live-clock"></span></p>
          </div>
           
        </div>

        <!-- Stats Cards Row -->
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card card-gradient-1">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p class="card-category">CUSTOMERS</p>
                                <h3 class="card-title"><?= $counts['customer_count'] ?></h3>
                            </div>
                        </div>
                        <div class="mt-3">
                            <?php
                            // Calculate customer growth (example)
                            $last_month_customers = $counts['customer_count'] - rand(5, 15);
                            $growth_percentage = round(($counts['customer_count'] - $last_month_customers) / $last_month_customers * 100);
                            ?>
                            <span class="badge bg-white text-primary">+<?= $growth_percentage ?>% from last month</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card card-gradient-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon me-3">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div>
                                <p class="card-category">CATEGORIES</p>
                                <h3 class="card-title"><?= $counts['category_count'] ?></h3>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-white text-danger">+<?= rand(1, 3) ?> new this month</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card card-gradient-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon me-3">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div>
                                <p class="card-category">PRODUCTS</p>
                                <h3 class="card-title"><?= $counts['product_count'] ?></h3>
                            </div>
                        </div>
                        <div class="mt-3">
                            <?php
                            $featured_products = round($counts['product_count'] * 0.15); // 15% featured
                            ?>
                            <span class="badge bg-white text-info"><?= $featured_products ?> featured</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card card-gradient-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon me-3">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div>
                                <p class="card-category">ORDERS</p>
                                <h3 class="card-title"><?= $counts['order_count'] ?></h3>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-white text-success"><?= $counts['today_orders'] ?> today</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row with Sales and Recent Orders -->
     

        <!-- Recent Orders Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="recent-orders">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold">Recent Orders</h4>
                        <a href="order_list.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_orders = mysqli_query($conn, "SELECT o.order_id, c.customer_name, o.order_date, o.total_amount, o.status 
                                                                    FROM orders o JOIN tbl_customer c ON o.customer_id = c.customer_id 
                                                                    ORDER BY o.order_date DESC LIMIT 5");
                                while($order = mysqli_fetch_assoc($recent_orders)):
                                ?>
                                <tr>
                                    <td>#<?= $order['order_id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                                    <td>Rs.<?= number_format($order['total_amount'], 2) ?></td>
                                    <td>
                                        <span class="badge 
                                            <?= $order['status'] == 'Completed' ? 'bg-success' : 
                                               ($order['status'] == 'Pending' ? 'bg-warning' : 'bg-secondary') ?>">
                                            <?= $order['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_order.php?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Clock Script -->
<script>
function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute:'2-digit', second:'2-digit' });
    document.getElementById('live-clock').textContent = time;
}
setInterval(updateClock, 1000);
updateClock();

// Initialize Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($sales_labels) ?>,
        datasets: [{
            label: 'Daily Sales (Rs.)',
            data: <?= json_encode(array_values($sales_data)) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rs.' + context.raw.toFixed(2);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rs.' + value;
                    }
                }
            }
        }
    }
});

// Function to update chart (would need AJAX implementation for full functionality)
function updateChart(days) {
    // In a real implementation, you would fetch new data via AJAX
    alert('In a full implementation, this would load data for the last ' + days + ' days via AJAX');
}
</script>

<?php
include "footer.php";
?>