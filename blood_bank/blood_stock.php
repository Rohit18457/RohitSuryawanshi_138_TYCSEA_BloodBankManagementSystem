<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Stock - Blood Bank</title>
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
            <div class="content-container">
                <h2>Blood Stock Status</h2>

                <div class="stock-grid">
                    <?php
                    $query = "SELECT * FROM blood_stock_status ORDER BY blood_group";
                    $result = execute_query($query);
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        $status_class = strtolower($row['stock_level']);
                        echo "<div class='stock-card {$status_class}'>
                                <h3>{$row['blood_group']}</h3>
                                <p class='units'>{$row['units_available']} Units</p>
                                <p class='status'>{$row['stock_level']}</p>
                                <p class='updated'>Updated: " . date('d M Y', strtotime($row['last_updated'])) . "</p>
                              </div>";
                    }
                    ?>
                </div>

                <div class="table-container">
                    <h3>Detailed Stock Information</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Blood Group</th>
                                <th>Units Available</th>
                                <th>Stock Level</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            mysqli_data_seek($result, 0);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td><strong>{$row['blood_group']}</strong></td>
                                        <td>{$row['units_available']}</td>
                                        <td><span class='badge badge-{$row['stock_level']}'>{$row['stock_level']}</span></td>
                                        <td>" . date('d M Y H:i', strtotime($row['last_updated'])) . "</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3>Donation Summary by Blood Group</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Blood Group</th>
                                <th>Total Donations</th>
                                <th>Total Units</th>
                                <th>Unique Donors</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM donation_summary_by_blood_group";
                            $result = execute_query($query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td><strong>{$row['blood_group']}</strong></td>
                                        <td>{$row['total_donations']}</td>
                                        <td>{$row['total_units_donated']}</td>
                                        <td>{$row['unique_donors']}</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="info-box">
                    <h3>Stock Level Indicators</h3>
                    <ul>
                        <li><span class="badge badge-critical">Critical</span> - Less than 10 units</li>
                        <li><span class="badge badge-low">Low</span> - 10-19 units</li>
                        <li><span class="badge badge-moderate">Moderate</span> - 20-29 units</li>
                        <li><span class="badge badge-good">Good</span> - 30+ units</li>
                    </ul>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Blood Bank Management System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
