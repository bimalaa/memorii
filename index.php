<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Reminder</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #8B0000;
      color: white;
      margin: 0;
      padding: 0;
    }

    .header {
      text-align: center;
      padding: 10px 0;
      background-color: #600000;
      font-size: 24px;
      font-weight: bold;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      margin: 20px auto;
      padding: 20px;
      max-width: 1000px;
      background-color: #B22222;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .calendar {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .calendar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      padding: 10px;
      background-color: #600000;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .calendar-header select,
    .calendar-header button {
      background-color: #800000;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }

    .calendar-header button:hover {
      background-color: #A52A2A;
    }

    .calendar-header select {
      font-size: 14px;
    }

    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 5px;
      width: 100%;
      text-align: center;
      background-color: #600000;
      border-radius: 8px;
      padding: 10px;
    }

    .weekday {
      font-weight: bold;
      color: #FFD700;
      background-color: #800000;
      padding: 10px;
      border-radius: 4px;
    }

    .day {
      padding: 15px;
      font-size: 16px;
      background-color: #A52A2A;
      color: white;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .day:hover {
      background-color: #FF4500;
      color: black;
      transform: scale(1.1);
    }

    .add-event-btn {
      background-color: #FF4500;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 20px;
      align-self: center;
    }

    .add-event-btn:hover {
      background-color: #FF6347;
    }
  </style>
</head>
<body>
  <div class="header">Memori - Event Reminder</div>
  <div class="container">
    <div class="calendar">
      <div class="calendar-header">
        <select id="year"></select>
        <select id="month"></select>
        <button id="prevMonth">&lt;</button>
        <div id="currentMonthYear" style="font-size: 18px; font-weight: bold; color: #FFD700;"></div>
        <button id="nextMonth">&gt;</button>
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
      location.href = 'register.php';
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
