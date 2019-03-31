<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}else{
    if ($_SESSION['type'] == 'admin'){
        header("location: login.php");
        exit;
    }
}

require_once "./database/db_config.php";

$email = $_SESSION["email"];

$sql = "SELECT submission_date,vacation_start,vacation_end,status FROM vacation 
        WHERE email=? ORDER BY submission_date DESC";

if ($stmt = $con->prepare($sql)){
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $stmt->bind_result($submission_date,$vacation_start,$vacation_end,$status);
    $result = $stmt->get_result();
    $stmt->close();
}else{
    echo 'oops!Something went wrong.Try later!';
}

// Close connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: left; }
    </style>
</head>
<body>
<div class="page-header">
    <h4>Welcome, <?php echo htmlspecialchars($_SESSION["first_name"]); ?>
    <a href="logout.php" class="btn btn-danger">Logout</a></h4>
    <a href="request.php" class="btn btn-primary">Submit Request</a>

<table class="table" style="width:600px;table-layout:fixed">
    <thead>
    <tr>
        <th>Date submitted</th>
        <th>Vacation Start</th>
        <th>Vacation End</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>

        <tr>
            <td><?php echo htmlspecialchars($row['submission_date']) ?></td>
            <td><?php echo htmlspecialchars($row['vacation_start']); ?></td>
            <td><?php echo htmlspecialchars($row['vacation_end']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>
</body>
</html>