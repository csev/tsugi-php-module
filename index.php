<?php
require_once "config.php";

use \Tsugi\Core\LTIX;

// Retrieve the launch data if present
$LTI = LTIX::requireData();
$p = $CFG->dbprefix;
$displayname = $USER->displayname;

// Handle your POST data here...
if ( isset($_POST['guess'])) {
    if ( !is_numeric($_POST['guess'])) {
        $_SESSION['error'] = "Guess must be numeric";
        header('Location: '.addSession('index.php'));
        return;
    }

    $PDOX->queryDie("INSERT INTO {$p}tsugi_sample_module
        (link_id, user_id, guess, updated_at)
        VALUES ( :LI, :UI, :GUESS, NOW() )
        ON DUPLICATE KEY UPDATE guess=:GUESS, updated_at = NOW()",
        array(
            ':LI' => $LINK->id,
            ':UI' => $USER->id,
            ':GUESS' => $_POST["guess"]
        )
    );

    if ( $_POST['guess'] == 42 ) {
        $_SESSION['success'] = "Nice work";
    } else {
        $_SESSION['error'] = "Please try again";
    }
    header('Location: '.addSession('index.php'));
    return;
}

// Retrieve the old data
$row = $PDOX->rowDie("SELECT guess FROM {$p}tsugi_sample_module
    WHERE user_id = :UI",
    array(':UI' => $USER->id)
);
$oldguess = $row ? $row['guess'] : '';

// Start of the output
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->flashMessages();
echo("<h1>Guessing Game</h1>\n");
$OUTPUT->welcomeUserCourse();

?>
<form method="post">
Pick a number:
<input type="text" name="guess" value="<?= $oldguess ?>"><br/>
<input type="submit" name="send" value="Guess">
</form>
<?php

echo("<pre>Global Tsugi Objects:\n\n");
var_dump($USER);
var_dump($CONTEXT);
var_dump($LINK);

echo("\n<hr/>\n");
echo("Session data (low level):\n");
echo($OUTPUT->safe_var_dump($_SESSION));

$OUTPUT->footerStart();
?>
<script>
// You might put some JavaScript here
</script>
<?php
$OUTPUT->footerEnd();

