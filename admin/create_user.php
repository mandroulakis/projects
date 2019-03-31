<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}else{
    if($_SESSION["type"] == 'employee' ){
        header("location:../index.php");
        exit;
    }
}

$first_name = "";
$last_name = "";
$email = "";
$password = "";
$user_type = "";
$error = "";

require_once "../database/db_config.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $user_type=$_POST["user_type"];

    //check if a field is empty
    if( empty( $first_name) || empty($last_name) || empty($email) || empty($password)){
        $error = "Empty field/fields";
    }else{
        //if every field is filled check if email isn't valid
        /*
        if(!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
          $error = "Invalid email format!";
        }else{
            $error = trim($_POST["email"]);
        }
        */
    }
    if(empty($error)) {

        $sql = "INSERT INTO users VALUES(?,?,?,?,?)";

        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $user_type);

            if ($stmt->execute()) {
                header("location:admin_page.php");
            } else {
                echo "Something went wrong";
            }

        }
        $con->close();
    }

    // Close connection
    $con->close();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label>First Name:</label>
        <input type="text" name="first_name" class="form-control"><br>
    </div>
    <div class="form-group">
        <label>Last Name:</label>
        <input type="text" name="last_name" class="form-control"><br>
    </div>
    <div class="form-group">
        <label>Email :</label>
        <input type="text" name="email" class="form-control"><br>
    </div>
    <div class="form-group">
        <label>Password :</label>
        <input type="password" name="password" class="form-control"><br>
    </div>
    <div class="form-group" class="form-control">
        <label>User Type :</label>
        <select name="user_type">
            <option value="admin">admin</option>
            <option value="employee">employee</option>
        </select><br>
    </div>
    <input class="btn btn-primary" type="submit" name="submit" value="Create" >
</form>
</div>
</body>