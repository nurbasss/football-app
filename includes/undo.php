<?php
// file to undo last delete 
require 'dbh.php';
session_start();


$matchId = $_POST['matchId'];
$userId = $_SESSION['userId'];

if(isset($_POST['matchId'])){
    //check user got here legit
    $select = "SELECT * FROM user_matches WHERE userVal= :uval AND matchVal= :mval";
    $stmt = oci_parse($conn, $select);
    
    if (!$stmt){
        //checks if input is valid (not SQL code)
        header("Location: ../myMatches.php?error=dbError1");
        exit();
    }
    else{
        oci_bind_by_name($stmt, ':uval', $userId);
        oci_bind_by_name($stmt, ':mval', $matchId);
        oci_execute($stmt);

        if($row = mysqli_fetch_assoc($stmt)){
            // undo already done 
            header("Location: ../myMatches.php?error=undoneBefore");
            exit();
        }
        else{
            $insert = "BEGIN user_game.addUserMatch(:uval,:mval); END;";
            $stmt2 = oci_parse($conn, $insert);
    
            if (!$stmt2){
                //checks if input is valid (not SQL code)
                header("Location: ../myMatches.php?error=dbError1");
                exit();
            }
            else{
                oci_bind_by_name($stmt2, ':uval', $userId);
                oci_bind_by_name($stmt2, ':mval', $matchId);
                oci_execute($stmt2);
                
                header("Location: ../myMatches.php?success=undone");
                exit();
            }
        }
    }
}
else {
    header("Location: ../index.php");
    exit();
}