<?php
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarsDekho - Find Your Dream Car</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
</head>
<body>
    <!-- Header Section -->
    <?php include 'includes/header.php'; ?>

    <!-- Banner Section -->
    <?php
    // Fetch Banner
    $banner_title = "Find Your Perfect Car";
    $banner_text = "Browse through thousands of new and used cars with the best deals";
    $banner_bg = ""; // Default CSS gradient used if empty
    
    try {
        $query_banner = "SELECT * FROM website_content WHERE section = 'banner' AND is_active = 1 ORDER BY id DESC LIMIT 1";
        $stmt_banner = $db->prepare($query_banner);
        $stmt_banner->execute();
        $banner = $stmt_banner->fetch(PDO::FETCH_ASSOC);
        
        if ($banner) {
            $banner_title = htmlspecialchars($banner['title']);
            $banner_text = htmlspecialchars($banner['content']);
            if ($banner['image_url']) {
                $banner_bg = "background-image: url('" . $banner['image_url'] . "');";
            }
        }
    } catch(Exception $e) { }
    ?>
    <section class="banner" style="<?php echo $banner_bg; ?>">
        <div class="container">
            <div class="banner-content">
                <h1><?php echo $banner_title; ?></h1>
                <p><?php echo $banner_text; ?></p>
                <div class="search-box">
                    <input type="text" placeholder="Search by brand, model, or keyword...">
                    <button class="search-btn"><i class="fas fa-search"></i> Search</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Most Searched Cars Section -->
    <section class="section most-searched">
        <div class="container">
            <h2 class="section-title">Most Searched Cars</h2>
            <div class="car-grid">
                <?php
                try {
                    $query = "SELECT * FROM cars WHERE category = 'most_searched' AND is_active = 1 ORDER BY created_at DESC LIMIT 6";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $img_src = $row['image_url'] ? $row['image_url'] : 'https://via.placeholder.com/400x300?text=Car+Image';
                        echo '
                        <div class="car-card">
                            <div class="car-image">
                                <img src="' . htmlspecialchars($img_src) . '" alt="' . htmlspecialchars($row['brand'] . ' ' . $row['model']) . '">
                                <div class="car-badge">Hot</div>
                            </div>
                            <div class="car-info">
                                <h3>' . htmlspecialchars($row['brand'] . ' ' . $row['model']) . '</h3>
                                <p class="car-year">' . htmlspecialchars($row['year']) . '</p>
                                <p class="car-price">₹ ' . number_format($row['price'], 2) . '</p>
                                <p class="car-desc">' . htmlspecialchars(substr($row['description'] ?? '', 0, 100)) . '...</p>
                                <a href="#" class="btn-view">View Details</a>
                            </div>
                        </div>';
                    }
                } catch(Exception $e) { echo "Error loading cars."; }
                ?>
            </div>
        </div>
    </section>

    <!-- Latest Cars Section -->
    <section class="section latest-cars">
        <div class="container">
            <h2 class="section-title">Latest Cars</h2>
            <div class="car-grid">
                <?php
                try {
                    $query = "SELECT * FROM cars WHERE category = 'latest' AND is_active = 1 ORDER BY created_at DESC LIMIT 4";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $img_src = $row['image_url'] ? $row['image_url'] : 'https://via.placeholder.com/400x300?text=New+Car';
                        echo '
                        <div class="car-card">
                            <div class="car-image">
                                <img src="' . htmlspecialchars($img_src) . '" alt="' . htmlspecialchars($row['brand'] . ' ' . $row['model']) . '">
                                <div class="car-badge new">New</div>
                            </div>
                            <div class="car-info">
                                <h3>' . htmlspecialchars($row['brand'] . ' ' . $row['model']) . '</h3>
                                <p class="car-year">' . htmlspecialchars($row['year']) . '</p>
                                <p class="car-price">₹ ' . number_format($row['price'], 2) . '</p>
                                <div class="car-features">
                                    <span><i class="fas fa-gas-pump"></i> Fuel</span>
                                    <span><i class="fas fa-cogs"></i> Auto</span>
                                    <span><i class="fas fa-users"></i> 5 Seats</span>
                                </div>
                                <a href="#" class="btn-view">View Details</a>
                            </div>
                        </div>';
                    }
                } catch(Exception $e) { }
                ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Find Your Dream Car?</h2>
                <p>Fill out our quick form and get personalized recommendations</p>
                <a href="form.php" class="btn-cta">
                    <i class="fas fa-car"></i> Select Your Car Now
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>