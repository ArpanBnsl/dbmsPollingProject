<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
include 'functions.php';
// Connect to MySQL
$pdo = pdo_connect_mysql();
// Output message
$msg = '';

// Assuming the user is logged in and their username is stored in session
session_start();
$createdBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'unknown_user';

// Check if POST data is not empty
if (!empty($_POST)) {
    // Get poll title and question text from the form
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $questionText = isset($_POST['question']) ? $_POST['question'] : '';

    // Insert new poll record into the "polls" table
    $stmt = $pdo->prepare('INSERT INTO polls (title, createdBy) VALUES (?, ?)');
    $stmt->execute([$title, $createdBy]);

    // Get the last inserted pollID
    $pollID = $pdo->lastInsertId();

    // Insert the question into the "questions" table (only one question per poll)
    $stmt = $pdo->prepare('INSERT INTO questions (pollID, questionText) VALUES (?, ?)');
    $stmt->execute([$pollID, $questionText]);

    // Get the last inserted questionID
    $questionID = $pdo->lastInsertId();

    // Get the options from POST data (textarea input separated by newlines)
    $options = isset($_POST['options']) ? explode(PHP_EOL, $_POST['options']) : [];

    // Iterate through the options and insert into the "options" table
    foreach ($options as $optionText) {
        // Trim and check if the option is not empty
        $optionText = trim($optionText);
        if (empty($optionText)) continue;

        // Insert option into the "options" table
        $stmt = $pdo->prepare('INSERT INTO options (questionID, optionText) VALUES (?, ?)');
        $stmt->execute([$questionID, $optionText]);
    }

    // Output success message
    $msg = 'Poll created successfully!';
}
?>

<?=template_header('Create Poll')?>

<div class="content update">
    <h2>Create Poll</h2>

    <form action="create.php" method="post">

        <!-- Poll Title -->
        <label for="title">Poll Title</label>
        <input type="text" name="title" id="title" placeholder="Enter Poll Title" required>

        <!-- Poll Question Text -->
        <label for="question">Poll Question</label>
        <input type="text" name="question" id="question" placeholder="Enter the poll question" required>

        <!-- Options Input (textarea for multiple options) -->
        <label for="options">Answer Options (one per line)</label>
        <textarea name="options" id="options" placeholder="Option 1<?=PHP_EOL?>Option 2<?=PHP_EOL?>Option 3" required></textarea>

        <button type="submit">Create</button>

    </form>

    <?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php endif; ?>
</div>

<?=template_footer()?>
