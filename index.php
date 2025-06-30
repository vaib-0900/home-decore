<?php
include 'header.php';
include 'db_connection.php';
?>
<!-- banner part start-->
<section class="banner_part">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="banner_slider owl-carousel">
                    <div class="single_banner_slider">
                        <div class="row">
                            <div class="col-lg-5 col-md-8">
                                <div class="banner_text">
                                    <div class="banner_text_iner">
                                        <h1>Luxury Home Decor</h1>
                                        <p>Transform your living space with our exquisite collection of handcrafted home decor pieces that blend style and functionality seamlessly.</p>
                                        <a href="#" class="btn_2">Shop Now</a>
                                    </div>
                                </div>
                            </div>
                            <div class="banner_img d-none d-lg-block">
                                <img src="img/banner_img.png" alt="Luxury home decor">
                            </div>
                        </div>
                    </div>
                    <div class="single_banner_slider">
                        <div class="row">
                            <div class="col-lg-5 col-md-8">
                                <div class="banner_text">
                                    <div class="banner_text_iner">
                                        <h1>Modern Wall Art</h1>
                                        <p>Elevate your walls with our curated selection of contemporary art pieces that add personality to any room.</p>
                                        <a href="#" class="btn_2">Explore Collection</a>
                                    </div>
                                </div>
                            </div>
                            <div class="banner_img d-none d-lg-block">
                                <img src="img/banner_img1.png" alt="Modern wall art">
                            </div>
                        </div>
                    </div>
                    <div class="single_banner_slider">
                        <div class="row">
                            <div class="col-lg-5 col-md-8">
                                <div class="banner_text">
                                    <div class="banner_text_iner">
                                        <h1>Seasonal Decor</h1>
                                        <p>Refresh your home for every season with our beautiful, trend-forward decorative accents.</p>
                                        <a href="#" class="btn_2">View Seasonal Picks</a>
                                    </div>
                                </div>
                            </div>
                            <div class="banner_img d-none d-lg-block">
                                <img src="img/banner_img2.png" alt="Seasonal home decor">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slider-counter"></div>
            </div>
        </div>
    </div>
</section>
<!-- banner part start-->
<!-- feature_part start-->
<section class="featured-products py-5 bg-light">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="fw-bold position-relative d-inline-block">Featured Products</h2>
            <p class="text-muted">Our most popular items this season</p>
        </div>
        <div class="row g-4">
            <?php
            $query = "SELECT tbl_product.*, tbl_feature.product_id AS feature_product_id 
                      FROM tbl_product 
                      INNER JOIN tbl_feature ON tbl_product.product_id = tbl_feature.product_id";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)):
                    $product_id = $row['product_id'];
                    $rating = rand(3, 5);
                    $discount = $row['product_price'] > $row['sell_price'] ? round(($row['product_price'] - $row['sell_price']) / $row['product_price'] * 100) : 0;
            ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm product-card position-relative">
                            <?php if ($discount > 0): ?>
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">-<?= $discount ?>%</span>
                            <?php endif; ?>
                            <div class="product-img-container position-relative overflow-hidden" style="height: 200px;">
                                <a href="single-product.php?id=<?= $product_id ?>" class="text-decoration-none">
                                    <img src="admin/<?= htmlspecialchars($row['product_image']) ?>" class="img-fluid w-100 h-100 object-fit-contain p-3" alt="<?= htmlspecialchars($row['product_name']) ?>">
                                </a>
                                <div class="product-actions position-absolute top-0 end-0 m-2">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm quick-view" data-id="<?= $product_id ?>" data-bs-toggle="tooltip" title="Quick View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-light text-dark"><?= htmlspecialchars($row['product_name']) ?></span>

                                </div>
                                <h5 class="card-title">
                                    <a href="single-product.php?id=<?= $product_id ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($row['product_name']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted small mb-3">
                                    <?= substr(htmlspecialchars($row['product_description']), 0, 80) ?>...
                                </p>
                                <div class="rating small text-warning">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?= $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>' ?>
                                    <?php endfor; ?>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="text-primary mb-0">
                                            ₹.<?= number_format($row['sell_price'], 2) ?>
                                        </h5>
                                        <?php if (isset($row['product_price']) && $row['product_price'] > $row['sell_price']): ?>
                                            <small class="text-muted" style="text-decoration: line-through;">₹.<?= number_format($row['product_price'], 2) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="action-buttons d-flex gap-1">
                                        <a href="addtocart.php?id=<?= $product_id ?>" class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="tooltip" title="Add to Cart">
                                            <i class="fas fa-cart-plus"></i>
                                        </a>
                                        <a href="wishlist-insert.php?product_id=<?= $product_id ?>" class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="tooltip" title="Add to Wishlist">
                                            <i class="far fa-heart"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <div class="col-12">
                    <p class="text-center text-muted">No featured products found.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="shop.php" class="btn btn-primary btn-lg">View All Products</a>
        </div>
    </div>
</section>

<!-- subscribe_area part start-->
<section class="subscribe_area section_padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="subscribe_area_text text-center">
                    <h5>Join Our Newsletter</h5>
                    <h2>Subscribe to get Updated
                        with new offers</h2>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="enter email address"
                            aria-label="Recipient's username" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <a href="#" class="input-group-text btn_2" id="basic-addon2">subscribe now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--::subscribe_area part end::-->

<!-- subscribe_area part start-->
<section class="client_logo padding_top">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="single_client_logo">
                    <img src="img/client_logo/client_logo_1.png" alt="">
                </div>
                <div class="single_client_logo">
                    <img src="img/client_logo/client_logo_2.png" alt="">
                </div>
                <div class="single_client_logo">
                    <img src="img/client_logo/client_logo_3.png" alt="">
                </div>
                <div class="single_client_logo">
                    <img src="img/client_logo/client_logo_4.png" alt="">
                </div>
                <div class="single_client_logo">
                    <img src="img/client_logo/client_logo_5.png" alt="">
                </div>
                <div class="single_client_logo">
                    <img src="img/client_logo/client_logo_3.png" alt="">
                </div>
                <div class="single_client_logo">
                    <img src="img/client_logo/client_logo_1.png" alt="">
                </div>
                <div class="single_client_logo">
                    <img src="img/client_logo/client_logo_2.png" alt="">
                </div>
                <div class="single_client_logo">
                    <img src="img/client_logo/client_logo_3.png" alt="">
                </div>
                <div class="single_client_logo">
                    <img src="img/client_logo/client_logo_4.png" alt="">
                </div>
            </div>
        </div>
    </div>
</section>
<!--::subscribe_area part end::-->
<?php
include 'footer.php';
?>