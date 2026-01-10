<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Selection Form - CarsDekho</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="form-container">
            <h1><i class="fas fa-car"></i> Select Your Preferred Car</h1>
            <p class="subtitle">Choose one or multiple car types that interest you</p>
            
            <form action="submit.php" method="POST" id="carForm">
                <div class="form-group">
                    <label for="name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your full name">
                </div>
                
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email address">
                </div>
                
                <div class="form-group">
                    <label for="address"><i class="fas fa-home"></i> Address</label>
                    <textarea id="address" name="address" rows="3" required placeholder="Enter your complete address"></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-car-side"></i> Preferred Car Types</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="hatchback" name="car_types[]" value="Hatchback">
                            <label for="hatchback">
                                <i class="fas fa-car-side"></i>
                                <span>Hatchback</span>
                                <p class="car-desc">Compact & Efficient</p>
                            </label>
                        </div>
                        
                        <div class="checkbox-item">
                            <input type="checkbox" id="sadan" name="car_types[]" value="Sadan">
                            <label for="sadan">
                                <i class="fas fa-car"></i>
                                <span>Sedan</span>
                                <p class="car-desc">Comfort & Style</p>
                            </label>
                        </div>
                        
                        <div class="checkbox-item">
                            <input type="checkbox" id="suv" name="car_types[]" value="SUV">
                            <label for="suv">
                                <i class="fas fa-truck-pickup"></i>
                                <span>SUV</span>
                                <p class="car-desc">Power & Space</p>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit Preferences
                    </button>
                    <button type="reset" class="btn-reset">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>