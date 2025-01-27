<?php
include 'connection.php'; // Include your database connection file

// Start the session
session_start();

// Check if the admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit();
}

// Handle admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash the password with MD5

    $sql = "SELECT * FROM admins WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="adminLogReg.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <?php if (isset($error)) { ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>
        <div class="form-box">
            <form method="post" action="">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>

                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="admin_register.php">Register here</a>.</p>
        </div>
    </div>
</body>
</html>
