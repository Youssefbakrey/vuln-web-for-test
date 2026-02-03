<?php
session_start();
$error = ""; // تعريف متغير الخطأ

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // اتصال بقاعدة البيانات
    $connection = mysqli_connect('localhost','root','123456','library');
    if(!$connection){
        die("DB Connection Failed: " . mysqli_connect_error());
    }

    // Prepared statement لتجنب SQL Injection
    $stmt = mysqli_prepare($connection, "SELECT id, name, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if($user && password_verify($password, $user['password'])){
        // تسجيل الدخول
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $email;

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "❌ Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Library</title>
<style>
/* --- BODY --- */
body {
    margin:0;
    padding:0;
    height:100vh;
    font-family: 'Arial', sans-serif;
    display:flex;
    justify-content:center;
    align-items:center;
    background-image: url('https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1600&q=80');
    background-size: cover;
    background-position: center;
}

/* --- CONTAINER --- */
.login-box {
    background: rgba(255,255,255,0.95);
    padding: 40px 50px;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    width: 350px;
    text-align:center;
    animation: fadeIn 1s ease;
}

h2 {
    margin-bottom: 25px;
    color: #007bff;
}

/* --- INPUTS --- */
input[type="email"],
input[type="password"] {
    width:100%;
    padding:12px 15px;
    margin:10px 0;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:16px;
}

input[type="email"]:focus,
input[type="password"]:focus {
    outline:none;
    border-color:#007bff;
}

/* --- BUTTON --- */
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

button:hover {
    background:#0056b3;
}

/* --- ERROR MESSAGE --- */
.error-msg {
    color:red;
    font-weight:bold;
    margin-bottom:15px;
}

/* --- REGISTER LINK --- */
.register-link {
    margin-top:15px;
    display:block;
    text-decoration:none;
    color:#007bff;
    font-weight:bold;
}
.register-link:hover {
    text-decoration:underline;
}

/* --- Animations --- */
@keyframes fadeIn {
    from {opacity:0; transform: translateY(-20px);}
    to {opacity:1; transform: translateY(0);}
}
</style>
</head>
<body>

<div class="login-box">
    <h2>Login to Library</h2>
    <?php if (!empty($error)) echo "<div class='error-msg'>$error</div>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <a href="register.php" class="register-link">Don't have an account? Register</a>
</div>

</body>
</html>
