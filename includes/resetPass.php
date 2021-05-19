<?php
// file to reset users password (resets it to password)
if(isset($_POST['passReset-Submit'])){

    require 'dbh.php';
    session_start();
    $email = $_POST['mailuid'];
    //$out =false;

    if(empty($email)){
        // checks form was fully filled out
        header("Location: ../reset.php?error=emptyfields");
        exit();
    }
    else{
        $sql = "BEGIN :out := user_auth.checkUserRegister(:em); END;";
        $stmt  = oci_parse($conn, $sql);
        if (!$stmt){
            //checks if input is valid (not SQL code)
            header("Location: ../reset.php?error=dbError1");
            exit();
        }
        else{
            oci_bind_by_name($stmt, ':em', $email);
            oci_bind_by_name($stmt, ':out', $out);
            oci_execute($stmt);

            if($out == 1){
                //user exists
                // reset password to 'password'
                $defaultPass = 'password';
                $hashpwd = password_hash($defaultPass, PASSWORD_DEFAULT);
                $sqlIns = "BEGIN user_auth.resetPassword(:em, :pswrd); END;";

                $stmt = oci_parse($conn, $sqlIns);
                if(!$stmt){
                    //checks if input is valid (not SQL code)
                    header("Location: ../reset.php?error=dbError1");
                    exit();
                }
                else{
                    oci_bind_by_name($stmt, ':pswrd', $hashpwd); 
                    oci_bind_by_name($stmt, ':em', $email); 
                    oci_execute($stmt);
                    header("Location: ../reset.php?success=passwordReset");
                    exit();
                }
                
            }
            else {
                //user does not exist
                header("Location: ../reset.php?error=noSuchUser");
                exit();
            }

        }
    }
}
else{
    header("Location: ../index.php");
    exit();
}