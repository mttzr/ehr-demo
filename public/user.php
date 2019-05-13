<?php

/**
  * Function to query information based on
  * a parameter: in this case, location.
  *
  */

if (isset($_GET["u"])) {
  try {
    require "../config.php";
    require "../common.php";

    $connection = new PDO($dsn, $username, $password, $options);

    $sql = "SELECT 
      records.license_plate as license_plate,
      users.name as user_name,
      parking_lots.name as parking_name,
      records.enter_time as enter_time,
      records.exit_time as exit_time
  
    FROM records
    JOIN users ON users.id = records.user_id
    JOIN parking_lots ON parking_lots.id = records.parking_id
    WHERE users.id = :user_id";

    $user_id = htmlspecialchars($_GET["u"]);

    $statement = $connection->prepare($sql);
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $statement->execute();

    $result = $statement->fetchAll();
  } catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
  }
}
?>

<?php require "templates/header.php"; ?>

<?php
if (isset($_GET["u"])) {
  if ($result && $statement->rowCount() > 0) { ?>
    <h2>User ID: <?php echo htmlspecialchars($_GET["u"]) ?></h2>

    <table>
      <thead>
<tr>
  <th>User Name</th>
  <th>Parking Lot Name</th>
  <th>License Plate</th>
  <th>Enter Time</th>
  <th>Exit Time</th>
</tr>
      </thead>
      <tbody>
  <?php foreach ($result as $row) { ?>
      <tr>
<td><?php echo escape($row["user_name"]); ?></td>
<td><?php echo escape($row["parking_name"]); ?></td>
<td><?php echo escape($row["license_plate"]); ?></td>
<td><?php echo escape($row["enter_time"]); ?></td>
<td><?php echo escape($row["exit_time"]); ?></td>
      </tr>
    <?php } ?>
      </tbody>
  </table>
  <?php } else { ?>
    > No results found for <?php echo escape($_POST['location']); ?>.
  <?php }
} ?>
<br>
<a href="index.php">Back to home</a>

<?php require "templates/footer.php"; ?>
