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
      TABLE_NAME
      FROM information_schema.tables
      WHERE table_name LIKE concat('%',:query,'%')
      AND TABLE_TYPE = 'VIEW'";

    $query= $_POST['query'];

    $statement = $connection->prepare($sql);
    $statement->bindParam(':query', $query, PDO::PARAM_STR);
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
  <th>Table Name</th>
</tr>
      </thead>
      <tbody>
  <?php foreach ($result as $row) { ?>
      <tr>
<td><a href="user.php?u=<?=$row["user_id"]?>"><?php echo escape($row["user_name"]); ?></a></td>
<td><?php echo escape($row["TABLE_NAME"]); ?></td>
      </tr>
    <?php } ?>
      </tbody>
  </table>
  <?php } else { ?>
    > No results found for <?php echo escape($_POST['location']); ?>.
  <?php }
} ?>

<h2>View Query</h2>

<form method="post">
  <label for="query">Query</label>
  <input type="text" id="query" name="query">
  <input type="submit" name="submit" value="View Results">
</form>
<br>
<a href="index.php">Back to home</a>

<?php require "templates/footer.php"; ?>
