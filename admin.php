<?php
include 'connection.php'; 

// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Add new admin
if (isset($_POST['add_admin'])) {
    $admin_name = $_POST['admin_name'];
    $admin_username = $_POST['admin_username'];
    $admin_email = $_POST['admin_email'];
    $admin_password = md5($_POST['admin_password']);

    $sql = "INSERT INTO admins (name, username, email, password) VALUES ('$admin_name', '$admin_username', '$admin_email', '$admin_password')";
    if ($conn->query($sql) === TRUE) {
        $message = "Admin added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Delete user
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Delete user events
    $conn->query("DELETE FROM events WHERE user_id = $user_id");

    // Delete user
    $sql = "DELETE FROM users WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        $message = "User deleted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch all users
$sql = "SELECT * FROM users ORDER BY id";
$users = $conn->query($sql);

// Fetch events for a specific user
if (isset($_GET['view_events'])) {
    $selected_user_id = $_GET['view_events'];
    $event_query = "SELECT * FROM events WHERE user_id = $selected_user_id ORDER BY event_date";
    $user_events = $conn->query($event_query);
}

if (isset($_POST['logout'])) {
    // Destroy the session and logout
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="admin.php">Users</a></li>
            <li><a href="admin.php?action=add_admin">Add Admin</a></li>
            <form method="post" action="" style="display:inline;">
                <button type="submit" name="logout" style="background:none; border:none; color:blue; text-decoration:underline; cursor:pointer;">Logout</button>
            </form>
        </ul>
    </div>

    <div class="content">
        <?php if (isset($message)) { ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php } ?>

        <?php if (isset($_GET['action']) && $_GET['action'] === 'add_admin') { ?>
            <div class="modal" id="addAdminModal">
        <div class="modal-content">
            <h1>Add Admin</h1>
            <form method="post" action="">
                <input type="text" name="admin_name" placeholder="Admin Name" required>
                <input type="text" name="admin_username" placeholder="Admin Username" required>
                <input type="email" name="admin_email" placeholder="Admin Email" required>
                <input type="password" name="admin_password" placeholder="Admin Password" required>
                <button type="submit" name="add_admin">Add Admin</button>
            </form>
        </div>
    </div>
        <?php } else { ?>
            <h1>Users</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <a href="?view_events=<?php echo $user['id']; ?>">View Events</a>
                                <form method="post" action="" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete_user" style="background:none; border:none; color:red; text-decoration:underline; cursor:pointer;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <?php if (isset($user_events)) { ?>
                <h2>Events for User ID: <?php echo $selected_user_id; ?></h2>
                <ul>
                    <?php while ($event = $user_events->fetch_assoc()) { ?>
                        <li><?php echo htmlspecialchars($event['event_name']) . " - " . htmlspecialchars($event['event_date']); ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        <?php } ?>
    </div>
</body>
</html>
