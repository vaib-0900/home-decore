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
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2 class="section-title">Featured Products</h2>
                <div>
                    <button class="btn btn-outline-secondary me-2 featured-prev rounded-circle">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-outline-secondary featured-next rounded-circle">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="swiper featured-slider overflow-hidden">
            <div class="swiper-wrapper">
                <?php

                $query = "SELECT * FROM tbl_product INNER JOIN tbl_feature ON tbl_product.product_id = tbl_feature.product_id";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                ?>
                        <div class="swiper-slide">
                            <div class="card h-100 border-0 shadow-sm">
                                <!-- Product Badge -->
                                <?php if ($row['discount_per'] > 0): ?>
                                    <span class="badge bg-danger position-absolute m-2">-<?= $row['discount_per'] ?>%</span>
                                <?php endif; ?>

                                <!-- Product Image -->
                                <div class="position-relative overflow-hidden text-center">
                                    <a href="single-product.php?id=<?= $row['product_id'] ?>" class="text-decoration-none">
                                            <img src="admin/<?= htmlspecialchars($row['product_image']) ?>" class="img-thumbnail" alt="<?= htmlspecialchars($row['product_image']) ?>">
                                            <div class="product-actions position-absolute top-0 end-0 m-2">                         
                                            <button class="btn btn-sm btn-light rounded-circle shadow-sm quick-view" data-id="<?= $row['product_id'] ?>" data-bs-toggle="tooltip" title="Quick View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                           
                                        </div>
                                        </a>

                                </div>

                                <!-- Product Body -->
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-light text-dark"><?= $row['category_name'] ?? 'Category' ?></span>
                                        <div class="text-warning">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                    </div>

                                    <h5 class="card-title">
                                        <a href="single_product.php?product_id=<?= $row['product_id'] ?>" class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($row['product_name']) ?>
                                        </a>
                                    </h5>

                                    <p class="card-text text-muted small">
                                        <?= substr(htmlspecialchars($row['product_description'] ?? ''), 0, 80) ?>...
                                    </p>

                                    <!-- Price -->
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <h5 class="text-primary mb-0">Rs. <?= number_format($row['sell_price'], 2) ?></h5>
                                            <?php if ($row['discount_per'] > 0 && isset($row['original_price'])): ?>
                                                <small class="text-muted text-decoration-line-through">Rs. <?= number_format($row['original_price'], 2) ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="action-buttons d-flex gap-2 flex-wrap">
                                            <a href="single-product.php?id=<?= $row['product_id'] ?>"
                                                class="btn btn-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center">
                                                <i class="fas fa-eye me-1"></i>
                                                <span>View</span>
                                            </a>
                                            <a href="addtocart.php?id=<?= $row['product_id'] ?>"
                                                class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center">
                                                <i class="fas fa-cart-plus me-1"></i>
                                                <span>Add</span>
                                            </a>
                                            <a href="wishlist-insert.php?id=<?= $row['product_id'] ?>"
                                                class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center">
                                                <i class="far fa-heart me-1"></i>
                                                <span>Wishlist</span>

                                            </a>
                                        </div>



                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<div class="col-12 text-center py-5"><div class="alert alert-info">No featured products found.</div></div>';
                }
                ?>
            </div>
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