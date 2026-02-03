<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* DB Connection */
$conn = mysqli_connect("localhost", "root", "123456", "library");
if (!$conn) {
    die("DB Connection Failed: " . mysqli_connect_error());
}

/* استقبال الفورم */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        die("Passwords do not match");
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    /* Prepared Statement */
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: login.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Library</title>
<style>
body {
    margin:0;
    padding:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family: Arial, sans-serif;
    background-image: url('https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1600&q=80');
    background-size: cover;
    background-position: center;
}

.register-box {
    background: rgba(255,255,255,0.95);
    padding: 40px 50px;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    width: 350px;
    text-align:center;
    animation: fadeIn 1s ease;
}

h2 { margin-bottom: 25px; color:#007bff; }

input[type="text"], input[type="email"], input[type="password"] {
    width:100%;
    padding:12px 15px;
    margin:10px 0;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:16px;
}

input:focus { outline:none; border-color:#007bff; }

button {
    width:100%;
    padding:12px;
    background:#007bff;
    color:white;
    font-size:16px;
    font-weight:bold;
    border:none;
    border-radius:8px;
    cursor:pointer;
    margin-top:15px;
    transition:0.3s;
}

button:hover { background:#0056b3; }

.error-msg { color:red; font-weight:bold; margin-bottom:15px; }

.login-link {
    margin-top:15px;
    display:block;
    text-decoration:none;
    color:#007bff;
    font-weight:bold;
}
.login-link:hover { text-decoration:underline; }

@keyframes fadeIn {
    from {opacity:0; transform: translateY(-20px);}
    to {opacity:1; transform: translateY(0);}
}
</style>
</head>
<body>

<div class="register-box">
    <h2>Create an Account</h2>
    <?php if(!empty($error)) echo "<div class='error-msg'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>
    <a href="login.php" class="login-link">Already have an account? Login</a>
</div>

</body>
</html>
