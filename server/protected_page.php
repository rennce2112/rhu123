<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php?error=login_required");
    exit();
}

// Get the username from the query parameter
$username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : 'Guest';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Calendar</title>
    <link rel="stylesheet" href="../navigation.css">
    <link rel="stylesheet" href="css/calendar.css">
    <style>
        form {
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            font-size:large;
            width: 60%;
        }
        .gap {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 80px;
        }


        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .available {
            background-color: #b3e0ff;
            cursor: pointer;
        }
        .reserved-one {
            background-color: #FFE4B5;
            cursor: pointer;
        }
        .reserved-two {
            background-color: #FFA500;
            cursor: pointer;
        }
        .reserved {
            background-color: #ff4d4d;
            cursor: not-allowed;
        }
        .lunch-time {
            background-color: #cccccc;
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .week-navigation {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        #reservation-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        #reservation-form input,
        #reservation-form select {
            padding: 8px;
            margin: 0 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        #reservation-form label {
            margin-right: 5px;
        }
        .past-date {
    background-color: #e0e0e0 !important;
    color: #999;
    cursor: not-allowed;
}

.calendar-header th.past-date {
    background-color: #cccccc;
    color: #666;
}

td[data-time] {
    position: relative;
    height: 30px;
    transition: background-color 0.3s;
    text-align: center;
}

td[data-time]:hover:not(.past-date):not(.lunch-time):not(.reserved) {
    background-color: #80ccff !important;
    cursor: pointer;
}

.selected {
    background-color: #4CAF50 !important;
    color: white;
}

.duration-preview {
    background-color: #81C784 !important;
    color: white;
}

.reserved {
    background-color: #ff4d4d !important;
    color: white;
    cursor: not-allowed;
}

.partially-reserved {
    background-color: #ffb84d !important;
    cursor: pointer;
}

.lunch-time {
    background-color: #cccccc !important;
    color: #666;
    cursor: not-allowed;
}
.legend {
    display: flex;
    gap: 20px;
    margin-top: 20px;
    padding: 10px;
    background: #f5f5f5;
    border-radius: 4px;
  }
  
  .legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .legend-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
  }
  
  .legend-color.available {
    background-color: #4CAF50; /* Vibrant green */
  }
  
  .legend-color.booked {
    background-color: #f44336; /* Vibrant red */
  }
  
  .legend-color.pending {
    background-color: #FF9800; /* Vibrant orange */
  }
  
  .legend-color.partially {
    background-color: #FFC107; /* Vibrant yellow */
  }
  
    </style>
    <script src="js/calendar.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container container">
            <input type="checkbox" name="" id="">
            <div class="hamburger-lines">
                <span class="line line1"></span>
                <span class="line line2"></span>
                <span class="line line3"></span>
            </div>
            <ul class="menu-items">
                <li><a href="../index.php#page1">Home</a></li>
                <li><a href="../index.php#page2">Callaborators</a></li>
                <li><a href="../index.php#page3">Category</a></li> 
                <li><a href="../html.php">log-in</a></li>
            </ul>
            <h1 class="logo">HEALTH</h1>
        </div>
    </nav>
    <div class="gap">
        <h1>Reservation System</h1>
        
        <form id="reservation-form">
    <label for="Date">Date:</label>
    <input type="date" id="Date" required>

    <label for="Duration">Duration:</label>
    <select id="Duration" required>
        <option value="20">20 minutes</option>
        <option value="40">40 minutes</option>
        <option value="60">1 hour</option>
    </select>

    <input type="hidden" id="Time">
    <a href="../login.php">log-out</a>
    <button type="submit">Reserve</button>
</form>

<div class="legend">
            <div class="legend-item">
                <div class="legend-color available"></div>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <div class="legend-color booked"></div>
                <span>Booked</span>
            </div>
            <div class="legend-item">
                <div class="legend-color pending"></div>
                <span>Pending</span>
            </div>
            <div class="legend-item">
                <div class="legend-color partially"></div>
                <span>Partially Booked</span>
            </div>
        </div>


        <div class="week-navigation">
            <button id="prev-week">← Previous Week</button>
            <button id="next-week">Next Week →</button>
        </div>

        <!-- Calendar Table -->
        <table id="calendar">
            <thead id="calendar-header">
                <tr>
                    <th>Time</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                    <th>Sunday</th>
                </tr>
            </thead>
            <tbody id="calendar-body"></tbody>
        </table>
    </div>
</body>
</html>