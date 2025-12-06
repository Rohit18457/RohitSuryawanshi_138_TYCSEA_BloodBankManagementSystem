<?php
session_start();
include '../config/db.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = md5(clean_input($_POST['password']));
    
    $query = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result = execute_query($query);
    
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error_message = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Blood Bank</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🩸 Blood Bank Management System</h1>
            <nav>
                <a href="../index.php">Home</a>
                <a href="../donor_registration.php">Register as Donor</a>
                <a href="../blood_request.php">Request Blood</a>
                <a href="../blood_stock.php">Blood Stock</a>
                <a href="../donor_list.php">Donor List</a>
                <a href="login.php">Admin Login</a>
            </nav>
        </header>

        <main>
            <div class="login-container">
                <h2>Admin Login</h2>
                
                <?php if ($error_message): ?>
                    <div class="alert error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn-primary">Login</button>
                </form>

                <div class="info-box">
                    <h4>Default Admin Credentials:</h4>
                    <p>Username: admin</p>
                    <p>Password: admin123</p>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Blood Bank Management System. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
