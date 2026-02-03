<?php
session_start();

// إذا تم التأكيد بالخروج
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // مسح الجلسة
    $_SESSION = [];
    session_destroy();

    // مسح كوكيز الجلسة
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // إعادة التوجيه
    header("Location: login.php");
    exit;
}

// إذا ضغط Cancel نرجع للصفحة الرئيسية
if (isset($_GET['confirm']) && $_GET['confirm'] === 'no') {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Confirm Logout</title>
<style>
body {
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family: Arial, sans-serif;
    background-image: url('https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1600&q=80');
    background-size: cover;
    background-position: center;
}

.confirm-box {
    text-align:center;
    background: rgba(255,255,255,0.95);
    padding: 40px 60px;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

h1 { margin-bottom: 20px; color: #007bff; }
p { font-size: 18px; color: #333; margin-bottom: 30px; }

.btn {
    padding: 10px 25px;
    margin: 0 10px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-weight: bold;
    transition: 0.2s;
}
.btn-yes { background: #007bff; color: white; }
.btn-yes:hover { opacity: 0.9; }
.btn-no { background: #ccc; color: black; }
.btn-no:hover { opacity: 0.9; }
</style>
</head>
<body>

<div class="confirm-box">
    <h1>👋 Confirm Logout</h1>
    <p>Are you sure you want to log out?</p>
    <a href="?confirm=yes"><button class="btn btn-yes">Yes</button></a>
    <a href="?confirm=no"><button class="btn btn-no">Cancel</button></a>
</div>

</body>
</html>
