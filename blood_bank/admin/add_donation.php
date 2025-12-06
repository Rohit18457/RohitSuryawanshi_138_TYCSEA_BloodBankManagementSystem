<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
include '../config/db.php';

$message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donor_id = clean_input($_POST['donor_id']);
    $donation_date = clean_input($_POST['donation_date']);
    $units_donated = clean_input($_POST['units_donated']);
    
    // Call stored procedure
    $query = "CALL add_donation($donor_id, '$donation_date', $units_donated)";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $message = $row['message'];
        mysqli_free_result($result);
        mysqli_next_result($conn);
    } else {
        $error_message = mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Donation - Blood Bank</title>
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
                <h2>Add New Donation</h2>

                <?php if ($message): ?>
                    <div class="alert success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="donor_id">Select Donor *</label>
                            <select id="donor_id" name="donor_id" required onchange="showDonorInfo(this.value)">
                                <option value="">Select Donor</option>
                                <?php
                                $query = "SELECT * FROM donor_status_view ORDER BY name";
                                $result = execute_query($query);
                                
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $eligible = ($row['eligibility_status'] == 'Eligible') ? '✓' : '✗';
                                    echo "<option value='{$row['donor_id']}' 
                                          data-blood='{$row['blood_group']}' 
                                          data-eligible='{$row['eligibility_status']}'
                                          data-next='{$row['next_eligible_date']}'>
                                          {$eligible} {$row['name']} - {$row['blood_group']} ({$row['eligibility_status']})
                                          </option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div id="donor-info" style="display:none;" class="info-box">
                            <h4>Donor Information</h4>
                            <p><strong>Blood Group:</strong> <span id="info-blood"></span></p>
                            <p><strong>Eligibility:</strong> <span id="info-eligible"></span></p>
                            <p><strong>Next Eligible Date:</strong> <span id="info-next"></span></p>
                        </div>

                        <div class="form-group">
                            <label for="donation_date">Donation Date *</label>
                            <input type="date" id="donation_date" name="donation_date" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="units_donated">Units Donated *</label>
                            <input type="number" id="units_donated" name="units_donated" 
                                   min="1" max="2" value="1" required>
                        </div>

                        <button type="submit" class="btn-primary">Add Donation</button>
                    </form>
                </div>

                <div class="table-container">
                    <h3>Eligible Donors</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Donor ID</th>
                                <th>Name</th>
                                <th>Blood Group</th>
                                <th>Phone</th>
                                <th>Last Donation</th>
                                <th>Next Eligible Date</th>
                                <th>Total Donations</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM donor_status_view WHERE eligibility_status = 'Eligible' ORDER BY name";
                            $result = execute_query($query);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $last_donation = $row['last_donation_date'] ? date('d M Y', strtotime($row['last_donation_date'])) : 'Never';
                                    echo "<tr>
                                            <td>{$row['donor_id']}</td>
                                            <td>{$row['name']}</td>
                                            <td><strong>{$row['blood_group']}</strong></td>
                                            <td>{$row['phone']}</td>
                                            <td>{$last_donation}</td>
                                            <td>" . date('d M Y', strtotime($row['next_eligible_date'])) . "</td>
                                            <td>{$row['total_donations']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No eligible donors at the moment</td></tr>";
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

    <script>
    function showDonorInfo(donorId) {
        if (donorId) {
            var select = document.getElementById('donor_id');
            var option = select.options[select.selectedIndex];
            
            document.getElementById('info-blood').textContent = option.getAttribute('data-blood');
            document.getElementById('info-eligible').textContent = option.getAttribute('data-eligible');
            document.getElementById('info-next').textContent = option.getAttribute('data-next');
            document.getElementById('donor-info').style.display = 'block';
        } else {
            document.getElementById('donor-info').style.display = 'none';
        }
    }
    </script>
</body>
</html>
