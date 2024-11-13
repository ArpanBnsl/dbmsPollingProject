<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
include 'functions.php';
// Connect to MySQL
$pdo = pdo_connect_mysql();

// MySQL query to select all polls with their associated questions and options
$stmt = $pdo->query("
    SELECT 
        p.pollID, 
        p.title AS poll_title,
        q.questionID, 
        q.questiontext AS question_text, 
        GROUP_CONCAT(o.optiontext ORDER BY o.optionID) AS options 
    FROM 
        polls p
    LEFT JOIN 
        questions q ON q.pollID = p.pollID
    LEFT JOIN 
        options o ON o.questionID = q.questionID
    GROUP BY 
        p.pollID, q.questionID
");

$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_header('Polls')?>

<div class="content home">
    <h2>Polls</h2>
    <p>Welcome to the home page! You can view the list of polls below.</p>
    <a href="create.php" class="create-poll">Create Poll</a>

    <table>
        <thead>
            <tr>
                <td>#</td>
                <td>Poll Title</td>
                <td>Question</td>
                <td>Answer Options</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($polls as $poll): ?>
            <tr>
                <td><?=$poll['pollID']?></td>
                <td><?=htmlspecialchars($poll['poll_title'], ENT_QUOTES)?></td>
                <td><?=htmlspecialchars($poll['question_text'], ENT_QUOTES)?></td>
                <td>
                    <?php 
                    // Display each option as a span
                    $options = explode(',', $poll['options']);
                    foreach ($options as $option): ?>
                        <span class="poll-answer"><?=htmlspecialchars($option, ENT_QUOTES)?></span>
                    <?php endforeach; ?>
                </td>
                <td class="actions">
                    <a href="vote.php?id=<?=$poll['pollID']?>" class="view" title="View Poll">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z" />
                        </svg>
                    </a>
                    <a href="delete.php?id=<?=$poll['pollID']?>" class="trash" title="Delete Poll">
                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
                        </svg>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?=template_footer()?>
