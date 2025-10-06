<?php
session_start();
include "db_connection.php";

header('Content-Type: application/json');

// Check if query parameter exists and is not empty
if(isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $searchTerm = trim($_GET['query']);
    
    // Use prepared statements to prevent SQL injection
    $query = "SELECT p.*, c.category_name 
              FROM tbl_products p 
              LEFT JOIN tbl_categories c ON p.product_category = c.category_id 
              WHERE p.product_status = '1' 
              AND (p.product_name LIKE ? 
                   OR p.product_description LIKE ?
                   OR c.category_name LIKE ?)
              ORDER BY 
                CASE 
                    WHEN p.product_name LIKE ? THEN 1
                    WHEN p.product_description LIKE ? THEN 2
                    WHEN c.category_name LIKE ? THEN 3
                    ELSE 4
                END,
                p.product_name 
              LIMIT 10";
    
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        $searchPattern = "%" . $searchTerm . "%";
        mysqli_stmt_bind_param($stmt, "ssssss", 
            $searchPattern, $searchPattern, $searchPattern,
            $searchPattern, $searchPattern, $searchPattern
        );
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $products = [];
        while($row = mysqli_fetch_assoc($result)) {
            $products[] = [
                'id' => $row['product_id'],
                'name' => $row['product_name'],
                'price' => '$' . number_format($row['product_price'], 2),
                'category' => $row['category_name'] ?? 'Uncategorized',
                'image' => !empty($row['product_image']) ? 'admin/uploads/' . $row['product_image'] : 'img/product/default.jpg',
                'slug' => $row['product_slug'] ?? $row['product_id']
            ];
        }
        
        mysqli_stmt_close($stmt);
        
        echo json_encode([
            'success' => true,
            'products' => $products,
            'count' => count($products)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database query failed: ' . mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No search query provided'
    ]);
}

mysqli_close($conn);
?>