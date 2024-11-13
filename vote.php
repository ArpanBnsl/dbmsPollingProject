<?php
include 'functions.php';
// Connect to MySQL
$pdo = pdo_connect_mysql();
// Assuming the username is stored in a session for identifying the user
session_start();
$username = $_SESSION['username']; // Ensure you have session started and username set after user logs in

// If the GET request "id" exists (poll id)...
if (isset($_GET['id'])) {
    // Fetch poll details
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE pollID = ?');
    $stmt->execute([$_GET['id']]);
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the poll record exists
    if ($poll) {
        // Fetch the question related to this poll
        $stmt = $pdo->prepare('SELECT * FROM questions WHERE pollID = ?');
        $stmt->execute([$_GET['id']]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch the options for this question
        $stmt = $pdo->prepare('SELECT * FROM options WHERE questionID = ?');
        $stmt->execute([$question['questionID']]);
        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if the user has already voted in this poll
        $stmt = $pdo->prepare('
            SELECT v.optionID 
            FROM votes v 
            JOIN options o ON v.optionID = o.optionID 
            JOIN questions q ON o.questionID = q.questionID 
            WHERE v.voter = ? AND q.pollID = ?');
        $stmt->execute([$username, $_GET['id']]);
        $user_vote = $stmt->fetch(PDO::FETCH_ASSOC);

        $user_voted_option = $user_vote ? $user_vote['optionID'] : null;
        
        // If the user submits a vote
        if (isset($_POST['poll_option']) && isset($_POST['submit_vote'])) {
            $selected_option = $_POST['poll_option'];

            if ($user_vote) {
                // Update the user's vote if they have already voted
                $stmt = $pdo->prepare('UPDATE votes SET optionID = ? WHERE voter = ? AND optionID = ?');
                $stmt->execute([$selected_option, $username, $user_voted_option]);
            } else {
                // Insert a new vote
                $stmt = $pdo->prepare('INSERT INTO votes (voter, optionID) VALUES (?, ?)');
                $stmt->execute([$username, $selected_option]);
            }
        }
    } else {
        exit('Poll with that ID does not exist.');
    }
} else {
    exit('No poll ID specified.');
}
?>

<?=template_header('Poll Vote')?>

<div class="content poll-vote">
    <h2><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></h2>
    <p><?=htmlspecialchars($question['questiontext'], ENT_QUOTES)?></p>

    <form action="vote.php?id=<?=$_GET['id']?>" method="post">
        <?php foreach ($options as $option): ?>
            <label>
                <input type="radio" name="poll_option" value="<?=$option['optionID']?>"
                <?=($user_vote && $user_voted_option == $option['optionID']) ? 'checked' : ''?>
                <?=($user_vote) ? 'disabled' : ''?>> 
                <?=htmlspecialchars($option['optiontext'], ENT_QUOTES)?>
            </label><br>
        <?php endforeach; ?>

        <div>
            <button type="submit" name="submit_vote" <?=($user_vote) ? 'disabled' : ''?>>Vote</button>
            <button type="button" onclick="enableVote()" <?=(!$user_vote) ? 'disabled' : ''?>>Change Vote</button>
            <button type="submit" name="view_result">View Result</button>
        </div>
    </form>
</div>

<script>
// JavaScript to enable voting when the user clicks "Change Vote"
function enableVote() {
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    const voteButton = document.querySelector('button[name="submit_vote"]');
    radioButtons.forEach(button => button.disabled = false);
    voteButton.disabled = false;
}
</script>

<?=template_footer()?>

<?php
// Redirect to the result page only if "View Result" button is clicked
if (isset($_POST['view_result'])) {
    header('Location: result.php?id=' . $_GET['id']);
    exit;
}
?>
