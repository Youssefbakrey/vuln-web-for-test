<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// حماية الصفحة
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<style>
/* --- BODY & Animated Background --- */
body {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: Arial, sans-serif;

    /* Animated gradient background */
    background: linear-gradient(-45deg, #1e3c72, #2a5298, #6dd5ed, #2193b0);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
}

/* --- Container --- */
.container {
    background: rgba(255,255,255,0.95); /* شفافية */
    padding: 30px 50px;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    text-align: center;
}

/* --- Headings & Text --- */
h1 { margin-bottom: 20px; }
ul { list-style: none; padding: 0; }
li { margin: 10px 0; }
a {
    text-decoration: none;
    color: #007bff;
    font-weight: bold;
    transition: 0.3s;
}
a:hover { color: #0056b3; }

/* --- Animation --- */
@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
</style>
</head>

<body>

<div class="container">
    <h1>Dashboard</h1>
    <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></p>

    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="settings.php">Settings</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

</body>
</html>