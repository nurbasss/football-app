<?php
// file to sign up a new user 
if(isset($_POST['signup-submit'])){

    require 'dbh.php';


    $email = $_POST['emailuid'];
    $pwd = $_POST['pwd'];
    $pwd2 = $_POST['confpwd'];
    $team = $_POST['team'];
    $id=1;
    

    if(empty($email)|| empty($pwd)|| empty($pwd2) || empty($team) ){
        // checks form was fully filled out
        header("Location: ../signup.php?error=emptyfields&emailuid=".$email);
        exit();
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        //checks if email is valid
        header("Location: ../signup.php?error=invalidemail");
        exit();
    }
    else if($pwd != $pwd2 ){
        //checks if two passwords match
        header("Location: ../signup.php?error=passwordsDoNotMatch&emailuid=".$email);
        exit();
    }
    // add checks for length etc..
    else{
        //check if email exists in db
        $sql = "BEGIN :out := user_auth.checkUserRegister(:mail); END;";
        $stmt = oci_parse($conn, $sql);
        if (!$stmt){
            //checks if input is valid (not SQL code)
            header("Location: ../signup.php?error=dbError1");
            exit();
        }
        else{
            oci_bind_by_name($stmt, ':mail', $email);
            oci_bind_by_name($stmt, ':out', $out);
            oci_execute($stmt);
            echo $out;
            
            if($out == 1){
                //email exists in db
                header("Location: ../signup.php?error=emailTaken");
                exit();
            }
            else{
                $sqlIns = "Begin user_auth.addUser(1, :email, :password, :team); end;";
                $stmt = oci_parse($conn, $sqlIns);
                if(!$stmt){
                    //checks if input is valid (not SQL code)
                    header("Location: ../signup.php?error=dbError2");
                    exit();
                }
                else{
                    $hashpwd = password_hash($pwd, PASSWORD_DEFAULT); // hash the password

                    oci_bind_by_name($stmt, ':email', $email); 
                    oci_bind_by_name($stmt, ':password', $hashpwd);
                    oci_bind_by_name($stmt, ':team', $team);
                    oci_execute($stmt);
                    header("Location: ../index.php?success=signedup");
                    exit();
                }
            }
        }
    }
    oci_free_statement($stmt);
    oci_commit($conn);
    oci_close($conn);


}
else{
    header("Location: ../signup.php");
    exit();
}
