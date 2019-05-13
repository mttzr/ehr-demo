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

// Check and make sure this person is a patient
$sql = "SELECT id FROM patients WHERE user_id = ?";
$param_id = $_SESSION["id"];
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $param_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if(mysqli_stmt_num_rows($stmt) == 0){
    header("location: patient-signup.php");
    exit;
} else {
    mysqli_stmt_bind_result($stmt, $patient_id);
    $_SESSION["patient_id"] = $patient_id;
}

// Query for visits table
$sql2 = "SELECT visits.created_at, users2.first_name, users2.last_name FROM visits JOIN patients ON patients.id = visits.patient_id JOIN users ON users.id = patients.user_id JOIN providers ON providers.id = visits.provider_id JOIN users AS users2 ON users2.id = providers.user_id WHERE users.id = ?";
$stmt2 = mysqli_prepare($link, $sql2);
mysqli_stmt_bind_param($stmt2, "s", $param_id);
mysqli_stmt_execute($stmt2);
$visit_result = mysqli_stmt_get_result($stmt2);

// Query for tests table
$sql3 = "SELECT lab_test_orders.created_at, lab_tests.test_name, lab_test_results.results FROM lab_test_orders LEFT JOIN lab_test_results ON lab_test_results.test_order_id = lab_test_orders.id JOIN patients ON patients.id = lab_test_orders.patient_id JOIN users ON users.id = patients.user_id JOIN lab_tests ON lab_tests.id = lab_test_orders.lab_test_id WHERE users.id = ?";
$stmt3 = mysqli_prepare($link, $sql3);
mysqli_stmt_bind_param($stmt3, "s", $param_id);
mysqli_stmt_execute($stmt3);
$test_result = mysqli_stmt_get_result($stmt3);

// Query for providers table
$sql4 = "SELECT 
provider_users.first_name,
provider_users.last_name,
provider_type.name,
provider_patient.created_at
FROM provider_patient
JOIN patients ON patients.id = provider_patient.patient_id
JOIN users ON users.id = patients.user_id
JOIN providers ON providers.id = provider_patient.provider_id
JOIN users AS provider_users ON provider_users.id = providers.user_id
JOIN provider_type ON provider_type.id = providers.provider_type_id
WHERE users.id = ?
AND provider_patient.status = 'Active'
;";
$stmt4 = mysqli_prepare($link, $sql4);
mysqli_stmt_bind_param($stmt4, "s", $param_id);
mysqli_stmt_execute($stmt4);
$provider_result = mysqli_stmt_get_result($stmt4);

// Query for caretakers table
$sql5 = "SELECT
users_caretaker.first_name,
users_caretaker.last_name,
caretaker_patient.created_at
FROM caretaker_patient
JOIN patients ON patients.id = caretaker_patient.patient_id
JOIN caretakers ON caretakers.id = caretaker_patient.caretaker_id
JOIN users ON users.id = patients.user_id
JOIN users AS users_caretaker ON users_caretaker.id = caretakers.user_id
WHERE users.id = ?
;";
$stmt5 = mysqli_prepare($link, $sql5);
mysqli_stmt_bind_param($stmt5, "s", $param_id);
mysqli_stmt_execute($stmt5);
$caretaker_result = mysqli_stmt_get_result($stmt5);

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
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to your patient portal.</h1>
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
            echo "</tr>";
        while($rowitem = mysqli_fetch_array($visit_result)) {
            echo "<tr>";
            echo "<td>" . $rowitem['created_at'] . "</td>";
            echo "<td>" . $rowitem['first_name'] . "</td>";
            echo "<td>" . $rowitem['last_name'] . "</td>";

        }
        echo "</table>";
        ?>
    </div>
    <div>
        <?php
        echo "<table class='table'>";
        echo "<h3 class='table-header'>Your Lab Tests</h3>";
            echo "<tr>";
            echo "<th> Created At</th>";
            echo "<th> Test</th>";
            echo "<th> Result</th>";
            echo "</tr>";
        while($rowitem = mysqli_fetch_array($test_result)) {
            echo "<tr>";
            echo "<td>" . $rowitem['created_at'] . "</td>";
            echo "<td>" . $rowitem['test_name'] . "</td>";
            echo "<td>" . $rowitem['results'] . "</td>";
        }
        echo "</table>";
        ?>
    </div>
    <div>
        <?php
        echo "<table class='table'>";
        echo "<div class = 'table-header-div'>";
        echo "<h3 class='table-header'>Your Providers</h3>";
        echo "<a href='provider-search.php' class='table-button btn btn-default'>Add</a>";
        echo "</div>";
        echo "<tr>";
        echo "<th> First Name</th>";
        echo "<th> Last Name </th>";
        echo "<th> Type </th>";
        echo "<th> Since </th>";
        echo "</tr>";
        while($rowitem = mysqli_fetch_array($provider_result)) {
            echo "<tr>";
            echo "<td>" . $rowitem['first_name'] . "</td>";
            echo "<td>" . $rowitem['last_name'] . "</td>";
            echo "<td>" . $rowitem['name'] . "</td>";
            echo "<td>" . $rowitem['created_at'] . "</td>";

        }
        echo "</table>";
        ?>
    </div>
    <div>
        <?php
        echo "<table class='table'>";
        echo "<div class = 'table-header-div'>";
        echo "<h3 class='table-header'>Your Caretakers</h3>";
        echo "<a href='caretaker-search.php' class='table-button btn btn-default'>Add</a>";
        echo "</div>";
            echo "<tr>";
            echo "<th> First Name</th>";
            echo "<th> Last Name</th>";
            echo "<th> Since</th>";
            echo "</tr>";
        while($rowitem = mysqli_fetch_array($caretaker_result)) {
            echo "<tr>";
            echo "<td>" . $rowitem['first_name'] . "</td>";
            echo "<td>" . $rowitem['last_name'] . "</td>";
            echo "<td>" . $rowitem['created_at'] . "</td>";
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