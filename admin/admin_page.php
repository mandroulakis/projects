<?php
// Initialize the session
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}else{
    if($_SESSION["type"] == 'employee' ){
        header("location: ../index.php");
        exit;
    }
}

require_once "../database/db_config.php";
$email = $_SESSION["email"];

$sql = "SELECT first_name,last_name,email,type FROM users";

if ($stmt = $con->prepare($sql)){
    $stmt->execute();
    $stmt->bind_result($first_name,$last_name,$email,$type);
    $result = $stmt->get_result();
    $stmt->close();
}else{
    echo 'oops!Something went wrong.Try later!';
}

// Close connection
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: left; }
        .table-row{
            cursor:pointer;
        }
    </style>
</head>
<body>
<div class="page-header">
    <h4>Welcome, <?php echo htmlspecialchars($_SESSION["first_name"]); ?>
        <a href="../logout.php" class="btn btn-danger">Logout</a></h4>
    <a href="create_user.php" class="btn btn-primary">Create User</a>
    <table class="table table-condensed table-striped table-hover" style="width:600px;table-layout:fixed">
        <thead>
        <tr>
            <th>First Name</th>
            <th>Last name</th>
            <th>Email</th>
            <th>Type</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="table-row" onclick="window.location.href = '<?php echo htmlspecialchars("http://localhost/php_project/admin/view_user.php?email=". $row['email']) ?>'; ">
                <td ><?php echo htmlspecialchars($row['first_name']) ?></td>
                <td ><?php echo htmlspecialchars($row['last_name']); ?></td>
                <td ><?php echo htmlspecialchars($row['email']); ?></td>
                <td ><?php echo htmlspecialchars($row['type']); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>