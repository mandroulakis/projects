<?php
$action = $_GET["action"];
$id = $_GET["id"];


$submission_date = ""; //get it from mysql query
$email = "";//as above

require "./database/db_config.php";

$sql = "SELECT email,submission_date FROM vacation WHERE vac_id=?";
//find email and submission_date
if ($stmt = $con->prepare($sql)){
    mysqli_stmt_bind_param($stmt,'i',$id);
    if($stmt->execute()){
        $stmt->bind_result($email,$submission_date);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $submission_date = $row['submission_date'];
        $email = $row['email'];
        $stmt->close();
    }
    else{
        echo 'Error';
    }
}else{
    echo 'oops!Something went wrong.Try later!';
}

$sql = "UPDATE vacation SET status=? WHERE vac_id=?";

if($stmt = $con->prepare($sql)){
    $stmt->bind_param("si",$action,$id);

    if($stmt->execute()){
        require "email.php";
        send_email_to_user("email1@localhost",$email,$action,$submission_date);
        echo "Succesfully {$action} the request.Redirecting...";
    }else{
        echo "Something went wrong";
    }
}
$con->close();

header("refresh:5;login.php");
exit;


