<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h1>Hello, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
    <p>You've successfully logged in.</p>
    <a href="logout.php">Logout</a>
</body>
</html>

