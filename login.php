<?php
include 'functions.php'; // Include the functions.php file for header and footer templates

// Start session to store username and password
session_start();

// Connect to the database
$pdo = pdo_connect_mysql();

// Initialize variables for the form
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get username and password from the form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare SQL statement to search for username and password in the users table
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND password = ?');
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // If a user is found, store the username and password in the session
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;

        // Redirect to create.php
        header('Location: home.php');
        exit;
    } else {
        // If no user is found, show an error message
        $error = 'Username and Password not matched';
    }
}
?>

<?=template_header('Login')?>

<div class="content">
    <h2>Login</h2>
    <div class="login-container">
        <form action="login.php" method="post" class="login-form">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" placeholder="Enter your username" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <div class="input-group">
                <input type="submit" value="LOGIN" class="btn-login">
            </div>
            <!-- Display error message if credentials are not matched -->
            <?php if ($error): ?>
                <p class="error-msg"><?= $error ?></p>
            <?php endif; ?>
        </form>
    </div>
</div>

<?=template_footer()?>
