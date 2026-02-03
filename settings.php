<?php
session_start();

// تأكد إن المستخدم مسجل دخول
if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit();
}

// اتصال مباشر بالقاعدة
$host = "localhost";
$user = "root";
$pass = "123456";
$db   = "library";

$conn = mysqli_connect($host, $user, $pass, $db);
if(!$conn){
    die("DB Connection Failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// ====================
// حذف الحساب
if(isset($_POST['delete_account'])){
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        session_destroy();
        header("Location: login.php?msg=account_deleted");
        exit;
    } else {
        $error = "❌ Failed to delete account.";
    }
}

// ====================
// تحديث البيانات
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_settings'])){

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // تحديث الاسم والايميل
    $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, email = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $user_id);

    if(mysqli_stmt_execute($stmt)){
        $success = "✅ Profile updated successfully!";
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
    } else {
        $error = "❌ Failed to update profile.";
    }

    // تغيير كلمة المرور
    if(!empty($_POST['password']) && !empty($_POST['confirm_password'])){
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if($password === $confirm){
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $password_hash, $user_id);
            mysqli_stmt_execute($stmt);
            $success .= " Password changed successfully!";
        } else {
            $error .= " Passwords do not match!";
        }
    }
}

// ====================
// جلب بيانات المستخدم الحالية
$stmt = mysqli_prepare($conn, "SELECT name, email FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Settings</title>
<style>
/* --- Body & Background --- */
body {
    margin:0;
    padding:0;
    font-family: 'Arial', sans-serif;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:flex-start;
    background-image: url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1600&q=80');
    background-size: cover;
    background-position: center;
}

/* --- Container --- */
.settings-container {
    background: rgba(255,255,255,0.95);
    margin:50px auto;
    padding: 30px 40px;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    width: 400px;
    animation: fadeIn 1s ease;
}

/* --- Headings --- */
h2, h3 { text-align:center; color:#007bff; }

/* --- Inputs & Buttons --- */
input[type="text"],
input[type="email"],
input[type="password"] {
    width:100%;
    padding:12px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:15px;
}

input:focus { outline:none; border-color:#007bff; }

button {
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
    margin-top:15px;
    transition:0.3s;
}

button.update { background:#007bff; color:white; }
button.update:hover { background:#0056b3; }

button.delete { background:#e74c3c; color:white; }
button.delete:hover { background:#c0392b; }

/* --- Messages --- */
.success { color:green; font-weight:bold; text-align:center; margin:10px 0; }
.error { color:red; font-weight:bold; text-align:center; margin:10px 0; }

/* --- Links --- */
a.back-link {
    display:block;
    text-align:center;
    margin-top:15px;
    color:#007bff;
    text-decoration:none;
}
a.back-link:hover { text-decoration:underline; }

/* --- Animation --- */
@keyframes fadeIn {
    from {opacity:0; transform: translateY(-20px);}
    to {opacity:1; transform: translateY(0);}
}
</style>
</head>
<body>

<div class="settings-container">
    <h2>Account Settings</h2>

    <?php
    if(!empty($error)) echo "<div class='error'>$error</div>";
    if(!empty($success)) echo "<div class='success'>$success</div>";
    ?>

    <!-- Update Profile -->
    <form method="post">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

        <hr>
        <h3>Change Password</h3>
        <label>New Password:</label>
        <input type="password" name="password">

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password">

        <button type="submit" name="update_settings" class="update">Update Settings</button>
    </form>

    <hr>

    <!-- Delete Account -->
    <h3>Delete Account</h3>
    <p style="color:red; text-align:center;">⚠️ This action is irreversible!</p>
    <form method="post" onsubmit="return confirm('Are you sure you want to delete your account?');">
        <button type="submit" name="delete_account" class="delete">Delete My Account</button>
    </form>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>