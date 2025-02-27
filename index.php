<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Reminder</title>
  <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="header">
    <div>Memori - Event Reminder</div>
    <div>
      <a href="login.php" style="color: white; text-decoration: none; margin-right: 10px;">Login</a>
      <a href="register.php" style="color: white; text-decoration: none;">Register</a>
    </div>
  </div>
  <div class="container">
    <div class="calendar">
      <div class="calendar-header">
        <select id="year"></select>
        <select id="month"></select>
        <div style="display:flex; justify-content:center; align-items: center; gap: 15px;">
        <button id="prevMonth">&lt;</button>
        <div id="currentMonthYear" style="font-size: 18px; font-weight: bold;"></div>
        <button id="nextMonth">&gt;</button>
        </div>
       
      </div>
      <div class="calendar-grid" id="calendarGrid">
        <!-- Weekdays -->
        <div class="weekday">Sun</div>
        <div class="weekday">Mon</div>
        <div class="weekday">Tue</div>
        <div class="weekday">Wed</div>
        <div class="weekday">Thu</div>
        <div class="weekday">Fri</div>
        <div class="weekday">Sat</div>
      </div>
      <button class="add-event-btn" onclick="addEventAlert()">Add Event</button>
    </div>
  </div>
  <script>
    const calendarGrid = document.getElementById('calendarGrid');
    const yearSelect = document.getElementById('year');
    const monthSelect = document.getElementById('month');
    const currentMonthYear = document.getElementById('currentMonthYear');
    const prevMonth = document.getElementById('prevMonth');
    const nextMonth = document.getElementById('nextMonth');

    const today = new Date();
    let currentYear = today.getFullYear();
    let currentMonth = today.getMonth();

    const renderCalendar = () => {
      // Clear old calendar days
      Array.from(calendarGrid.children)
        .slice(7) // Skip the first 7 weekday elements
        .forEach(child => calendarGrid.removeChild(child));

      const firstDay = new Date(currentYear, currentMonth, 1).getDay();
      const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

      currentMonthYear.textContent = `${monthSelect.options[currentMonth].text} ${currentYear}`;

      // Add empty cells for days of the previous month
      for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        calendarGrid.appendChild(emptyCell);
      }

      // Add days of the current month
      for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        dayCell.classList.add('day');
        dayCell.textContent = day;
        calendarGrid.appendChild(dayCell);
      }
    };

    const populateYearMonth = () => {
      for (let year = 1925; year <= 2100; year++) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearSelect.appendChild(option);
      }
      yearSelect.value = currentYear;

      const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
      ];
      months.forEach((month, index) => {
        const option = document.createElement('option');
        option.value = index;
        option.textContent = month;
        monthSelect.appendChild(option);
      });
      monthSelect.value = currentMonth;
    };

    const addEventAlert = () => {
      alert('Please register or log in to add an event.');
      location.href = 'login.php';
    };

    yearSelect.addEventListener('change', () => {
      currentYear = parseInt(yearSelect.value, 10);
      renderCalendar();
    });

    monthSelect.addEventListener('change', () => {
      currentMonth = parseInt(monthSelect.value, 10);
      renderCalendar();
    });

    prevMonth.addEventListener('click', () => {
      currentMonth--;
      if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
      }
      yearSelect.value = currentYear;
      monthSelect.value = currentMonth;
      renderCalendar();
    });

    nextMonth.addEventListener('click', () => {
      currentMonth++;
      if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
      }
      yearSelect.value = currentYear;
      monthSelect.value = currentMonth;
      renderCalendar();
    });

    populateYearMonth();
    renderCalendar();
  </script>
</body>
</html>
