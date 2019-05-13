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

// Check and make sure this person is a caretaker
$sql = "SELECT * FROM caretakers WHERE user_id = ?";
$param_id = $_SESSION["id"];
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if(mysqli_stmt_num_rows($stmt) == 0){
    header("location: caretaker-signup.php");
    exit;
}


//Visit patient and change session
if(array_key_exists('visit', $_POST))
{
    $patient_id = trim($_POST["visit"]);
    $patient_username = trim($_POST["pusername"]);
    $_SESSION["pid"] = $patient_id;
    $_SESSION["pusername"] = $patient_username;  
    header("location: patient-home-caretaker-view.php");
}

// Query for patients table
$sql2 = "SELECT
users.id,
users.username,
users.first_name,
users.last_name,
caretaker_patient.created_at
FROM caretaker_patient
JOIN patients on patients.id = caretaker_patient.patient_id
JOIN users ON users.id = patients.user_id
JOIN caretakers ON caretakers.id = caretaker_patient.caretaker_id
JOIN users AS users2 ON users2.id = caretakers.user_id
WHERE users2.id = ?";
$stmt2 = mysqli_prepare($link, $sql2);
mysqli_stmt_bind_param($stmt2, "s", $param_id);
mysqli_stmt_execute($stmt2);
$patient_result = mysqli_stmt_get_result($stmt2);



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
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to your caretaker portal.</h1>
    </div>
    <div class="wrapper">
    <div>
        <?php
        echo "<table class='table'>";
        echo "<h3 class='table-header'>Your Patients</h2>";
        while($rowitem = mysqli_fetch_array($patient_result)) {
            echo "<tr>";
            echo "<th> Created At</th>";
            echo "<th> First Name</th>";
            echo "<th> Last Name</th>";
            echo "<th> </th>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>" . $rowitem['created_at'] . "</td>";
            echo "<td>" . $rowitem['first_name'] . "</td>";
            echo "<td>" . $rowitem['last_name'] . "</td>";
            echo "<td><form action='caretaker-home.php' method='post'><input type='hidden' name='pusername' value=" .$rowitem['username'] . "><button type='submit' class='btn btn-default' name='visit' id='visit' value=" .$rowitem['id'] .">hi</button></td>";
        }
        echo "</table>";
        ?>
    </div>
    </div>
</div>
    <p>
        <a href="welcome.php" type="button" class="btn btn-default">Home</a>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
</body>
</html>