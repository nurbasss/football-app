<?php
// file to remove a match from users logged matches

require 'dbh.php';
session_start();


$matchId = $_POST['matchId'];
$userId = $_SESSION['userId'];
$position = $_POST['pos'];
$page = $_POST['page'];

$delete = "BEGIN user_game.deleteUserMatch(:uval,:mval); END;";
$stmt = oci_parse($conn, $delete);

if (!$stmt){
    //checks if input is valid (not SQL code) etc..
    header("Location: ../myMatches.php?error=dbError1");
    exit();
}
else{
    $_SESSION['lastDeleted'] = $matchId;
    oci_bind_by_name($stmt, ':uval', $userId);
    oci_bind_by_name($stmt, ':mval', $matchId);
    oci_execute($stmt);
    // return to same page and position
    header("Location: ../".$page.".php?success=deleted#".$position."");
    exit();
}

?>