<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle Add/Edit/Delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if POST max size exceeded
    if (empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $max_size = ini_get('post_max_size');
        header("Location: manage-cars.php?msg=error&error=File too large. Maximum allowed size is $max_size");
        exit();
    }

    if (isset($_POST['delete_id'])) {
        // Delete car
        $query = "DELETE FROM cars WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $_POST['delete_id']);
        $stmt->execute();
        header('Location: manage-cars.php?msg=deleted');
        exit();
    } else {
        // Add or Update car
        $id = $_POST['id'] ?? 0;
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Handle image upload
        $image_url = $_POST['existing_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../uploads/cars/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = 'uploads/cars/' . $file_name;
            }
        }
        
        if ($id > 0) {
            // Update existing car
            $query = "UPDATE cars SET brand = :brand, model = :model, year = :year, 
                     price = :price, category = :category, description = :description, 
                     image_url = :image_url, is_active = :is_active, updated_at = NOW() 
                     WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
        } else {
            // Insert new car
            $query = "INSERT INTO cars (brand, model, year, price, category, description, image_url, is_active) 
                     VALUES (:brand, :model, :year, :price, :category, :description, :image_url, :is_active)";
            $stmt = $db->prepare($query);
        }
        
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->bindParam(':is_active', $is_active);
        
        $stmt->execute();
        
        $msg = $id > 0 ? 'updated' : 'added';
        header("Location: manage-cars.php?msg=$msg");
        exit();
    }
}

// Fetch all cars
$query = "SELECT * FROM cars ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get car for editing if ID is provided
$edit_car = null;
if (isset($_GET['edit'])) {
    $query = "SELECT * FROM cars WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['edit']);
    $stmt->execute();
    $edit_car = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .image-preview {
            max-width: 200px;
            margin: 10px 0;
            border-radius: 5px;
            border: 2px solid #ddd;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'admin-header.php'; ?>
        
        <div class="admin-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage-content.php"><i class="fas fa-edit"></i> Manage Content</a></li>
                <li><a href="manage-cars.php" class="active"><i class="fas fa-car"></i> Manage Cars</a></li>
                <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'error'): ?>
                <div class="message error" style="background: #ffebee; color: #c62828; border-color: #ef9a9a;">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo htmlspecialchars($_GET['error'] ?? 'An error occurred'); ?>
                </div>
            <?php else: ?>
                <div class="message <?php echo $_GET['msg']; ?>">
                    <i class="fas fa-check-circle"></i> 
                    Car <?php echo $_GET['msg']; ?> successfully!
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="form-container">
            <h2><i class="fas fa-car"></i> <?php echo $edit_car ? 'Edit Car' : 'Add New Car'; ?></h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $edit_car['id'] ?? 0; ?>">
                
                <?php if ($edit_car && $edit_car['image_url']): ?>
                    <input type="hidden" name="existing_image" value="<?php echo $edit_car['image_url']; ?>">
                    <div>
                        <img src="../<?php echo $edit_car['image_url']; ?>" class="image-preview" alt="Current Image">
                    </div>
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="brand">Brand *</label>
                        <input type="text" id="brand" name="brand" required 
                               value="<?php echo htmlspecialchars($edit_car['brand'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="model">Model *</label>
                        <input type="text" id="model" name="model" required 
                               value="<?php echo htmlspecialchars($edit_car['model'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="year">Year *</label>
                        <input type="number" id="year" name="year" min="2000" max="2024" required 
                               value="<?php echo htmlspecialchars($edit_car['year'] ?? date('Y')); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price (₹) *</label>
                        <input type="number" id="price" name="price" step="0.01" required 
                               value="<?php echo htmlspecialchars($edit_car['price'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="most_searched" <?php echo ($edit_car['category'] ?? '') == 'most_searched' ? 'selected' : ''; ?>>Most Searched</option>
                        <option value="latest" <?php echo ($edit_car['category'] ?? '') == 'latest' ? 'selected' : ''; ?>>Latest</option>
                        <option value="hatchback" <?php echo ($edit_car['category'] ?? '') == 'hatchback' ? 'selected' : ''; ?>>Hatchback</option>
                        <option value="sadan" <?php echo ($edit_car['category'] ?? '') == 'sadan' ? 'selected' : ''; ?>>Sedan</option>
                        <option value="suv" <?php echo ($edit_car['category'] ?? '') == 'suv' ? 'selected' : ''; ?>>SUV</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Car Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small>Upload new image to replace existing one</small>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($edit_car['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" 
                               <?php echo ($edit_car['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        Active (Show on website)
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> <?php echo $edit_car ? 'Update Car' : 'Add Car'; ?>
                    </button>
                    <?php if ($edit_car): ?>
                        <a href="manage-cars.php" class="btn-reset">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="admin-table">
            <h2><i class="fas fa-list"></i> All Cars (<?php echo count($cars); ?>)</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Brand & Model</th>
                        <th>Year</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cars as $car): ?>
                        <tr>
                            <td><?php echo $car['id']; ?></td>
                            <td>
                                <?php if ($car['image_url']): ?>
                                    <img src="../<?php echo $car['image_url']; ?>" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <div style="width: 60px; height: 40px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                        <i class="fas fa-car" style="color: #999;"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($car['brand']); ?></strong><br>
                                <small><?php echo htmlspecialchars($car['model']); ?></small>
                            </td>
                            <td><?php echo $car['year']; ?></td>
                            <td>₹ <?php echo number_format($car['price'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo $car['category']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $car['category'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($car['is_active']): ?>
                                    <span class="status active"><i class="fas fa-check-circle"></i> Active</span>
                                <?php else: ?>
                                    <span class="status inactive"><i class="fas fa-times-circle"></i> Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?edit=<?php echo $car['id']; ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this car?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $car['id']; ?>">
                                    <button type="submit" class="btn-delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>