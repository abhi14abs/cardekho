<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if POST max size exceeded
    if (empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $max_size = ini_get('post_max_size');
        header("Location: manage-content.php?msg=error&error=File too large. Maximum allowed size is $max_size");
        exit();
    }

    $action = $_POST['action'] ?? '';
    
    if ($action == 'delete' && isset($_POST['id'])) {
        $query = "DELETE FROM website_content WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $_POST['id']);
        $stmt->execute();
        header('Location: manage-content.php?msg=deleted');
        exit();
    } else {
        $id = $_POST['id'] ?? 0;
        $section = $_POST['section'];
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $link_url = $_POST['link_url'] ?? '';
        $display_order = $_POST['display_order'] ?? 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Handle image upload
        $image_url = $_POST['existing_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../uploads/content/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = 'uploads/content/' . $file_name;
            }
        }
        
        if ($id > 0) {
            // Update
            $query = "UPDATE website_content SET section = :section, title = :title, 
                     content = :content, image_url = :image_url, link_url = :link_url,
                     display_order = :display_order, is_active = :is_active, 
                     updated_at = NOW() WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
        } else {
            // Insert
            $query = "INSERT INTO website_content (section, title, content, image_url, 
                     link_url, display_order, is_active) 
                     VALUES (:section, :title, :content, :image_url, :link_url, 
                     :display_order, :is_active)";
            $stmt = $db->prepare($query);
        }
        
        $stmt->bindParam(':section', $section);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->bindParam(':link_url', $link_url);
        $stmt->bindParam(':display_order', $display_order);
        $stmt->bindParam(':is_active', $is_active);
        
        $stmt->execute();
        
        $msg = $id > 0 ? 'updated' : 'added';
        header("Location: manage-content.php?msg=$msg");
        exit();
    }
}

// Fetch all content
$query = "SELECT * FROM website_content ORDER BY section, display_order";
$stmt = $db->prepare($query);
$stmt->execute();
$contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get content for editing
$edit_content = null;
if (isset($_GET['edit'])) {
    $query = "SELECT * FROM website_content WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['edit']);
    $stmt->execute();
    $edit_content = $stmt->fetch(PDO::FETCH_ASSOC);
}

$sections = ['header', 'banner', 'footer', 'cta', 'sidebar'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Content - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .section-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .section-header { background: #1a237e; color: white; }
        .section-banner { background: #ff9800; color: white; }
        .section-footer { background: #4caf50; color: white; }
        .section-cta { background: #9c27b0; color: white; }
        .section-sidebar { background: #607d8b; color: white; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'admin-header.php'; ?>
        
        <div class="admin-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage-content.php" class="active"><i class="fas fa-edit"></i> Manage Content</a></li>
                <li><a href="manage-cars.php"><i class="fas fa-car"></i> Manage Cars</a></li>
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
                <div class="message success">
                    <i class="fas fa-check-circle"></i> 
                    Content <?php echo $_GET['msg']; ?> successfully!
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="form-container">
            <h2><i class="fas fa-edit"></i> <?php echo $edit_content ? 'Edit Content' : 'Add New Content'; ?></h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $edit_content['id'] ?? 0; ?>">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="section">Section *</label>
                        <select id="section" name="section" required>
                            <option value="">Select Section</option>
                            <?php foreach ($sections as $section): ?>
                                <option value="<?php echo $section; ?>" 
                                    <?php echo ($edit_content['section'] ?? '') == $section ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($section); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" id="display_order" name="display_order" 
                               value="<?php echo htmlspecialchars($edit_content['display_order'] ?? 0); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" 
                           value="<?php echo htmlspecialchars($edit_content['title'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="4"><?php echo htmlspecialchars($edit_content['content'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="link_url">Link URL</label>
                    <input type="url" id="link_url" name="link_url" 
                           value="<?php echo htmlspecialchars($edit_content['link_url'] ?? ''); ?>">
                </div>
                
                <?php if ($edit_content && $edit_content['image_url']): ?>
                    <input type="hidden" name="existing_image" value="<?php echo $edit_content['image_url']; ?>">
                    <div class="form-group">
                        <label>Current Image:</label>
                        <img src="../<?php echo $edit_content['image_url']; ?>" 
                             style="max-width: 200px; display: block; border-radius: 5px; margin: 5px 0;">
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small>Leave empty to keep current image</small>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" 
                               <?php echo ($edit_content['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        Active (Show on website)
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> <?php echo $edit_content ? 'Update' : 'Add Content'; ?>
                    </button>
                    <?php if ($edit_content): ?>
                        <a href="manage-content.php" class="btn-reset">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="admin-table">
            <h2><i class="fas fa-list"></i> Website Content (<?php echo count($contents); ?>)</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Section</th>
                        <th>Title</th>
                        <th>Content Preview</th>
                        <th>Image</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contents as $content): ?>
                        <tr>
                            <td><?php echo $content['id']; ?></td>
                            <td>
                                <span class="section-badge section-<?php echo $content['section']; ?>">
                                    <?php echo $content['section']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(substr($content['title'] ?? '', 0, 30)); ?></td>
                            <td><?php echo htmlspecialchars(substr($content['content'] ?? '', 0, 50)) . '...'; ?></td>
                            <td>
                                <?php if ($content['image_url']): ?>
                                    <img src="../<?php echo $content['image_url']; ?>" 
                                         style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $content['display_order']; ?></td>
                            <td>
                                <?php if ($content['is_active']): ?>
                                    <span class="status active"><i class="fas fa-check-circle"></i> Active</span>
                                <?php else: ?>
                                    <span class="status inactive"><i class="fas fa-times-circle"></i> Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?edit=<?php echo $content['id']; ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this content?');">
                                    <input type="hidden" name="id" value="<?php echo $content['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
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