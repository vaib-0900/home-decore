<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo mb-4">
        <!-- Logo Header -->
        <div class="logo-header d-flex align-items-center justify-content-between px-3 py-2  rounded shadow-sm"data-background-color="dark">
            <div class="d-flex align-items-center">
                <i class="fas fa-home text-white fs-4 me-2"></i>
                <span class="fw-bold text-white fs-5" style="letter-spacing:1px;">Nestify Home</span>
            </div>
          
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <?php
            // Database connection (adjust credentials as needed)
            $conn = new mysqli("localhost", "root", "", "admin_home");

            // Function to get count from a table
            function getCount($conn, $table) {
                $result = $conn->query("SELECT COUNT(*) as cnt FROM $table");
                $row = $result->fetch_assoc();
                return $row['cnt'];
            }

            $category_count = getCount($conn, "tbl_category");
            $product_count = getCount($conn, "tbl_product");
            $customer_count = getCount($conn, "tbl_customer");
            $contact_count = getCount($conn, "tbl_contact");
            $feature_count = getCount($conn, "tbl_feature");
            $order_count = getCount($conn, "orders");
            ?>

            <ul class="nav nav-secondary">
                <li class="nav-item ">
                    <a href="index.php" class="collapsed">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="category_list.php" class="collapsed">
                        <i class="fas fa-th-large"></i>
                        <p>Category <span class="badge badge-info"><?php echo $category_count; ?></span></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="product_list.php" class="collapsed">
                        <i class="fas fa-shopping-bag"></i>
                        <p>Product <span class="badge badge-info"><?php echo $product_count; ?></span></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="customer_list.php" class="collapsed">
                        <i class="fas fa-user"></i>
                        <p>Customer <span class="badge badge-info"><?php echo $customer_count; ?></span></p>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="contact_list.php" class="collapsed">
                        <i class="fas fa-user"></i>
                        <p>Contact <span class="badge badge-info"><?php echo $contact_count; ?></span></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="feature_list.php" class="collapsed">
                        <i class="fas fa-star"></i>
                        <p>Featured <span class="badge badge-info"><?php echo $feature_count; ?></span></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="order_list.php" class="collapsed">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Orders <span class="badge badge-info"><?php echo $order_count; ?></span></p>
                    </a>
                </li>
            </ul>
            <?php $conn->close(); ?>
    </div>
</div>
</div>
<!-- End Sidebar -->