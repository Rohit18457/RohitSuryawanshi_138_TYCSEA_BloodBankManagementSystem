<?php
include 'config/db.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $age = clean_input($_POST['age']);
    $gender = clean_input($_POST['gender']);
    $blood_group = clean_input($_POST['blood_group']);
    $phone = clean_input($_POST['phone']);
    $city = clean_input($_POST['city']);
    
    // Validate age
    if ($age < 18 || $age > 65) {
        $error_message = "Age must be between 18 and 65 years.";
    } else {
        $query = "INSERT INTO donor (name, age, gender, blood_group, phone, city) 
                  VALUES ('$name', $age, '$gender', '$blood_group', '$phone', '$city')";
        
        if (mysqli_query($conn, $query)) {
            $success_message = "Registration successful! Thank you for registering as a donor.";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Registration - Blood Bank</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🩸 Blood Bank Management System</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="donor_registration.php">Register as Donor</a>
                <a href="blood_request.php">Request Blood</a>
                <a href="blood_stock.php">Blood Stock</a>
                <a href="donor_list.php">Donor List</a>
                <a href="admin/login.php">Admin Login</a>
            </nav>
        </header>

        <main>
            <div class="form-container">
                <h2>Donor Registration Form</h2>
                
                <?php if ($success_message): ?>
                    <div class="alert success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="age">Age *</label>
                        <input type="number" id="age" name="age" min="18" max="65" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender *</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="blood_group">Blood Group *</label>
                        <select id="blood_group" name="blood_group" required>
                            <option value="">Select Blood Group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" pattern="[0-9]{10,15}" required>
                    </div>

                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" required>
                    </div>

                    <button type="submit" class="btn-primary">Register</button>
                </form>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Blood Bank Management System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
