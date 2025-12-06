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
    <title>Reports - Blood Bank</title>
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
                <h2>Blood Bank Reports</h2>

                <div class="table-container">
                    <h3>Blood Stock Report</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Blood Group</th>
                                <th>Units Available</th>
                                <th>Stock Status</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "CALL get_stock_report()";
                            $result = mysqli_query($conn, $query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status_class = strtolower($row['stock_status']);
                                echo "<tr>
                                        <td><strong>{$row['blood_group']}</strong></td>
                                        <td>{$row['units_available']}</td>
                                        <td><span class='badge badge-{$status_class}'>{$row['stock_status']}</span></td>
                                        <td>" . date('d M Y H:i', strtotime($row['last_updated'])) . "</td>
                                      </tr>";
                            }
                            mysqli_free_result($result);
                            mysqli_next_result($conn);
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
                                <th>Total Units Donated</th>
                                <th>Unique Donors</th>
                                <th>Average Units per Donation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT *, 
                                     ROUND(total_units_donated / total_donations, 2) as avg_units 
                                     FROM donation_summary_by_blood_group";
                            $result = execute_query($query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td><strong>{$row['blood_group']}</strong></td>
                                        <td>{$row['total_donations']}</td>
                                        <td>{$row['total_units_donated']}</td>
                                        <td>{$row['unique_donors']}</td>
                                        <td>{$row['avg_units']}</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3>Request Status Summary</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Total Requests</th>
                                <th>Total Units Requested</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT status, COUNT(*) as total_requests, SUM(units_required) as total_units 
                                     FROM request GROUP BY status";
                            $result = execute_query($query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status_class = strtolower($row['status']);
                                echo "<tr>
                                        <td><span class='badge badge-{$status_class}'>{$row['status']}</span></td>
                                        <td>{$row['total_requests']}</td>
                                        <td>{$row['total_units']}</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3>Top 10 Donors by Donation Count</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Donor Name</th>
                                <th>Blood Group</th>
                                <th>Total Donations</th>
                                <th>Phone</th>
                                <th>City</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM donor_status_view 
                                     WHERE total_donations > 0 
                                     ORDER BY total_donations DESC LIMIT 10";
                            $result = execute_query($query);
                            
                            $rank = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td>{$rank}</td>
                                        <td>{$row['name']}</td>
                                        <td><strong>{$row['blood_group']}</strong></td>
                                        <td>{$row['total_donations']}</td>
                                        <td>{$row['phone']}</td>
                                        <td>{$row['city']}</td>
                                      </tr>";
                                $rank++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3>Donor Statistics by Blood Group</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Blood Group</th>
                                <th>Total Donors</th>
                                <th>Average Age</th>
                                <th>Eligible Donors</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT 
                                     d.blood_group,
                                     COUNT(*) as total_donors,
                                     ROUND(AVG(d.age), 1) as avg_age,
                                     SUM(CASE WHEN dsv.eligibility_status = 'Eligible' THEN 1 ELSE 0 END) as eligible_count
                                     FROM donor d
                                     LEFT JOIN donor_status_view dsv ON d.donor_id = dsv.donor_id
                                     GROUP BY d.blood_group
                                     ORDER BY d.blood_group";
                            $result = execute_query($query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td><strong>{$row['blood_group']}</strong></td>
                                        <td>{$row['total_donors']}</td>
                                        <td>{$row['avg_age']} years</td>
                                        <td>{$row['eligible_count']}</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3>Monthly Donation Statistics</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Donations</th>
                                <th>Total Units</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT 
                                     DATE_FORMAT(donation_date, '%Y-%m') as month,
                                     DATE_FORMAT(donation_date, '%M %Y') as month_name,
                                     COUNT(*) as total_donations,
                                     SUM(units_donated) as total_units
                                     FROM donation
                                     GROUP BY month
                                     ORDER BY month DESC
                                     LIMIT 12";
                            $result = execute_query($query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td>{$row['month_name']}</td>
                                        <td>{$row['total_donations']}</td>
                                        <td>{$row['total_units']}</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3>City-wise Donor Distribution</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>City</th>
                                <th>Total Donors</th>
                                <th>Total Donations</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT 
                                     d.city,
                                     COUNT(DISTINCT d.donor_id) as total_donors,
                                     COUNT(don.donation_id) as total_donations
                                     FROM donor d
                                     LEFT JOIN donation don ON d.donor_id = don.donor_id
                                     GROUP BY d.city
                                     ORDER BY total_donors DESC";
                            $result = execute_query($query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                        <td>{$row['city']}</td>
                                        <td>{$row['total_donors']}</td>
                                        <td>{$row['total_donations']}</td>
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
