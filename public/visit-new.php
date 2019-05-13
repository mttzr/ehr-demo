<?php
// Initialize the session
session_start();

// Include config file
require_once "../config.php";
 
// Define variables and initialize with empty values

$id = uniqid();
$created_at = date("y-m-d");
$updated_at = date("y-m-d");
$media_id = uniqid();

// Prepare a select statement
$sql = "SELECT id as location_id, name FROM locations";

$result = mysqli_query($link, $sql);  

//echo "<pre>"; 
 //  print_r($_FILES); 
  // echo "</pre>"; 

//echo '<pre>';
//var_dump($_SESSION);
//echo '</pre>'; 

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
        // Prepare an insert statement
        $sql = "INSERT INTO visits (id, patient_id, provider_id, location_id, type, symptoms, start_date, end_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssssss", $param_id, $param_patient_id, $param_provider_id, $param_location_id, $param_type, $param_symptoms, $param_start_date, $param_end_date, $param_created_at, $param_updated_at);
            
            // Set parameters
            $param_id = $id;
            $param_patient_id = $_SESSION["pid"];
            $param_provider_id = $_SESSION["provider_id"];
            $param_location_id = 1;
            //$param_location_id = trim($_POST["location_id"]);
            $param_type = trim($_POST["type"]);
            $param_symptoms = trim($_POST["symptoms"]);
            $param_start_date = trim($_POST["start"]);
            $param_end_date = trim($_POST["end"]);
            $param_created_at = $created_at;
            $param_updated_at = $updated_at;

            }

        $sql_media = "INSERT INTO visit_media (id, meta_data, data, visit_id) VALUES (?, ?, ?, ?)";

        if($stmt_media = mysqli_prepare($link, $sql_media)){
            mysqli_stmt_bind_param($stmt_media, "ssss", $media_id, $param_media_meta, $param_media_data, $id);
        } 
         
        $param_media_data = file_get_contents($_FILES['userImage']['tmp_name']);
        $param_media_meta = getimageSize($_FILES['userImage']['tmp_name']);

              // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                if(mysqli_stmt_execute($stmt_media)){
                    $_SESSION["visit_id"] = $id;
                    header("location: visit.php");
                }
                else{
                echo "Something went wrong with media upload. Please try again later.";
                //print_r($param_location_id);
                print_r($stmt_media); 
                }
                // Redirect to login page
                //header("location: login.php");
                //echo "It worked!";
                //print_r($stmt);
               //echo file_get_contents($_FILES['userImage']['tmp_name']);
 
            } else{
                echo "Something went wrong. Please try again later.";
                //print_r($param_location_id);
                print_r($stmt);
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
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>New Visit</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <div class="form-group">
                <label>Location</label>
                <select name="Location">
                <option>---Select Location---</option>
                <?php while($providerData = mysqli_fetch_array($result)) { ?>
                <option value ="<?php echo $location_id;?>"> <?php echo $providerData['name'];?>
            </option>
        <?php }?>
        </select value ="<?php echo $provider_type_id;?>">
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
                <input type="date" name="start" class="form-control" value="<?php echo $start;?>">
            </div>
                <label>End</label>
                <input type="date" name="end" class="form-control" value="<?php echo $end;?>">
            </div>    
            <div>
            <input type="file" name="userImage" class = "inputFile" id = "userImage">
            </div>
            <br>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <a href="./provider-home.php" class="btn btn-default" >Back</a>
        </form>
    </div>    
</body>
</html>