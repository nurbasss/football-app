<?php
// file to favourite/unfavourite a match that a user has logged

if(isset($_POST['fav-Id'])){
    session_start();
    // fav a match
    require 'dbh.php';

    $favId = $_POST['fav-Id'];
    $position = $_POST['pos'];
    //".$_SESSION['userId']."

    $update = "BEGIN user_game.makeFavourite(:uid, :fid); END;";
    $stmt = oci_parse($conn, $update);
    if (!$stmt){
        
        header("Location: ../myMatches.php?error=dbError1");
        exit();
    }
    else{
        oci_bind_by_name($stmt, ':uid', $_SESSION['userId']);
        oci_bind_by_name($stmt, ':fid', $favId);
        oci_execute($stmt);
        header("Location: ../myMatches.php?success=favourited#".$position."");
        exit();
    }

}
if(isset($_POST['unfav-Id'])){
    session_start();
    // unfav a match
    require 'dbh.php';

    $favId = $_POST['unfav-Id'];
    $position = $_POST['pos'];

    $update = "BEGIN user_game.makeUnfavourite(:uid, :fid); END;";
    $stmt = oci_parse($conn, $update);
    if (!$stmt){
     
        header("Location: ../myMatches.php?error=dbError2");
        exit();
    }
    else{
        oci_bind_by_name($stmt, ':uid', $_SESSION['userId']);
        oci_bind_by_name($stmt, ':fid', $favId);
        oci_execute($stmt);
        header("Location: ../myMatches.php?success=unfavourited#".$position."");
        exit();
    }

}
else{
    header("Location: ../index.php");
    exit();
}