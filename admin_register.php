<?php
include 'connection.php'; // Include your database connection file

// Start the session
session_start();

// Check if the admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit();
}

// Handle admin registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash the password with MD5

    // Check if the email or username already exists
    $sql = "SELECT * FROM admins WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email or username is already registered.";
    } else {
        // Insert the new admin into the database
        $sql = "INSERT INTO admins (username, name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $name, $email, $password);

        if ($stmt->execute()) {
            $success = "Admin registered successfully! You can now log in.";
        } else {
            $error = "Error occurred during registration. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="adminLogReg.css">
</head>
<body>
    <div class="container">
        <h2>Admin Registration</h2>
        <?php if (isset($error)) { ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>
        <?php if (isset($success)) { ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php } ?>
        <div class="form-box">
            <form method="post" action="">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" placeholder="Choose a username" required>

                <label for="name">Full Name:</label>
                <input type="text" name="name" id="name" placeholder="Enter your full name" required>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Choose a password" required>

                <button type="submit">Register</button>
            </form>
            <p>Already registered? <a href="admin_login.php">Login here</a>.</p>
        </div>
    </div>
</body>
</html>
