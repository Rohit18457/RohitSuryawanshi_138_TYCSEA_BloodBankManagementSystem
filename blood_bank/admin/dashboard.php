<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
include '../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blood Bank</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🩸 Admin Dashboard</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="manage_requests.php">Manage Requests</a>
                <a href="add_donation.php">Add Donation</a>
                <a href="donor_history.php">Donor History</a>
                <a href="reports.php">Reports</a>
                <a href="logout.php">Logout (<?php echo $_SESSION['admin_username']; ?>)</a>
            </nav>
        </header>

        <main>
            <div class="content-container">
                <h2>Welcome, <?php echo $_SESSION['admin_username']; ?>!</h2>

                <div class="stats-grid">
                    <?php
                    $total_donors = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as count FROM donor"))['count'];
                    $total_donations = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as count FROM donation"))['count'];
                    $pending_requests = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as count FROM request WHERE status='Pending'"))['count'];
                    $total_units = mysqli_fetch_assoc(execute_query("SELECT SUM(units_available) as total FROM blood_stock"))['total'];
                    ?>
                    <div class="stat-card">
                        <h3><?php echo $total_donors; ?></h3>
                        <p>Total Donors</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $total_donations; ?></h3>
                        <p>Total Donations</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $pending_requests; ?></h3>
                        <p>Pending Requests</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $total_units; ?></h3>
                        <p>Total Blood Units</p>
                    </div>
                </div>

                <div class="table-container">
                    <h3>Blood Stock Status</h3>
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
                            $query = "SELECT * FROM blood_stock_status ORDER BY blood_group";
                            $result = execute_query($query);
                            
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
                    <h3>Recent Donations (Last 30 Days)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Donation ID</th>
                                <th>Donor Name</th>
                                <th>Blood Group</th>
                                <th>Phone</th>
                                <th>Donation Date</th>
                                <th>Units</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM recent_donations LIMIT 10";
                            $result = execute_query($query);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                            <td>{$row['donation_id']}</td>
                                            <td>{$row['donor_name']}</td>
                                            <td><strong>{$row['blood_group']}</strong></td>
                                            <td>{$row['phone']}</td>
                                            <td>" . date('d M Y', strtotime($row['donation_date'])) . "</td>
                                            <td>{$row['units_donated']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No recent donations</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3>Pending Blood Requests</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Patient Name</th>
                                <th>Blood Group</th>
                                <th>Units Required</th>
                                <th>Doctor</th>
                                <th>Request Date</th>
                                <th>Fulfillment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM pending_requests_view LIMIT 10";
                            $result = execute_query($query);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $status_class = ($row['fulfillment_status'] == 'Can Fulfill') ? 'eligible' : 'not-eligible';
                                    echo "<tr>
                                            <td>{$row['request_id']}</td>
                                            <td>{$row['patient_name']}</td>
                                            <td><strong>{$row['blood_group_needed']}</strong></td>
                                            <td>{$row['units_required']}</td>
                                            <td>{$row['doctor_name']}</td>
                                            <td>" . date('d M Y', strtotime($row['request_date'])) . "</td>
                                            <td><span class='badge badge-{$status_class}'>{$row['fulfillment_status']}</span></td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No pending requests</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <a href="manage_requests.php" class="btn-primary">Manage All Requests</a>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Blood Bank Management System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
