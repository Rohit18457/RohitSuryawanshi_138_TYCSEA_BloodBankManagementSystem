<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Management System</title>
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
            <section class="hero">
                <h2>Welcome to Blood Bank Management System</h2>
                <p>Save lives by donating blood. Every drop counts!</p>
            </section>

            <section class="stats">
                <h3>Current Blood Stock Status</h3>
                <div class="stock-grid">
                    <?php
                    $query = "SELECT * FROM blood_stock_status ORDER BY blood_group";
                    $result = execute_query($query);
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        $status_class = strtolower($row['stock_level']);
                        echo "<div class='stock-card {$status_class}'>
                                <h4>{$row['blood_group']}</h4>
                                <p class='units'>{$row['units_available']} Units</p>
                                <p class='status'>{$row['stock_level']}</p>
                              </div>";
                    }
                    ?>
                </div>
            </section>

            <section class="info">
                <div class="info-box">
                    <h3>Who Can Donate?</h3>
                    <ul>
                        <li>Age: 18-65 years</li>
                        <li>Weight: Minimum 50 kg</li>
                        <li>Good health condition</li>
                        <li>90 days gap between donations</li>
                    </ul>
                </div>

                <div class="info-box">
                    <h3>Benefits of Donating Blood</h3>
                    <ul>
                        <li>Saves up to 3 lives</li>
                        <li>Free health checkup</li>
                        <li>Reduces risk of heart disease</li>
                        <li>Stimulates blood cell production</li>
                    </ul>
                </div>

                <div class="info-box">
                    <h3>Quick Statistics</h3>
                    <?php
                    $total_donors = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as count FROM donor"))['count'];
                    $total_donations = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as count FROM donation"))['count'];
                    $pending_requests = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as count FROM request WHERE status='Pending'"))['count'];
                    ?>
                    <ul>
                        <li>Total Donors: <?php echo $total_donors; ?></li>
                        <li>Total Donations: <?php echo $total_donations; ?></li>
                        <li>Pending Requests: <?php echo $pending_requests; ?></li>
                    </ul>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Blood Bank Management System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
