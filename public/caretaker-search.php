<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "../config.php";

// Check and make sure this person is a provider
$sql = "SELECT id as patient_id FROM patients WHERE user_id = ?";
$param_id = $_SESSION["id"];
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $patient_id);
mysqli_stmt_store_result($stmt);

if(mysqli_stmt_num_rows($stmt) == 0){
    header("location: patient-signup.php");
    exit;
} else {
    mysqli_stmt_fetch($stmt);
    $_SESSION["patient_id"] = $patient_id;
}

// Query for providers table
$sql5 = "
SELECT
users.first_name,
users.username,
users.last_name,
users.id as user_id,
caretakers.id as id,
IF(caretaker_patient.patient_id = '$patient_id', TRUE, FALSE) as your_caretaker
FROM caretakers
JOIN users ON users.id = caretakers.user_id
LEFT JOIN caretaker_patient on caretaker_patient.caretaker_id = caretakers.id
WHERE users.id != '$param_id'
";
$stmt5 = mysqli_prepare($link, $sql5);
//mysqli_stmt_bind_param($stmt, "s", $patient_id);
mysqli_stmt_execute($stmt5);
$caretaker_result = mysqli_stmt_get_result($stmt5);

//Add this provider for this patient
if(array_key_exists('add', $_POST))
{
    $caretaker_id = trim($_POST["add"]);
    $patient_id = $_SESSION["patient_id"];
    $created_at = date("y-m-d");
    $updated_at = date("y-m-d");
    $status = "Active";
    
    $sql_add = "INSERT INTO caretaker_patient VALUES ('$patient_id', '$caretaker_id', '$status', '$created_at', '$updated_at')";
    $stmt6 = mysqli_prepare($link, $sql_add);
 // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt6)){
                // Redirect to login page
                header("location: caretaker-search.php");
                print_r($stmt6);

            } else{
                echo "Something went wrong. Please try again later.";
                print_r($stmt6);
                print_r($patient_id);
                print_r($provider_id);
            }
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .wrapper{ width: 800px; padding: 20px; margin:auto; }
        .table-header{text-align: left;}
        .table-button{margin-top: 15px; margin-left: 13px;}
        .table-header-div{display: -webkit-box}
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Let's find a caretaker</h1>
    </div>
    <div class="wrapper">
    <div>
        <?php
        echo "<table class='table'>";
        echo "<h3 class='table-header'>Caretakers</h3>";
        echo "<th> First Name</th>";
        echo "<th> Last Name </th>";
        echo "<th> Your Caretaker? </th>";
        echo "<th></th>";
        echo "<th></th>";
        while($rowitem = mysqli_fetch_array($caretaker_result)) {
            echo "<tr>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>" . $rowitem['first_name'] . "</td>";
            echo "<td>" . $rowitem['last_name'] . "</td>";
            echo "<td>" . $rowitem['your_caretaker'] . "</td>";
            echo "<td><form action='caretaker-search.php' method='post'><input type='hidden' name='prousername' value=" .$rowitem['username'] . "><button type='submit' class='btn btn-default' name='add' id='add' value=" .$rowitem['id'] .">Add</button></td>";
        }
        echo "</table>";
        ?>
    </div>
</div>
    <p>
        <a href="welcome.php" type="button" class="btn btn-default">Home</a>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
</body>
</html>