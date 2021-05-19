<?php
// file to log a user in 
if(isset($_POST['login-submit'])){

    require 'dbh.php';

    $email = $_POST['mailuid'];
    $pwd = $_POST['pwd'];

    if(empty($email)|| empty($pwd)){
        // checks form was fully filled out
        header("Location: ../index.php?error=emptyfields&mailuid=".$email);
        exit();
    }
    else {
        $sql = "SELECT * FROM users WHERE email= :email";
        $stmt  = oci_parse($conn, $sql);
        if (!$stmt){
            //checks if input is valid (not SQL code)
            header("Location: ../index.php?error=dbError1");
            echo 'dsds';
            exit();
        }
        else{
            oci_bind_by_name($stmt, ':email', $email);
            oci_execute($stmt);

            //$checker = mysqli_stmt_get_result($stmt);
            if($row = oci_fetch_assoc($stmt)){
                //checks if user exists
                
                $hashpwd = password_verify($pwd, $row['PASS']);
                if($hashpwd == false){
                    //if passwords do not match
                    header("Location: ../index.php?error=incorrectPassword");
                    exit();
                }
                else if($hashpwd == true){
                    //password corrrect
                    session_start();
                    
                    // initialise all session variables used
                    $_SESSION['userId'] = $row['USERID'];
                    $_SESSION['userEmail'] = $row['EMAIL'];
                    $_SESSION['searchedTeam'];
                    $_SESSION['dateSearched'];
                    $_SESSION['lastDeleted'];
                    $_SESSION['defaultStatsName'];
                    $_SESSION['defaultStatsId'];
                    $_SESSION['defaultStatsUserId'] = $_SESSION['userId'];
                    $_SESSION['defaultStatsUserEmail'] = $_SESSION['userEmail'];
                    // get users team as session variable and assign to value
                    $teamgetter = "SELECT teamId, teamName FROM teams WHERE teamId=".$row['TEAM']."";
                    $result = oci_parse($conn, $teamgetter);
                    oci_execute($result);
                    $row2 = oci_fetch_assoc($result);
                    $_SESSION['userTeam'] = $row2['TEAMNAME'];
                    $_SESSION['favTeamId'] = $row2['TEAMID'];
                    $_SESSION['dateStart'];
                    $_SESSION['dateEnd'];
                    $_SESSION['dateTitle'];
                    $_SESSION['scrollPos'];

                    header("Location: ../index.php?success=loginSuccess");
                    exit();

                }
                else{
                    // password wrong 
                    header("Location: ../index.php?error=incorrectPassword");
                    exit();
                }
            }
            else {
                // no user with that email 
                header("Location: ../index.php?error=noSuchUser");
                exit();
            }
        }
    }
}
else{
    header("Location: ../index.php");
    exit();
}
