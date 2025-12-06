<?php
include 'config/db.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_name = clean_input($_POST['patient_name']);
    $blood_group_needed = clean_input($_POST['blood_group_needed']);
    $units_required = clean_input($_POST['units_required']);
    $doctor_name = clean_input($_POST['doctor_name']);
    
    // Check available stock
    $stock_query = "SELECT units_available FROM blood_stock WHERE blood_group = '$blood_group_needed'";
    $stock_result = execute_query($stock_query);
    $stock_row = mysqli_fetch_assoc($stock_result);
    
    if ($units_required > 10) {
        $error_message = "Cannot request more than 10 units at once.";
    } else {
        $query = "INSERT INTO request (patient_name, blood_group_needed, units_required, doctor_name) 
                  VALUES ('$patient_name', '$blood_group_needed', $units_required, '$doctor_name')";
        
        if (mysqli_query($conn, $query)) {
            if ($stock_row['units_available'] >= $units_required) {
                $success_message = "Request submitted successfully! Available stock: " . $stock_row['units_available'] . " units.";
            } else {
                $success_message = "Request submitted but insufficient stock. Available: " . $stock_row['units_available'] . " units.";
            }
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
    <title>Blood Request - Blood Bank</title>
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
                <h2>Blood Request Form</h2>
                
                <?php if ($success_message): ?>
                    <div class="alert success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="patient_name">Patient Name *</label>
                        <input type="text" id="patient_name" name="patient_name" required>
                    </div>

                    <div class="form-group">
                        <label for="blood_group_needed">Blood Group Needed *</label>
                        <select id="blood_group_needed" name="blood_group_needed" required>
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
                        <label for="units_required">Units Required *</label>
                        <input type="number" id="units_required" name="units_required" min="1" max="10" required>
                    </div>

                    <div class="form-group">
                        <label for="doctor_name">Doctor Name *</label>
                        <input type="text" id="doctor_name" name="doctor_name" required>
                    </div>

                    <button type="submit" class="btn-primary">Submit Request</button>
                </form>

                <div class="info-section">
                    <h3>Current Blood Stock Availability</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Blood Group</th>
                                <th>Units Available</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM blood_stock_status ORDER BY blood_group";
                            $result = execute_query($query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td>{$row['blood_group']}</td>
                                        <td>{$row['units_available']}</td>
                                        <td class='status-{$row['stock_level']}'>{$row['stock_level']}</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Blood Bank Management System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
