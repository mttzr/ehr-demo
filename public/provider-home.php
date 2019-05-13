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
$sql = "SELECT id as provider_id FROM providers WHERE user_id = ?";
$param_id = $_SESSION["id"];
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $provider_id);
mysqli_stmt_store_result($stmt);

if(mysqli_stmt_num_rows($stmt) == 0){
    header("location: provider-signup.php");
    exit;
} else {
    mysqli_stmt_fetch($stmt);
    $_SESSION["provider_id"] = $provider_id;
}

//view patient and change session
if(array_key_exists('view', $_POST))
{
    $patient_id = trim($_POST["view"]);
    $patient_username = trim($_POST["pusername"]);
    $_SESSION["pid"] = $patient_id;
    $_SESSION["pusername"] = $patient_username;  
    header("location: patient-home-provider-view.php");
}

//New visit patient and change session
if(array_key_exists('visit', $_POST))
{
    $patient_id = trim($_POST["visit"]);
    $patient_username = trim($_POST["pusername"]);
    $_SESSION["pid"] = $patient_id;
    $_SESSION["pusername"] = $patient_username;  
    header("location: visit-new.php");
}

//View visit details
if(array_key_exists('viewv', $_POST))
{
    $visit_id = trim($_POST["viewv"]);
    $_SESSION["visit_id"] = $visit_id;
    $_SESSION["pid"] = $patient_id;
    $_SESSION["pusername"] = $patient_username;  
    header("location: visit.php");
}

// Query for visits table
$sql2 = "SELECT visits.created_at, users.first_name, users.last_name, visits.id FROM visits JOIN patients ON patients.id = visits.patient_id JOIN users ON users.id = patients.user_id JOIN providers ON providers.id = visits.provider_id JOIN users AS users2 ON users2.id = providers.user_id WHERE users2.id = ?";
$stmt2 = mysqli_prepare($link, $sql2);
mysqli_stmt_bind_param($stmt2, "s", $param_id);
mysqli_stmt_execute($stmt2);
$visit_result = mysqli_stmt_get_result($stmt2);

// Query for patients table
$sql5 = "SELECT
users.first_name,
users.username,
users.last_name,
provider_patient.patient_id as id,
provider_patient.created_at
FROM provider_patient
JOIN patients ON patients.id = provider_patient.patient_id
JOIN providers ON providers.id = provider_patient.provider_id
JOIN users ON users.id = patients.user_id
JOIN users AS users_providers ON users_providers.id = providers.user_id
WHERE users_providers.id = ?
;";
$stmt5 = mysqli_prepare($link, $sql5);
mysqli_stmt_bind_param($stmt5, "s", $param_id);
mysqli_stmt_execute($stmt5);
$provider_result = mysqli_stmt_get_result($stmt5);

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
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to your provider portal.</h1>
    </div>
    <div class="wrapper">
    <div>
        <?php
        echo "<table class='table'>";
        echo "<h3 class='table-header'>Your Visits</h2>";
                 echo "<tr>";
            echo "<th> Created At</th>";
            echo "<th> First Name</th>";
            echo "<th> Last Name</th>";
            echo "<th></th>";
            echo "</tr>";
        while($rowitem = mysqli_fetch_array($visit_result)) {
            echo "<tr>";
            echo "<td>" . $rowitem['created_at'] . "</td>";
            echo "<td>" . $rowitem['first_name'] . "</td>";
            echo "<td>" . $rowitem['last_name'] . "</td>";
            echo "<td><form action='provider-home.php' method='post'><button type='submit' class='btn btn-default' name='viewv' id='viewv' value=" .$rowitem['id'] .">View</button></td>";

        }
        echo "</table>";
        ?>
    </div>
    <div>
        <?php
        echo "<table class='table'>";
        echo "<h3 class='table-header'>Your Patients</h3>";
            echo "<tr>";
            echo "<th> First Name</th>";
            echo "<th> Last Name </th>";
            echo "<th> Since </th>";
            echo "<th></th>";
            echo "<th></th>";
            echo "</tr>";
        while($rowitem = mysqli_fetch_array($provider_result)) {
            echo "<tr>";
            echo "<td>" . $rowitem['first_name'] . "</td>";
            echo "<td>" . $rowitem['last_name'] . "</td>";
            echo "<td>" . $rowitem['created_at'] . "</td>";
            echo "<td><form action='provider-home.php' method='post'><input type='hidden' name='pusername' value=" .$rowitem['username'] . "><button type='submit' class='btn btn-default' name='view' id='view' value=" .$rowitem['id'] .">View</button></td>";
            echo "<td><form action='provider-home.php' method='post'><input type='hidden' name='pusername' value=" .$rowitem['username'] . "><button type='submit' class='btn btn-default' name='visit' id='visit' value=" .$rowitem['id'] .">New Visit</button></td>";

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