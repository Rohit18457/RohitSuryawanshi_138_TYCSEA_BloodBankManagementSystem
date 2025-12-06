<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor List - Blood Bank</title>
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
                <h2>Registered Donors</h2>

                <div class="filter-section">
                    <form method="GET" action="">
                        <label for="blood_group_filter">Filter by Blood Group:</label>
                        <select id="blood_group_filter" name="blood_group" onchange="this.form.submit()">
                            <option value="">All Blood Groups</option>
                            <option value="A+" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                            <option value="AB+" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                            <option value="O+" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo (isset($_GET['blood_group']) && $_GET['blood_group'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                        </select>
                    </form>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Blood Group</th>
                                <th>City</th>
                                <th>Total Donations</th>
                                <th>Last Donation</th>
                                <th>Next Eligible Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $where_clause = "";
                            if (isset($_GET['blood_group']) && !empty($_GET['blood_group'])) {
                                $blood_group = clean_input($_GET['blood_group']);
                                $where_clause = "WHERE blood_group = '$blood_group'";
                            }
                            
                            $query = "SELECT * FROM donor_status_view $where_clause ORDER BY donor_id DESC";
                            $result = execute_query($query);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $status_class = ($row['eligibility_status'] == 'Eligible') ? 'eligible' : 'not-eligible';
                                    $last_donation = $row['last_donation_date'] ? date('d M Y', strtotime($row['last_donation_date'])) : 'Never';
                                    $next_date = date('d M Y', strtotime($row['next_eligible_date']));
                                    
                                    echo "<tr>
                                            <td>{$row['donor_id']}</td>
                                            <td>{$row['name']}</td>
                                            <td>{$row['age']}</td>
                                            <td>{$row['gender']}</td>
                                            <td><strong>{$row['blood_group']}</strong></td>
                                            <td>{$row['city']}</td>
                                            <td>{$row['total_donations']}</td>
                                            <td>{$last_donation}</td>
                                            <td>{$next_date}</td>
                                            <td><span class='badge badge-{$status_class}'>{$row['eligibility_status']}</span></td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10'>No donors found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="stats-section">
                    <h3>Donor Statistics</h3>
                    <div class="stats-grid">
                        <?php
                        $total_donors = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as count FROM donor"))['count'];
                        $eligible_donors = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as count FROM donor_status_view WHERE eligibility_status = 'Eligible'"))['count'];
                        $total_donations = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as count FROM donation"))['count'];
                        ?>
                        <div class="stat-card">
                            <h4><?php echo $total_donors; ?></h4>
                            <p>Total Donors</p>
                        </div>
                        <div class="stat-card">
                            <h4><?php echo $eligible_donors; ?></h4>
                            <p>Eligible Donors</p>
                        </div>
                        <div class="stat-card">
                            <h4><?php echo $total_donations; ?></h4>
                            <p>Total Donations</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Blood Bank Management System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
