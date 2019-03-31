<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}else{
    if ($_SESSION['type'] == 'admin'){
        header("location: login.php");
        exit;
    }
}

$submission_date="";
$vacation_start="";
$vacation_end="";
$reason="";
$status="pending";
$email=$_SESSION["email"];
$days_remaining = 0;

$vacation_err = $reason_err = "";

require_once "./database/db_config.php";
//calculate days remaining


$sql = "SELECT days_remaining FROM users where email = ?";

if ($stmt = $con->prepare($sql)){
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $stmt->bind_result($days_remaining);
    $stmt->fetch();
    $stmt->close();
}else{
    echo 'oops!Something went wrong.Try later!';
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
//first we check if date fields are empty
    if (empty($_POST["vacation_start"]) || empty($_POST["vacation_end"])) {
        $vacation_err = 'Please select starting and ending date';
    } else {
        $current_date = date('Y-m-d');

        if ($_POST["vacation_start"] < $current_date) {
            $vacation_err = 'Vacation start is before current date';
        } else {
            if ($_POST["vacation_end"] < $_POST["vacation_start"]) {
                $vacation_err = 'Invalid date order';
            } else {
                $day_start =  strtotime($_POST["vacation_start"]);
                $day_end =  strtotime($_POST["vacation_end"]);
                $dif = $day_end - $day_start;
                $days = round($dif / (60 * 60 * 24));

                if ($days_remaining > $days){
                    $days_remaining = $days_remaining - $days;
                    $vacation_start = $_POST["vacation_start"];
                    $vacation_end = $_POST["vacation_end"];
                }else{
                    $vacation_err = "no more days left";
                }

            }
        }

}
    if (empty($_POST["reason"])) {
        $reason_err = "No reason described";
    } else {
        $reason = $_POST["reason"];
    }

//update remaining days
    if (empty($vacation_err) && empty($reason_err)){
        $submission_date = date('Y-m-d H:i:s');

        $sql = "UPDATE users SET days_remaining=? WHERE email=?";

        if($stmt = $con->prepare($sql)){
            $stmt->bind_param("is",$days_remaining,$email);

            if($stmt->execute()){

               // echo "Succesfully updated days";

            }else{
                $vacation_error = "Couldnt update days remaining";
            }
        }
//insert vacation
        $sql = "INSERT INTO vacation(submission_date,vacation_start,vacation_end,reason,status,email) VALUES(?,?,?,?,?,?)";

        if($stmt = $con->prepare($sql)){
            echo "tralalal";
            $stmt->bind_param("ssssss", $submission_date,$vacation_start,$vacation_end,$reason,$status,$email);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                //mysqli_stmt_store_result($stmt);
                $last_id = $stmt->insert_id;

                //send email to admin
                require_once "email.php";
                send_email_to_admin($email,"email1@localhost",$_SESSION["first_name"],
                    $vacation_start,$vacation_end,$reason,$last_id);

                // Redirect to login page
                header("location: employee_page.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
    }
    // Close connection
    $con->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request</title>
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
        <label>Date From:</label>
        <input type="date" name="vacation_start" class="form-control"><br>
    </div>
    <div class="form-group">
        <label>Date To:</label>
        <input type="date" name="vacation_end" class="form-control"><br>
        <span class="help-block"><?php echo $vacation_err; ?></span>
    </div>
    <div class="form-group">
        <label>Reason :</label>
        <textarea rows="4" cols="50" name="reason" class="form-control"></textarea>
        <span class="help-block"><?php echo $reason_err; ?></span>
    </div>
    <input class="btn btn-primary" type="submit" name="submit" value="Submit">
</form>
</div>
</body>

