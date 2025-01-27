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

$delete_success = false;

// Add Event
if (isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];

    // Insert event with the user_id
    $sql = "INSERT INTO events (event_name, event_date, user_id) VALUES ('$event_name', '$event_date', $user_id)";
    if ($conn->query($sql) === TRUE) {
        header("Location: home.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Delete Event
if (isset($_POST['delete_event'])) {
    $event_id = $_POST['event_id'];

    // Delete event only if it belongs to the logged-in user
    $sql = "DELETE FROM events WHERE id = $event_id AND user_id = $user_id";
    if ($conn->query($sql) === TRUE) {
        $delete_success = true;
    } else {
        echo "Error: " . $conn->error;
    }
}

// Edit Event
if (isset($_POST['edit_event'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];

    // Update event only if it belongs to the logged-in user
    $sql = "UPDATE events SET event_name = '$event_name', event_date = '$event_date' WHERE id = $event_id AND user_id = $user_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: home.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch events for the logged-in user
$sql = "SELECT * FROM events WHERE user_id = $user_id ORDER BY event_date";
$result = $conn->query($sql);

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
    <title>Memori - Event Reminder</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <header>
        <h1>Memori - Event Reminder</h1>
    </header>
    <nav>
        <a href="#">Home</a>
        <a href="#">Contact Us</a>
        <span>Welcome, <?php echo htmlspecialchars($user_name); ?>!</span>
        <form method="post" action="" style="display:flex;">
            <button type="submit" name="logout" style="background-color: #610000; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
                Logout
            </button>
        </form>
        <a href="register.php">Login/Signup</a>
    </nav>

    <div id="alertBox">
        <button onclick="this.parentElement.style.display='none';">OK</button>
    </div>

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
            <form class="event-form" method="post" action="home.php">
                <input type="text" name="event_name" placeholder="Event Name" autocomplete="off">
                <input type="hidden" id="event_date" name="event_date">
                <button type="submit" name="add_event">Add Event</button>
            </form>
            <div class="message">
                <?php
                if (isset($_POST['add_event'])) {
                    echo "Event added successfully!";
                }

                if (isset($_POST['delete_event'])) {
                    echo "Event deleted successfully!";
                }

                if (isset($_POST['edit_event'])) {
                    echo "Event updated successfully!";
                }
                ?>
            </div>
            <div class="event-list">
                <h3>Saved Events</h3>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $event_id = $row['id'];
                        echo "<div class='event-item' id='event-$event_id'>";
                        echo "<span class='event-name'>" . $row['event_name'] . " - " . $row['event_date'] . "</span>";
                        echo "<form method='post' action='home.php' id='edit-form-$event_id' style='display:none;'>";
                        echo "<input type='hidden' name='event_id' value='" . $row['id'] . "'>";
                        echo "<input type='text' name='event_name' value='" . $row['event_name'] . "' required>";
                        echo "<input type='date' name='event_date' value='" . $row['event_date'] . "' required>";
                        echo "<button type='submit' name='edit_event'>Save Changes</button>";
                        echo "</form>";
                        echo "<button type='button' onclick='editEvent($event_id)'>Edit</button>"; // Edit button
                        echo "<form method='post' action='home.php' style='display:inline;'>";
                        echo "<input type='hidden' name='event_id' value='" . $row['id'] . "'>";
                        echo "<button type='submit' name='delete_event'>Delete</button>";
                        echo "</form>";
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

            document.getElementById('yearSelect').onchange = function () {
                currentYear = parseInt(this.value);
                renderCalendar(currentYear, currentMonth);
            };

            document.getElementById('monthSelect').onchange = function () {
                currentMonth = parseInt(this.value);
                renderCalendar(currentYear, currentMonth);
            };

            document.querySelector('.event-form').onsubmit = function (e) {
                const eventName = document.querySelector('input[name="event_name"]').value.trim();
                const eventDate = document.querySelector('input[name="event_date"]').value.trim();

                if (!eventName || !eventDate) {
                    e.preventDefault();
                    let message = '';
                    if (!eventName && !eventDate) {
                        message += "Event name and Date not selected.";
                    } else {
                        if (!eventName) {
                            message += "Event name is not selected.";
                        }
                        if (!eventDate) {
                            message += "Event date is not selected.";
                        }
                    }
                    alert(message);
                }
            };
        });

        function populateYearDropdown() {
            const yearSelect = document.getElementById('yearSelect');
            const currentYear = new Date().getFullYear();
            const range = 100;

            for (let year = currentYear - range; year <= currentYear + range; year++) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                yearSelect.appendChild(option);
            }
        }

        function showAlert(message) {
            const alertBox = document.getElementById('alertBox');
            alertBox.textContent = message;
            alertBox.style.display = 'block';
        }

        document.addEventListener('DOMContentLoaded', populateYearDropdown);

        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($delete_success) { ?>
                alert("Event deleted successfully!");
                window.location.href = "home.php";
            <?php } ?>
        });

        function editEvent(eventId) {
            // Hide all edit forms and show the selected one
            document.querySelectorAll('.event-item form').forEach(form => form.style.display = 'none');
            document.getElementById('edit-form-' + eventId).style.display = 'block';

            // Hide the Edit button for the selected event
            const editButton = document.querySelector(`#event-${eventId} button[onclick="editEvent(${eventId})"]`);
            if (editButton) {
                editButton.style.display = 'none';
            }
        }
    </script>
</body>
</html>