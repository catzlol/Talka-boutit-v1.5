<?php
session_start();
include("../SiT_3/config.php");

$threadID = $_GET['thread_id'];
$action = $_GET['action'];
$userID = $_SESSION['user_id'];

$threadIDSafe = $conn->real_escape_string($threadID);
$actionSafe = $conn->real_escape_string($action);

// Check if the user has already voted (doesnt work atm)
$userVoteQuery = "SELECT * FROM `thread_votes` WHERE `thread_id` = '$threadIDSafe' AND `user_id` = '$userID'";
$userVoteResult = $conn->query($userVoteQuery);
$userVote = $userVoteResult->num_rows > 0 ? (object) $userVoteResult->fetch_assoc() : null;

if ($actionSafe == 'upvote') {
    if ($userVote) {
        if ($userVote->vote_type == 'upvote') {
            exit('You have already upvoted this thread.');
        } else {
            $updatePoints = "UPDATE `forum_threads` SET `points` = `points` + 2 WHERE `id` = '$threadIDSafe'";
            $insertVote = "UPDATE `thread_votes` SET `vote_type` = 'upvote' WHERE `thread_id` = '$threadIDSafe' AND `user_id` = '$userID'";
        }
    } else {
        $updatePoints = "UPDATE `forum_threads` SET `points` = `points` + 1 WHERE `id` = '$threadIDSafe'";
        $insertVote = "INSERT INTO `thread_votes` (`thread_id`, `user_id`, `vote_type`) VALUES ('$threadIDSafe', '$userID', 'upvote')";
    }
} elseif ($actionSafe == 'downvote') {
    if ($userVote) {
        if ($userVote->vote_type == 'downvote') {
            exit('You have already downvoted this thread.');
        } else {
            $updatePoints = "UPDATE `forum_threads` SET `points` = `points` - 2 WHERE `id` = '$threadIDSafe'";
            $insertVote = "UPDATE `thread_votes` SET `vote_type` = 'downvote' WHERE `thread_id` = '$threadIDSafe' AND `user_id` = '$userID'";
        }
    } else {
        $updatePoints = "UPDATE `forum_threads` SET `points` = `points` - 1 WHERE `id` = '$threadIDSafe'";
        $insertVote = "INSERT INTO `thread_votes` (`thread_id`, `user_id`, `vote_type`) VALUES ('$threadIDSafe', '$userID', 'downvote')";
    }
} elseif ($actionSafe == 'remove_vote') {
    if ($userVote) {
        $updatePoints = $userVote->vote_type == 'upvote' ?
            "UPDATE `forum_threads` SET `points` = `points` - 1 WHERE `id` = '$threadIDSafe'" :
            "UPDATE `forum_threads` SET `points` = `points` + 1 WHERE `id` = '$threadIDSafe'";
        $insertVote = "DELETE FROM `thread_votes` WHERE `thread_id` = '$threadIDSafe' AND `user_id` = '$userID'";
    } else {
        exit('You have not voted on this thread.');
    }
} else {
    exit('Invalid action.');
}

$conn->query($updatePoints);
$conn->query($insertVote);

// Redirect to the thread page
header("Location: /forum/thread?id=$threadIDSafe");
exit();
?>
