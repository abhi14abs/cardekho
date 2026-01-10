<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in or not
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get statistics
$totalResponses = $db->query("SELECT COUNT(*) FROM customer_responses")->fetchColumn();
$totalCars = $db->query("SELECT COUNT(*) FROM cars")->fetchColumn();
$totalMostSearched = $db->query("SELECT COUNT(*) FROM cars WHERE category = 'most_searched'")->fetchColumn();
$totalLatest = $db->query("SELECT COUNT(*) FROM cars WHERE category = 'latest'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CarsDekho</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-user-cog"></i> Admin Dashboard</h1>
            <p>Welcome, <?php echo $_SESSION['admin_username']; ?>!</p>
        </div>
        
        <div class="admin-nav">
            <ul>
                <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage-content.php"><i class="fas fa-edit"></i> Manage Content</a></li>
                <li><a href="manage-cars.php"><i class="fas fa-car"></i> Manage Cars</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #2196f3;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Responses</h3>
                    <p class="stat-number"><?php echo $totalResponses; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #4caf50;">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Cars</h3>
                    <p class="stat-number"><?php echo $totalCars; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #ff9800;">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="stat-info">
                    <h3>Most Searched</h3>
                    <p class="stat-number"><?php echo $totalMostSearched; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #9c27b0;">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>Latest Cars</h3>
                    <p class="stat-number"><?php echo $totalLatest; ?></p>
                </div>
            </div>
        </div>
        
        <div class="admin-table">
            <h2><i class="fas fa-list"></i> Recent Customer Responses</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Car Types</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM customer_responses ORDER BY created_at DESC LIMIT 10";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $carTypes = json_decode($row['car_types'], true);
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                        echo '<td>' . implode(', ', array_map('htmlspecialchars', $carTypes)) . '</td>';
                        echo '<td>' . date('d M Y', strtotime($row['created_at'])) . '</td>';
                        echo '<td>';
                        echo '<button class="btn-edit"><i class="fas fa-eye"></i> View</button>';
                        echo '<button class="btn-delete" onclick="deleteResponse(' . $row['id'] . ')"><i class="fas fa-trash"></i> Delete</button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    function deleteResponse(id) {
        if (confirm('Are you sure you want to delete this response?')) {
            window.location.href = 'delete-response.php?id=' + id;
        }
    }
    </script>
</body>
</html>