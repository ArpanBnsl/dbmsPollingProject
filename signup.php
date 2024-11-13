<?php
include 'functions.php'; // Include the functions.php file for header and footer templates

// Start session
session_start();

// Connect to the database
$pdo = pdo_connect_mysql();

$error = ''; // Error message
$success = ''; // Success message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $age = $_POST['age'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $state = $_POST['state'] ?? '';
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';
    $phoneno = $_POST['phoneno'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check if the username, email, or phone number already exists in the database
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ? OR phoneno = ?');
    $stmt->execute([$username, $email, $phoneno]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // If a record already exists, display the error message and stay on this page
        $error = 'Username, Email, or Phone Number already registered!';
    } else {
        try {
            // Insert new user into the users table
            $stmt = $pdo->prepare('INSERT INTO users (firstname, lastname, age, gender, state, city, country, phoneno, email, username, password) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$firstname, $lastname, $age, $gender, $state, $city, $country, $phoneno, $email, $username, $password]);

            // If successful, display success message and redirect to login
            $success = 'Registered successfully!';
            header('Location: login.php');
            exit;

        } catch (PDOException $e) {
            // If query fails, display the error message
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<?=template_header('Register')?>

<div class="content">
    <h2>Register</h2>
    <div class="register-container">
        <form action="signup.php" method="post" class="register-form">
            <div class="input-group">
                <label for="firstname">First Name:</label>
                <input type="text" name="firstname" id="firstname" required>
            </div>
            <div class="input-group">
                <label for="lastname">Last Name:</label>
                <input type="text" name="lastname" id="lastname" required>
            </div>
            <div class="input-group">
                <label for="age">Age:</label>
                <input type="number" name="age" id="age" required>
            </div>
            <div class="input-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="input-group">
                <label for="state">State:</label>
                <input type="text" name="state" id="state" required>
            </div>
            <div class="input-group">
                <label for="city">City:</label>
                <input type="text" name="city" id="city" required>
            </div>
            <div class="input-group">
                <label for="country">Country:</label>
                <input type="text" name="country" id="country" required>
            </div>
            <div class="input-group">
                <label for="phoneno">Phone No:</label>
                <input type="text" name="phoneno" id="phoneno" required>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="input-group">
                <input type="submit" value="Register" class="btn-register">
            </div>
            
            <!-- Display error message if there is an issue with the query -->
            <?php if ($error): ?>
                <p class="error-msg"><?= $error ?></p>
            <?php endif; ?>
            
            <!-- Display success message if registration is successful -->
            <?php if ($success): ?>
                <p class="success-msg"><?= $success ?></p>
            <?php endif; ?>
        </form>
    </div>
</div>

<?=template_footer()?>
