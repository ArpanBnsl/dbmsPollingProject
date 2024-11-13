<?php
include 'functions.php';

// Connect to MySQL
$pdo = pdo_connect_mysql();

// If the GET request "id" exists (poll id)...
if (isset($_GET['id'])) {
    // MySQL query that selects the poll record by the GET request "id"
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE pollID = ?');
    $stmt->execute([ $_GET['id'] ]);
    // Fetch the poll record
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the poll record exists with the id specified
    if ($poll) {
        // MySQL Query to get the question related to this poll
        $stmt = $pdo->prepare('SELECT * FROM questions WHERE pollID = ?');
        $stmt->execute([ $_GET['id'] ]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the question exists
        if ($question) {
            // MySQL Query to get the options for this question
            $stmt = $pdo->prepare('SELECT * FROM options WHERE questionID = ?');
            $stmt->execute([ $question['questionID'] ]);
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate total votes for the question
            $total_votes = 0;
            foreach ($options as &$option) {
                // Get the vote count for each option
                $stmt = $pdo->prepare('SELECT COUNT(*) AS votes FROM votes WHERE optionID = ?');
                $stmt->execute([ $option['optionID'] ]);
                $vote = $stmt->fetch(PDO::FETCH_ASSOC);
                $option['votes'] = $vote['votes'];
                $total_votes += $option['votes'];
            }
        } else {
            exit('No questions found for this poll.');
        }
    } else {
        exit('Poll with that ID does not exist.');
    }
} else {
    exit('No poll ID specified.');
}
?>

<?=template_header('Poll Results')?>

<div class="content poll-result">
    <h2><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></h2>

    <div class="wrapper">
        <div class="poll-question">
            <p><?=htmlspecialchars($question['questiontext'], ENT_QUOTES)?></p>

            <div class="result-bar-wrapper">
                <?php 
                    // Debugging output: Check if options are loaded correctly
                    // print_r($options); // Uncomment if necessary
                ?>
                <?php foreach ($options as $option): ?>
                    <div class="poll-option">
                        <p><?=htmlspecialchars($option['optiontext'], ENT_QUOTES)?> 
                            <span>(<?=$option['votes']?> Votes)</span>
                        </p>

                        <div class="result-bar-wrapper">
                            <?php
                                // Calculate the width for the result bar
                                $percentage = $total_votes ? round(($option['votes'] / $total_votes) * 100) : 0;
                            ?>
                            <div class="result-bar" style="width: <?= $percentage ?>%;">
                                <?= $percentage ?>%
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<?=template_footer()?>
