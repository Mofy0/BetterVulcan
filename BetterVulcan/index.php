<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <div id="login" style="width: 20%; margin: 40px auto;">
    <img src="person.svg" alt="logo">
    <form action="login.php" method="post">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required > </input><br>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required ><br>

      <label for="role">Role:</label>
      <select id="role" name="role" required>
          <option value="students">Student</option>
          <option value="teachers">Teacher</option>
          <option value="admins">Admin</option>
      </select><br>

      <input type="submit" value="Login">
    </form>
    </div>
    <!-- Display error message if exists -->
    <?php
    if (isset($_GET['error'])) {
        echo "<p style='color: red;'>Invalid username or password</p>";
    }
    ?>
</body>
</html>