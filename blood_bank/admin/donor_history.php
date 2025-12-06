<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
include '../config/db.php';

$donor_history = null;
$donor_info = null;

if (isset($_GET['donor_id'])) {
    $donor_id = clean_input($_GET['donor_id']);
    
    // Get donor info
    $query = "SELECT * FROM donor_status_view WHERE donor_id = $donor_id";
    $result = execute_query($query);
    $donor_info = mysqli_fetch_assoc($result);
    
    // Get donation history using stored procedure
    $query = "CALL get_donor_history($donor_id)";
    $donor_history = mysqli_query($conn, $query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor History - Blood Bank</title>
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
                <h2>Donor History</h2>

                <div class="form-container">
                    <form method="GET" action="">
                        <div class="form-group">
                            <label for="donor_id">Select Donor</label>
                            <select id="donor_id" name="donor_id" onchange="this.form.submit()">
                                <option value="">Select Donor</option>
                                <?php
                                $query = "SELECT donor_id, name, blood_group FROM donor ORDER BY name";
                                $result = execute_query($query);
                                
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $selected = (isset($_GET['donor_id']) && $_GET['donor_id'] == $row['donor_id']) ? 'selected' : '';
                                    echo "<option value='{$row['donor_id']}' $selected>
                                          {$row['name']} - {$row['blood_group']}
                                          </option>";
                                }
                                ?>
                            </select>
                        </div>
                    </form>
                </div>

                <?php if ($donor_info): ?>
                <div class="donor-details">
                    <h3>Donor Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Name:</strong> <?php echo $donor_info['name']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Age:</strong> <?php echo $donor_info['age']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Gender:</strong> <?php echo $donor_info['gender']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Blood Group:</strong> <span class="blood-group"><?php echo $donor_info['blood_group']; ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Phone:</strong> <?php echo $donor_info['phone']; ?>
                        </div>
                        <div class="info-item">
                            <strong>City:</strong> <?php echo $donor_info['city']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Total Donations:</strong> <?php echo $donor_info['total_donations']; ?>
                        </div>
                        <div class="info-item">
                            <strong>Last Donation:</strong> 
                            <?php echo $donor_info['last_donation_date'] ? date('d M Y', strtotime($donor_info['last_donation_date'])) : 'Never'; ?>
                        </div>
                        <div class="info-item">
                            <strong>Next Eligible Date:</strong> 
                            <?php echo date('d M Y', strtotime($donor_info['next_eligible_date'])); ?>
                        </div>
                        <div class="info-item">
                            <strong>Eligibility Status:</strong> 
                            <span class="badge badge-<?php echo ($donor_info['eligibility_status'] == 'Eligible') ? 'eligible' : 'not-eligible'; ?>">
                                <?php echo $donor_info['eligibility_status']; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <h3>Donation History</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Donation ID</th>
                                <th>Donation Date</th>
                                <th>Units Donated</th>
                                <th>Days Since Donation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($donor_history && mysqli_num_rows($donor_history) > 0) {
                                while ($row = mysqli_fetch_assoc($donor_history)) {
                                    $days_since = floor((strtotime(date('Y-m-d')) - strtotime($row['donation_date'])) / (60 * 60 * 24));
                                    echo "<tr>
                                            <td>{$row['donation_id']}</td>
                                            <td>" . date('d M Y', strtotime($row['donation_date'])) . "</td>
                                            <td>{$row['units_donated']}</td>
                                            <td>{$days_since} days ago</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No donation history found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <div class="table-container">
                    <h3>All Donors Summary</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Donor ID</th>
                                <th>Name</th>
                                <th>Blood Group</th>
                                <th>Total Donations</th>
                                <th>Last Donation</th>
                                <th>Eligibility</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM donor_status_view ORDER BY total_donations DESC";
                            $result = execute_query($query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                $last_donation = $row['last_donation_date'] ? date('d M Y', strtotime($row['last_donation_date'])) : 'Never';
                                $status_class = ($row['eligibility_status'] == 'Eligible') ? 'eligible' : 'not-eligible';
                                
                                echo "<tr>
                                        <td>{$row['donor_id']}</td>
                                        <td>{$row['name']}</td>
                                        <td><strong>{$row['blood_group']}</strong></td>
                                        <td>{$row['total_donations']}</td>
                                        <td>{$last_donation}</td>
                                        <td><span class='badge badge-{$status_class}'>{$row['eligibility_status']}</span></td>
                                        <td><a href='?donor_id={$row['donor_id']}' class='btn-view'>View History</a></td>
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
