<?php
// Initialize the session
session_start();

// Include config file
require_once "../config.php";


    //echo '<pre>';
    //var_dump($_SESSION);
    //echo '</pre>';
 
// Variables
$patient_id = "";
$type = "";
$symptoms = "";
$start_date = "";
$end_date = "";

// Prepare a select statement
$visit_id = $_SESSION["visit_id"];
$sql = "SELECT patient_id, location_id, type, symptoms, start_date, end_date FROM visits WHERE id = '$visit_id'";

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $patient_id, $location_id, $type, $symptoms, $start_date, $end_date);
mysqli_stmt_fetch($stmt);
 // Close statement
mysqli_stmt_close($stmt);  
    // Close connection
//mysqli_close($link); 


$sql_media = "SELECT data FROM visit_media WHERE visit_id='$visit_id'" ;
$stmt_media = mysqli_prepare($link, $sql_media);
mysqli_stmt_execute($stmt_media);
$img_data = mysqli_stmt_get_result($stmt_media);

 // Close statement
mysqli_stmt_close($stmt_media);  
    // Close connection
//mysqli_close($link); 

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
        // Prepare an insert statement
       $sql2 = "UPDATE visits SET location_id = ?, type = ?, symptoms = ?, start_date = ?, end_date = ?, updated_at = ? WHERE id = '$visit_id'"; 
       
        if($stmt2 = mysqli_prepare($link, $sql2)){

            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt2, "ssssss", $param_location_id, $param_type, $param_symptoms, $param_start_date, $param_end_date, $param_updated_at);
            
            // Set parameters
            $param_location_id = "1";
            //$param_location_id = trim($_POST["location_id"]);
            $param_type = trim($_POST["type"]);
            $param_symptoms = trim($_POST["symptoms"]);
            $param_start_date = trim($_POST["start"]);
            $param_end_date = trim($_POST["end"]);
            $param_updated_at = date("y-m-d");
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt2)){
                // Redirect to login page
                header("location: visit.php");
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


     /*
        if($stmt2 = mysqli_prepare($link, $sql2)){
     

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt2)){
                // Redirect to login page
                //header("location: login.php");
             //   echo "It worked!";
                print_r($stmt2);
               // $_SESSION["visit_id"] = $id;
                header("location: visit.php");
            } else{
                echo "Something went wrong. Please try again later.";
                //print_r($param_location_id);
                print_r($stmt2);
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt2);
    
    // Close connection
    mysqli_close($link); 
}*/
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Visit Details</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <div class="form-group">
                <label>Location</label>
                <input type="text" name="type" class="form-control" value="<?php echo $location_id;?>">
            </div>
             <div class="form-group">
                <label>Visit Type</label>
                <input type="text" name="type" class="form-control" value="<?php echo $type;?>">
            </div>
            <div class="form-group">
                <label>Symptoms</label>
                <input type="text" name="symptoms" class="form-control" value="<?php echo $symptoms;?>">
            </div>
            <div class="form-group">
                <label>Start</label>
                <input type="date" name="start" class="form-control" value="<?php echo $start_date;?>">
            </div>
                <label>End</label>
                <input type="date" name="end" class="form-control" value="<?php echo $end_date;?>">
            </div>    
            <div>
                <?php while($rowitem = mysqli_fetch_array($img_data)){
                    echo "<img src='data:image/png;base64,". base64_encode($rowitem['data'])."'/>";
                }
                    ?>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Update">
            </div>
            <a href="./provider-home.php" class="btn btn-default" >Back</a>
        </form>
    </div>    
</body>
</html>