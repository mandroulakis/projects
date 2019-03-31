<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$email = $_GET["email"];

$sql = "SELECT first_name,last_name,type FROM users WHERE email=?";

require_once "../database/db_config.php";

if($stmt=$con->prepare($sql)){
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->bind_result($first_name,$last_name,$type);
    $result = $stmt->get_result();
    $row =  $result->fetch_assoc();
    $stmt->close();
}else{
    echo "something went wrong";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Properties</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">

<form action="" method="POST">
    <div class="form-group">
        <label>First Name:</label>
        <input type="text" name="first_name" class="form-control" value="<?php echo $row['first_name']?>"><br>
    </div>
    <div class="form-group">
        <label>Last Name:</label>
        <input type="text" name="last_name" class="form-control" value="<?php echo $row['last_name']?>"><br>
    </div>
    <div class="form-group">
        <label>Email :</label>
        <input type="text" name="email" class="form-control" value="<?php echo $email?>"><br>
    </div>
    <div class="form-group">
        <label>Password : </label>
        <input type="password" class="form-control" name="password"><br>
    </div>
    <div class="form-group">
        <label>User Type :</label>
        <select name="user_type" >
            <option><?php echo $row['type']?></option>
        </select><br>
    </div>
</form>

</div>
</body>