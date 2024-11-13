<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the header template
include 'functions.php';
?>

<?=template_header('Welcome')?>

<div class="content home">
    <h2>Welcome to Our Website</h2>
    <p>Go to login if already registered, else go to signup.</p>
    
    <div class="auth-buttons">
        <a href="login.php" class="btn-auth">Login</a>
        <a href="signup.php" class="btn-auth">Signup</a>
    </div>
</div>


<?=template_footer()?>
