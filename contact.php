<?php

include 'connection.php'; // Include your database connection file

// Start the session (if not already started)
session_start();


if (!isset($_COOKIE['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

// Get the user_id from the cookie
$user_id = $_COOKIE['user_id'];

$sql = "SELECT name FROM users WHERE id = $user_id"; // Assuming 'name' is a column in the 'users' table
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_name = $user['name'];
} else {
    $user_name = "Guest";
}

if (isset($_POST['logout'])) {
    // Clear the user cookie and destroy the session
    setcookie('user_id', '', time() - 3600, '/'); // Expire the cookie
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="home.css">
</head>
<body style="width: 100%; margin: 0px; padding: 0px; height: 100vh;">

    <div>
    <header>
        <h1>Memori - Event Reminder</h1>
    </header>
    <nav>
    <span>Welcome, <?php echo htmlspecialchars($user_name); ?>!</span>
        <a href="home.php">Home</a>
        <a href="#">Contact Us</a>
        
        <form method="post" action="" style="display:flex;">
            <button type="submit" name="logout" style="background-color: #80000b;color: white; border: none; padding: 0px 10px; border-radius: 5px; font-size:16px; cursor: pointer;">
                Logout
            </button>
        </form>
    </nav>
    </div>


    <div style="padding-top: 100px; padding-bottom:100; ">
    <h1 class="title">Contact Us</h1>
        <p class="title">9879976557</p>
        <p class="title">support@memori.com.np</p>
    </div>
    

    <div>
    <footer style="background-color: #80000b; color: white; text-align: center; padding: 30px; margin-top: 130px;" >
    <p>&copy; 2025 Memori - Event Reminder. All rights reserved.</p>
    <p>Quick Navigation:  
        <a href="home.php" style="color: rgb(216, 96, 106); text-decoration: none;">Home</a>, 
        <a href="contact.php" style="color: rgb(216, 96, 106); text-decoration: none;">Contact</a>
    </p>
    </footer>
    </div>
   
</body>
</html>