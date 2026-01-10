<?php
require_once 'config/database.php';
$success = false;
$error_msg = "";
$display_name = "";
$display_email = "";
$display_phone = "";
$display_car_types = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();
    
    // Sanitize inputs
    $name = htmlspecialchars(strip_tags($_POST['name'] ?? ''));
    $phone = htmlspecialchars(strip_tags($_POST['phone'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $address = htmlspecialchars(strip_tags($_POST['address'] ?? ''));
    
    // Sanitize car_types array
    $raw_car_types = isset($_POST['car_types']) ? $_POST['car_types'] : [];
    $sanitized_car_types = [];
    if (is_array($raw_car_types)) {
        foreach ($raw_car_types as $type) {
            $sanitized_car_types[] = htmlspecialchars(strip_tags($type));
        }
    }
    $car_types_json = json_encode($sanitized_car_types);
    
    // Validate inputs
    if (empty($name) || empty($phone) || empty($email) || empty($address)) {
        $error_msg = "All fields are required!";
    } else {
        // Insert into database
        $query = "INSERT INTO customer_responses (name, phone, email, address, car_types) 
                  VALUES (:name, :phone, :email, :address, :car_types)";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":car_types", $car_types_json);
        
        if ($stmt->execute()) {
            $success = true;
            $display_name = $name;
            $display_email = $email;
            $display_phone = $phone;
            $display_car_types = implode(', ', $sanitized_car_types);
        } else {
            $error_msg = "Error submitting form. Please try again.";
        }
    }
} else {
    header("Location: form.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Status - CarsDekho</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="success-container">
        <div class="success-box">
            <?php if ($success): ?>
                <i class="fas fa-check-circle"></i>
                <h2>Thank You, <?php echo $display_name; ?>!</h2>
                <p>Your car preferences have been submitted successfully.</p>
                <p>We'll contact you at <strong><?php echo $display_email; ?></strong> shortly.</p>
                
                <div class="selected-cars">
                    <h3>Your Selections:</h3>
                    <p><?php echo $display_car_types ?: 'None selected'; ?></p>
                </div>
                
                <div style="margin-top: 30px;">
                    <a href="form.php" class="btn-back"><i class="fas fa-arrow-left"></i> Submit Another</a>
                    <a href="index.php" class="btn-home"><i class="fas fa-home"></i> Go to Homepage</a>
                </div>
            <?php else: ?>
                <div style="color: #d63031; font-size: 60px; margin-bottom: 20px;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h2>Submission Failed</h2>
                <p style="color: #d63031;"><?php echo $error_msg; ?></p>
                <a href="form.php" class="btn-back"><i class="fas fa-arrow-left"></i> Try Again</a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>