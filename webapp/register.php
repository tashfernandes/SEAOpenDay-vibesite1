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

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST["username"]);
    $pass = trim($_POST["password"]);
    $confirm = trim($_POST["confirm_password"]);

    if (!empty($user) && !empty($pass) && !empty($confirm)) {
        if ($pass === $confirm) {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Username already taken. Try another one.";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

                $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $insert->bind_param("ss", $user, $hashed_password);

                if ($insert->execute()) {
                    $success = "Registration successful! You can now <a href='login.php'>log in</a>.";
                } else {
                    $error = "Something went wrong. Please try again.";
                }

                $insert->close();
            }

            $stmt->close();
        } else {
            $error = "Passwords do not match.";
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
    <title>Register</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-box {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
            width: 300px;
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
        .success {
            color: green;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="register-box">
    <h2>Register</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>
    <form method="POST" action="">
        <label>Username</label>
        <input type="text" name="username" placeholder="Choose a username" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm password" required>

        <button type="submit">Register</button>
    </form>
    <p style="text-align:center; margin-top:10px;">
        Already have an account? <a href="login.php">Login</a>
    </p>
</div>
</body>
</html>

