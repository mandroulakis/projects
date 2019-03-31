<?php
// Initialize the session
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if($_SESSION["type"] === 'admin'){
        header("location:./admin/admin_page.php");
    }
    else{
        header("location: welcome.php");
    }
    exit;
}

require_once "./database/db_config.php";

$email = $password = "";
$email_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if email is empty and if it's not check for valid email pattern
    if(empty(trim($_POST["email"]))){
        $email = "Please enter an email.";
    } else{
        $email = trim($_POST["email"]);
        /*
        if(!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
            $email_err = "Invalid email format!";
        }else{
            $email = trim($_POST["email"]);
        }*/
    }

    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($email_err) && empty($password_err)){

        $sql = "SELECT first_name, email, password,type FROM users WHERE email = ?";

        if($stmt = $con->prepare($sql)){

            $stmt->bind_param( "s", $email);

            if($stmt->execute()){

                $stmt->store_result();

                // Check if email exists, if yes then verify password
                if($stmt->num_rows == 1){
                    // Bind result variables
                    $stmt->bind_result($first_name, $email, $hashed_password,$type);
                    if(mysqli_stmt_fetch($stmt)){

                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["first_name"] = $first_name;
                            $_SESSION["email"] = $email;
                            $_SESSION["type"] = $type;

                            if ($type == "admin") {
                                header("location:./admin/admin_page.php");
                            }else{
                                header("location: employee_page.php");
                            }

                        } else{

                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    $password_err = "No account found.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        $stmt->close();
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
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
            <span class="help-block"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Login">
        </div>
    </form>
</div>
</body>
</html>