    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <a href="index.php">
                        <i class="fas fa-car"></i>
                        <span>Cars<span class="logo-highlight">Dekho</span></span>
                    </a>
                </div>
                
                <div class="nav-menu" id="navMenu">
                    <a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>><i class="fas fa-home"></i> Home</a>
                    <a href="form.php" <?php echo basename($_SERVER['PHP_SELF']) == 'form.php' ? 'class="active"' : ''; ?>><i class="fas fa-car"></i> Select Car</a>
                    
                    <!-- Dynamic Header Content -->
                    <?php
                    if (!class_exists('Database')) {
                        require_once (dirname(__DIR__) . '/config/database.php');
                    }
                    
                    try {
                        $database_header = new Database();
                        $db_header = $database_header->getConnection();
                        $query_header = "SELECT * FROM website_content WHERE section = 'header' AND is_active = 1 ORDER BY display_order";
                        $stmt_header = $db_header->prepare($query_header);
                        $stmt_header->execute();
                        
                        while ($row = $stmt_header->fetch(PDO::FETCH_ASSOC)) {
                            echo '<a href="' . ($row['link_url'] ?: '#') . '"><i class="fas fa-info-circle"></i> ' . htmlspecialchars($row['content']) . '</a>';
                        }
                    } catch(Exception $e) {
                        // Silent fail
                    }
                    ?>
                    
                    <a href="admin/login.php" class="admin-link"><i class="fas fa-user-cog"></i> Admin</a>
                </div>
                
                <div class="mobile-toggle" id="mobileToggle">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>
