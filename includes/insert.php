<?php
// file to log a match

require 'dbh.php';
session_start();


$matchId = $_POST['matchId'];
$userId = $_SESSION['userId'];
$position = $_POST['pos'];


$select = "SELECT * FROM user_matches WHERE userVal= :uval AND matchVal= :mval";
$stmt = oci_parse($conn, $select);

if (!$stmt){
    //checks if input is valid (not SQL code)
    header("Location: ../searchResult.php?error=dbError1");

    exit();
}
else{
    
    oci_bind_by_name($stmt, ':uval', $userId); 
    oci_bind_by_name($stmt, ':mval', $matchId); 
    oci_execute($stmt);
    //$checker = mysqli_stmt_get_result($stmt);
    if($row = oci_fetch_assoc($stmt)){
        // match already logged 
        header("Location: ../searchResult.php?error=alreadyLogged#".$position."");
        exit();
    }
    else{
        $insert = "BEGIN user_game.addUserMatch(:uval,:mval); END;";
        $stmt2 = oci_parse($conn, $insert);

        if (!$stmt2){
            //checks if input is valid (not SQL code)
            header("Location: ../searchResult.php?error=dbError1");
                        //echo 'dcs';

            exit();
        }
        else{
            oci_bind_by_name($stmt2, ':uval', $userId); 
            oci_bind_by_name($stmt2, ':mval', $matchId);
            oci_execute($stmt2);
            // return to past position
            header("Location: ../searchResult.php?success=added#".$position."");
            exit();
        }
    }

}


?>