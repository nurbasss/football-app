<?php
// file called when user wants to change favourite team
session_start();
if(isset($_POST['teamChange-submit'])){
    require 'dbh.php';
    // updates team name session variable 
    $_SESSION['userTeam'] = $_POST['FavteamChange'];

    $idGetter = 'SELECT teamId, teamName FROM teams WHERE teamName = :utem';
    if($result = oci_parse($conn, $idGetter)){
        oci_bind_by_name($result, ':utem', $_SESSION['userTeam']);
        oci_execute($result);
        $row = oci_fetch_assoc($result);
        // gets team id
        $_SESSION['favTeamId'] = $row['TEAMID'];

        $sqlIns = "BEGIN user_game.changeTeam(:uem, :ftem); END;";
        $stmt = oci_parse($conn, $sqlIns);
        if(!$stmt){
            //checks if input is valid (not SQL code)
            header("Location: ../profile.php?error=dbError2");
            exit();
        }
        else{
            oci_bind_by_name($stmt, ':ftem', $_SESSION['favTeamId']);
            oci_bind_by_name($stmt, ':uem', $_SESSION['userEmail']);
            oci_execute($stmt);
            header("Location: ../profile.php?success=TeamChanged");
        exit();
        }
        
    }
    else{
        header("Location: ../index.php?error=DbError");
        exit();
    }

}
else{
    header("Location: ../index.php");
    exit();
}