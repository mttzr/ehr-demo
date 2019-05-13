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
$sql = "SELECT id, name FROM provider_type";

$result = mysqli_query($link, $sql);   

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
         
        // Prepare an insert statement
        $sql = "INSERT INTO providers (id, user_id, provider_type_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?)";   
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_id, $param_user_id, $param_provider_type_id, $param_created_at, $param_updated_at);
            
            // Set parameters
            $param_id = $id;
            $param_user_id = $_SESSION["id"];
            $param_provider_type_id = 1;
            $param_created_at = $created_at;
            $param_updated_at = $updated_at;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: provider-home.php");
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
        <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .wrapper{ width: 750px; text-align: center; margin: auto; }
    </style>
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.
        </h1>
    </div>
        <form method="post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
        <label> Provider Type </label>
        <select name="Provider Types">
        <option>---Select Provider Type---</option>
        <?php while($providerData = mysqli_fetch_array($result)) { ?>
            <option value ="<?php echo $provider_type_id;?>"> <?php echo $providerData['name'];?>
            </option>
        <?php }?>
        </select value ="<?php echo $provider_type_id;?>">
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <input type="reset" class="btn btn-default" value="Reset">
        <a href="welcome.php" class="btn btn-default">Back</a>

    </div>
    </form>
    </div>
        <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
</body>
</html>