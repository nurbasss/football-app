<?php
// file called when user wishes to change thier password
session_start();
if(isset($_POST['passChange-Submit'])){
    require 'dbh.php';
    $OldPass = $_POST['pwdOld'];
    $newPass = $_POST['pwdNew'];
    $newPass2 = $_POST['pwdNew1'];

    if(empty($OldPass)|| empty($newPass) || empty($newPass2)){
        // checks form was fully filled out
        header("Location: ../profile.php?error=emptyfields");
        exit();
    }
    else if($newPass != $newPass2 ){
        //checks if two passwords match
        header("Location: ../profile.php?error=passwordsDoNotMatch");
        exit();
    }
    else {
        //form fully filled $_SESSION['userEmail']
        $sql = "SELECT * FROM users WHERE email= :uem";
        if($result = oci_parse($conn, $sql)){
            oci_bind_by_name($result, ':uem', $_SESSION['userEmail']);
            oci_execute($result);
            $row = oci_fetch_assoc($result);

            $hashpwd = password_verify($OldPass, $row['PASS']);
            if($hashpwd == false){
                //if passwords do not match
                header("Location: ../profile.php?error=incorrectPassword");
                exit();
            }
            else if($hashpwd == true){
                //password corrrect
                $sqlIns = "BEGIN user_auth.resetPassword(:em, :pswrd); END;";
                $stmt = oci_parse($conn, $sqlIns);
                if(!$stmt){
                    //checks if input is valid (not SQL code)
                    header("Location: ../profile.php?error=dbError2");
                    exit();
                }
                else{
                    $hashpwd = password_hash($newPass, PASSWORD_DEFAULT); // hash the password

                    oci_bind_by_name($stmt, ':pswrd', $hashpwd); 
                    oci_bind_by_name($stmt, ':em', $_SESSION['userEmail']); 
                    oci_execute($stmt);
                    header("Location: ../profile.php?success=passwordChanged");
                    exit();
                }
            }
        }
        else{
            header("Location: ../profile.php?error=DBError");
            exit();
        }
    }
}
else{
    header("Location: ../index.php");
    exit();
}