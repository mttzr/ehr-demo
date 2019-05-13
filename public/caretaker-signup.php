<?php
// Initialize the session
session_start();

// Include config file
require_once "../config.php";

// Define variables and initialize with empty values
$id = uniqid();
$created_at = date("y-m-d");
$updated_at = date("y-m-d");

// Prepare a select statement
$sql = "SELECT id FROM provider_type";

$result = mysqli_query($link, $sql);   

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
         
        // Prepare an insert statement
        $sql = "INSERT INTO caretakers (id, user_id, created_at, updated_at) VALUES (?, ?, ?, ?)";   
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_id, $param_user_id, $param_created_at, $param_updated_at);
            
            // Set parameters
            $param_id = $id;
            $param_user_id = $_SESSION["id"];
            $param_created_at = $created_at;
            $param_updated_at = $updated_at;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: caretaker-home.php");
                print_r($stmt);
            } else{
                echo "Something went wrong. Please try again later.";
                print_r($stmt);

            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($link);
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 500px; padding: 20px; text-align: center; margin:auto; }
        .page-header{text-align: center;}
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Are you ready to become a caretaker?
        </h1>
    </div>
    <div class="wrapper">
        <form method="post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <p>You will have access to the patient records of patients who select you as a caretaker. </p>
        <div class="form-group">
        <input type="submit" class="btn btn-default" value="Yes">
        <a href="./welcome.php" type="reset" class="btn btn-default">No</a>
    </div>
    </form>
        <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
    </div>
</body>
</html>