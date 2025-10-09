<?php
session_start();

// Replace with your own database credentials
$host = "127.0.0.1";
$dbname = "injection";
$username = "admin";
$password = "password";

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST["username"]);
    $pass = trim($_POST["password"]);

    if (!empty($user) && !empty($pass)) {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->query("SELECT id, password FROM users WHERE username = '$user'");
        if ($stmt->num_rows > 0) {
            $row = $stmt->fetch_row();
            $hashed_password = $row[1];
            $id = $row[0];

            if (password_verify($pass, $hashed_password)) {
                // Success: set session and redirect
                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = $user;
                header("Location: welcome.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
            $stmt->free_result();
        } else {
            $error = "User not found.";
        }

    } else {
        $error = "Please fill in all fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>

