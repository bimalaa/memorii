<?php
include 'connection.php'; // Include your database connection file

// Start the session (if not already started)
session_start();

// Initialize $user_id with a value if the user is logged in
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = null;  // No user logged in
}

// Fetch user name if logged in
if ($user_id !== null) {
    $sql = "SELECT name FROM users WHERE id = $user_id"; // Assuming 'name' is a column in the 'users' table
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_name = $user['name'];
    } else {
        $user_name = "Guest";
    }
} else {
    $user_name = "Guest";
}

// Logout functionality
if (isset($_POST['logout'])) {
    // Clear the user cookie and destroy the session
    setcookie('user_id', '', time() - 3600, '/'); // Expire the cookie
    session_destroy();
    header("Location: login.php");
    exit();
}

// Fetch events for the logged-in user
if ($user_id !== null) {
    $sql = "SELECT * FROM events WHERE user_id = $user_id ORDER BY event_date";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memori - Event Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #8B0000;
            color: white;
        }

        header {
            background-color: #610000;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #610000;
            padding: 10px 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .container {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #610000;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        .calendar-container {
            flex: 2;
            background-color: #B22222;
            padding: 20px;
            border-radius: 5px;
        }

        .event-form-container {
            flex: 1;
            margin-left: 20px;
            background-color: #610000;
            padding: 20px;
            border-radius: 5px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header select {
            background-color: #610000;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            color: white;
        }

        .calendar div {
            background-color: #610000;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }

        .calendar div.header {
            font-weight: bold;
        }

        .calendar div.selected {
            background-color: #8B0000;
            color: white;
        }

        form input, form button {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            border: none;
            border-radius: 5px;
        }

        form button {
            background-color: #B22222;
            color: white;
            cursor: pointer;
        }

        .message {
            color: #FFD700;
            text-align: center;
        }

        .event-list {
            margin-top: 20px;
        }

        .event-item {
            background-color: #B22222;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .event-item button {
            background-color: #610000;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        #alertBox {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #FFD700;
            color: #610000;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        #alertBox button {
            margin-top: 10px;
            padding: 10px;
            background-color: #610000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Memori - Event Reminder</h1>
    </header>
    <nav>
        <a href="#">Home</a>
        <a href="#">Contact Us</a>
        <span>Welcome, <?php echo htmlspecialchars($user_name); ?>!</span>
        <?php if ($user_id !== null): ?>
            <form method="post" action="" style="display: inline;">
                <button type="submit" name="logout" style="background-color: #610000; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
                    Logout
                </button>
            </form>
        <?php else: ?>
            <a href="login.php">Login/Signup</a>
        <?php endif; ?>
    </nav>

    <div class="container">
        <div class="calendar-container">
            <div class="calendar-header">
                <select id="yearSelect"></select>
                <select id="monthSelect">
                    <option value="0">January</option>
                    <option value="1">February</option>
                    <option value="2">March</option>
                    <option value="3">April</option>
                    <option value="4">May</option>
                    <option value="5">June</option>
                    <option value="6">July</option>
                    <option value="7">August</option>
                    <option value="8">September</option>
                    <option value="9">October</option>
                    <option value="10">November</option>
                    <option value="11">December</option>
                </select>
                <button type="button" id="prevMonth">&lt;</button>
                <span id="currentMonth"></span>
                <button type="button" id="nextMonth">&gt;</button>
            </div>
            <div class="calendar"></div>
        </div>

        <div class="event-form-container">
            <?php if ($user_id !== null): ?>
                <form class="event-form" method="post" action="index.php">
                    <input type="text" name="event_name" placeholder="Event Name" autocomplete="off">
                    <input type="hidden" id="event_date" name="event_date">
                    <button type="submit" name="add_event">Add Event</button>
                </form>
            <?php else: ?>
                <button onclick="window.location.href='login.php'" style="background-color: #B22222; color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; cursor: pointer;">
                    Login to Add Event
                </button>
            <?php endif; ?>

            <div class="event-list">
                <h3>Saved Events</h3>
                <?php
                if ($user_id !== null && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $event_id = $row['id'];
                        echo "<div class='event-item' id='event-$event_id'>";
                        echo "<span>" . $row['event_name'] . " - " . $row['event_date'] . "</span>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No events found.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // JavaScript for Calendar Rendering
        function renderCalendar(year, month) {
            const calendar = document.querySelector('.calendar');
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            document.querySelector('#currentMonth').textContent = `${monthNames[month]} ${year}`;
            document.querySelector('#yearSelect').value = year;
            document.querySelector('#monthSelect').value = month;

            calendar.innerHTML = '';

            const firstDay = new Date(year, month, 1).getDay();
            const lastDate = new Date(year, month + 1, 0).getDate();

            const dayHeaders = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
            dayHeaders.forEach(day => {
                const dayDiv = document.createElement('div');
                dayDiv.textContent = day;
                dayDiv.classList.add('header');
                calendar.appendChild(dayDiv);
            });

            for (let i = 0; i < firstDay; i++) {
                const emptyDiv = document.createElement('div');
                calendar.appendChild(emptyDiv);
            }

            for (let day = 1; day <= lastDate; day++) {
                const dayDiv = document.createElement('div');
                dayDiv.textContent = day;
                dayDiv.onclick = function () {
                    document.querySelectorAll('.calendar div').forEach(div => div.classList.remove('selected'));
                    dayDiv.classList.add('selected');
                    document.getElementById('event_date').value = `${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                };
                calendar.appendChild(dayDiv);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const today = new Date();
            let currentYear = today.getFullYear();
            let currentMonth = today.getMonth();

            renderCalendar(currentYear, currentMonth);

            document.getElementById('prevMonth').onclick = function () {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                renderCalendar(currentYear, currentMonth);
            };

            document.getElementById('nextMonth').onclick = function () {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                renderCalendar(currentYear, currentMonth);
            };
        });
    </script>
</body>
</html>