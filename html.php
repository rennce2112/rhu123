<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Responsive Navigation Bar</title>
    <link rel="stylesheet" href="navigation.css" />
    <style>
        form {
    margin: 80px auto;
    width: 50%;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
}

form div {
    margin-bottom: 10px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input[type="text"],
input[type="tel"],
input[type="email"],
input[type="date"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

input[type="submit"] {
    background-color: #ff9900;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

input[type="submit"]:hover {
    background-color: #e68a00;
}

fieldset {
    border: 1px solid #ccc;
    padding: 10px;
}

legend {
    font-weight: bold;
}

/* Media Queries for Mobile */
@media (max-width: 768px) {
    form {
        width: 90%;
    }

    .menu-items {
        display: none;
        flex-direction: column;
    }

    .hamburger-lines {
        display: block;
    }

    .navbar input:checked + .hamburger-lines + .menu-items {
        display: flex;
    }

    .menu-items li {
        margin: 10px 0;
    }
}


input[type="submit"]:hover {
    background-color: #e68a00; /* Darker shade on hover */
}

    </style>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#page2">Callaborators</a></li>
                <li><a href="index.php#page9">Category</a></li> 
                <li><a href="#">Register</a></li>
            </ul>
            <h1 class="logo">HEALTH</h1>
        </div>
    </nav>
    <div class="raper">
    
        <div class="water-effect" onclick="showRipple(event)">
        <form action="server/upload.php" method="POST">
            <div>
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div>
                <label for="middle_name">Middle Name</label>
                <input type="text" id="middle_name" name="middle_name" required>
            </div>
            <div>
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div>
                <label for="birthday">Birthday:</label>
                <input type="date" id="birthday" name="birthday" required>
            </div>
            <div>
                <fieldset>
                    <legend>Gender</legend>
                    <label>
                        <input type="radio" name="gender" value="male" checked> Male
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="gender" value="female"> Female
                    </label>
                    <br>
                </fieldset>
            </div>
            <div>
                <label for="contactNumber">Contact Number</label>
                <input type="tel" id="contactNumber" name="contact_number" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label class="form-label">Region *</label>
                <select name="region" class="form-control form-control-md" id="region"></select>
                <input type="hidden" class="form-control form-control-md" name="region_text" id="region-text" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label class="form-label">Province *</label>
                <select name="province" class="form-control form-control-md" id="province"></select>
                <input type="hidden" class="form-control form-control-md" name="province_text" id="province-text" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label class="form-label">City / Municipality *</label>
                <select name="city" class="form-control form-control-md" id="city"></select>
                <input type="hidden" class="form-control form-control-md" name="city_text" id="city-text" required>
            </div>
            <div class="col-sm-6 mb-3">
                <label class="form-label">Barangay *</label>
                <select name="barangay" class="form-control form-control-md" id="barangay"></select>
                <input type="hidden" class="form-control form-control-md" name="barangay_text" id="barangay-text" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="street-text" class="form-label">Street (Optional)</label>
                <input type="text" class="form-control form-control-md" name="street_text" id="street-text">
            </div>
            
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <a href="login.php">log-in</a>
            <input type="submit" name="create" value="Submit">
        </form>

        
    
    <script>
        feather.replace()
    </script>
    
</body>

</html>
<script src="ph-address-selector.js"></script>