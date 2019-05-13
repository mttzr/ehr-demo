<?php
// Initialize the session
session_start();

// Include config file
require_once "../config.php";
 
// Define variables and initialize with empty values
$dob = "";
$sex = "";
$phone_number = "";
$street_address = "";
$city = "";
$state = "";
$zip_code = "";
$marital_status = "";
$children = "";
$id = uniqid();
$created_at = date("y-m-d");
$updated_at = date("y-m-d");

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
         
        // Prepare an insert statement
        $sql = "INSERT INTO patients (id, user_id, date_of_birth, sex, phone, address_street, address_city, address_state, address_zip, marriage_status, children_count, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";   
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssssssssss", $param_id, $param_user_id, $param_dob, $param_sex, $param_phone_number, $param_street_address, $param_city, $param_state, $param_zip_code, $param_marital_status, $param_children, $param_created_at, $param_updated_at);
            
            // Set parameters
            $param_id = $id;
            $param_user_id = $_SESSION["id"];
            $param_dob = trim($_POST["dob"]);
            $param_sex = trim($_POST["sex"]);
            $param_phone_number = trim($_POST["phone_number"]);
            $param_street_address = trim($_POST["street_address"]);
            $param_city = trim($_POST["city"]);
            $param_state = trim($_POST["state"]);
            $param_zip_code = trim($_POST["zip_code"]);
            $param_marital_status = trim($_POST["marital_status"]);
            $param_children = trim($_POST["children"]);
            $param_created_at = $created_at;
            $param_updated_at = $updated_at;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: patient-home.php");
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
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; margin: auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.
        </h1>
    </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="<?php echo $dob;?>">
            </div>
            <div class="form-group">
                <label>Sex</label>
                <input type="text" name="sex" class="form-control" value="<?php echo $sex;?>">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" class="form-control" value="<?php echo $phone_number;?>">
            </div>
            <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="street_address" class="form-control" value="<?php echo $street_address?>">
            </div>
                <label>City</label>
                <input type="text" name="city" class="form-control" value="<?php echo $city; ?>">
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>State</label>
                <input type="text" name="state" class="form-control" value="<?php echo $state; ?>">
            </div>
            <div>
                <label>Zip Code</label>
                <input type="text" name="zip_code" class="form-control" value="<?php echo $zip_code; ?>">
            </div>
            <div class="form-group">
                <label>Marital Status</label>
                <input type="text" name="marital_status" class="form-control" value="<?php echo $marital_status;?>">
            </div>
            <div class="form-group">
                <label>Children</label>
                <input type="text" name="children" class="form-control" value="<?php echo $children;?>">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
                <a href="welcome.php" class="btn btn-default">Back</a>

            </div>
        </form>
    </div>    
</body>
</html>