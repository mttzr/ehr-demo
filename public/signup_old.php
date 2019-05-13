<?php

/**
  * Use an HTML form to create a new entry in the
  * users table.
  *
  */


if (isset($_POST['submit'])) {
  require "../config.php";
  require "../common.php";

  try {
    $connection = new PDO($dsn, $username, $password, $options);

    $new_user = array(
      "id" => $_POST['id'],
      "first_name" => $_POST['first_name'],
      "last_name"  => $_POST['last_name'],
      "middle_name" => $_POST['middle_name'],
      "email"     => $_POST['email'],
      "username"       => $_POST['username'],
      "password"  => $_POST['password'],
      "created_at" => $_POST['updated_at'],
      "updated_at" => $_POST['updated_at']
    );

    $sql = sprintf(
        "INSERT INTO %s (%s) values (%s)",
        "users",
        implode(", ", array_keys($new_user)),
        ":" . implode(", :", array_keys($new_user))
    );

    $statement = $connection->prepare($sql);
    $statement->execute($new_user);
  } catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
  }
}
?>

<?php require "templates/header.php"; ?>

<?php if (isset($_POST['submit']) && $statement) { ?>
  > <?php echo $_POST['firstname']; ?> successfully added.
<?php } ?>

<h2>Add a user</h2>

<form method="post">
  <label for="id">ID</label>
  <input type="text" name="id" id="id">
  <label for="first_name">First Name</label>
  <input type="text" name="first_name" id="first_name">
  <label for="last_name">Last Name</label>
  <input type="text" name="last_name" id="last_name">
  <label for="middle_name">Middle Name</label>
  <input type="text" name="middle_name" id="middle_name">
  <label for="email">Email Address</label>
  <input type="text" name="email" id="email">
  <label for="username">Username</label>
  <input type="text" name="username" id="username">
  <label for="password">Password</label>
  <input type="text" name="password" id="password">
  <label for="created_at">Created At</label>
  <input type="date" name="created_at" id="created_at">
  <label for="updated_at">Updated At</label>
  <input type="date" name="updated_at" id="updated_at">
  <input type="submit" name="submit" value="Submit">
</form>

<a href="index.php">Back to home</a>

<?php require "templates/footer.php"; ?>