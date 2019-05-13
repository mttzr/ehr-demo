<?php

/**
  * Function to query information based on
  * a parameter: in this case, location.
  *
  */

if (isset($_POST['submit'])) {
  try {
    require "../config.php";
    require "../common.php";

    $connection = new PDO($dsn, $username, $password, $options);

    $sql = "SELECT 

    records.license_plate as license_plate,
	users.name as user_name,
	users.id as user_id,
	valets.name as valet_name

    FROM records
    JOIN users ON users.id = records.user_id
    JOIN valets ON valets.id = records.valet_id
    WHERE records.license_plate LIKE concat('%',:license_plate,'%')";

    $license_plate = $_POST['license_plate'];

    $statement = $connection->prepare($sql);
    $statement->bindParam(':license_plate', $license_plate, PDO::PARAM_STR);
    $statement->execute();

    $result = $statement->fetchAll();
  } catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
  }
}
?>
<?php require "templates/header.php"; ?>

<?php
if (isset($_POST['submit'])) {
  if ($result && $statement->rowCount() > 0) { ?>
    <h2>Results</h2>

    <table>
      <thead>
<tr>
  <th>User ID</th>
  <th>Valet ID</th>
  <th>License Plate</th>
</tr>
      </thead>
      <tbody>
  <?php foreach ($result as $row) { ?>
      <tr>
<td><a href="user.php?u=<?=$row["user_id"]?>"><?php echo escape($row["user_name"]); ?></a></td>
<td><?php echo escape($row["valet_name"]); ?></td>
<td><?php echo escape($row["license_plate"]); ?></td>
      </tr>
    <?php } ?>
      </tbody>
  </table>
  <?php } else { ?>
    > No results found for <?php echo escape($_POST['location']); ?>.
  <?php }
} ?>

<h2>License Plate Lookup</h2>

<form method="post">
  <label for="license_plate">License Plate</label>
  <input type="text" id="license_plate" name="license_plate">
  <input type="submit" name="submit" value="View Results">
</form>
<br>
<a href="index.php">Back to home</a>

<?php require "templates/footer.php"; ?>
