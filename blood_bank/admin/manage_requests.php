<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
include '../config/db.php';

$message = '';

// Handle approve request
if (isset($_GET['approve'])) {
    $request_id = clean_input($_GET['approve']);
    $query = "CALL approve_request($request_id)";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $message = $row['message'];
        mysqli_free_result($result);
        mysqli_next_result($conn);
    }
}

// Handle reject request
if (isset($_GET['reject'])) {
    $request_id = clean_input($_GET['reject']);
    $query = "CALL reject_request($request_id)";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $message = $row['message'];
        mysqli_free_result($result);
        mysqli_next_result($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests - Blood Bank</title>
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
                <h2>Manage Blood Requests</h2>

                <?php if ($message): ?>
                    <div class="alert success"><?php echo $message; ?></div>
                <?php endif; ?>

                <h3>Pending Requests</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Patient Name</th>
                                <th>Blood Group</th>
                                <th>Units Required</th>
                                <th>Available Stock</th>
                                <th>Doctor</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM pending_requests_view";
                            $result = execute_query($query);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $can_fulfill = ($row['fulfillment_status'] == 'Can Fulfill');
                                    echo "<tr>
                                            <td>{$row['request_id']}</td>
                                            <td>{$row['patient_name']}</td>
                                            <td><strong>{$row['blood_group_needed']}</strong></td>
                                            <td>{$row['units_required']}</td>
                                            <td>{$row['units_available']}</td>
                                            <td>{$row['doctor_name']}</td>
                                            <td>" . date('d M Y H:i', strtotime($row['request_date'])) . "</td>
                                            <td><span class='badge badge-" . ($can_fulfill ? 'eligible' : 'not-eligible') . "'>{$row['fulfillment_status']}</span></td>
                                            <td>
                                                <a href='?approve={$row['request_id']}' class='btn-approve' onclick='return confirm(\"Approve this request?\")'>Approve</a>
                                                <a href='?reject={$row['request_id']}' class='btn-reject' onclick='return confirm(\"Reject this request?\")'>Reject</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No pending requests</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <h3>All Requests</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Patient Name</th>
                                <th>Blood Group</th>
                                <th>Units Required</th>
                                <th>Doctor</th>
                                <th>Request Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM request ORDER BY request_id DESC";
                            $result = execute_query($query);
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                $status_class = strtolower($row['status']);
                                echo "<tr>
                                        <td>{$row['request_id']}</td>
                                        <td>{$row['patient_name']}</td>
                                        <td><strong>{$row['blood_group_needed']}</strong></td>
                                        <td>{$row['units_required']}</td>
                                        <td>{$row['doctor_name']}</td>
                                        <td>" . date('d M Y H:i', strtotime($row['request_date'])) . "</td>
                                        <td><span class='badge badge-{$status_class}'>{$row['status']}</span></td>
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
