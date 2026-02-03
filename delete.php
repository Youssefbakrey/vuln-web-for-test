<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// DB Connection
$conn = mysqli_connect("localhost", "root", "123456", "library");
if (!$conn) die("DB Connection Failed: " . mysqli_connect_error());

// التحقق من صلاحية Admin
$user_email = $_SESSION['email'];
$res = mysqli_query($conn, "SELECT admin FROM users WHERE email = '".mysqli_real_escape_string($conn, $user_email)."'");
$row = mysqli_fetch_assoc($res);

if (!$row || $row['admin'] != 1) {
    die("❌ Access Denied: Admins Only");
}

// -------------------
// حذف User
if (isset($_GET['delete_user'])) {
    $id = (int)$_GET['delete_user'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    $msg = "✅ User deleted successfully";
    header("Location: delete.php"); // إعادة تحميل الصفحة
    exit;
}

// حذف Book
if (isset($_GET['delete_book'])) {
    $id = (int)$_GET['delete_book'];
    mysqli_query($conn, "DELETE FROM books WHERE id = $id");
    $msg = "✅ Book deleted successfully";
    header("Location: delete.php"); // إعادة تحميل الصفحة
    exit;
}

// جلب Users و Books للعرض
$users = mysqli_query($conn, "SELECT * FROM users");
$books = mysqli_query($conn, "SELECT * FROM books");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Delete Panel</title>

<style>
/* --- BODY & BACKGROUND --- */
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:Arial, sans-serif;

    background-image: url("https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1600&q=80");
    background-size: cover;
    background-position: center;
}

/* --- CONTAINER --- */
.container{
    width:90%;
    max-width:900px;
    background: rgba(255,255,255,0.95);
    padding:25px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,.4);
}

/* --- HEADINGS --- */
h1{text-align:center; margin-bottom:15px}

/* --- TABS --- */
.tabs{
    display:flex;
    justify-content:center;
    margin:20px 0;
}
.tablink{
    padding:10px 20px;
    border:none;
    background:#ddd;
    margin:0 5px;
    cursor:pointer;
    border-radius:5px;
    transition:0.3s;
}
.tablink.active{
    background:#dc3545;
    color:white;
}
.tab{display:none}
.tab.active{display:block}

/* --- TABLE --- */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}
th,td{
    border:1px solid #ccc;
    padding:8px;
    text-align:center;
}
th{background:#f1f1f1}

/* --- DELETE LINK --- */
a.delete{
    color:#dc3545;
    text-decoration:none;
    font-weight:bold;
}
a.delete:hover{text-decoration:underline}

/* --- MESSAGES --- */
.msg{
    color:green;
    font-weight:bold;
    text-align:center;
    margin-bottom:10px;
}
</style>

<script>
function openTab(evt,id){
    document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.tablink').forEach(b=>b.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    evt.currentTarget.classList.add('active');
}
</script>
</head>

<body>
<div class="container">
<h1>Admin Delete Panel</h1>

<div class="tabs">
    <button class="tablink active" onclick="openTab(event,'users')">Users</button>
    <button class="tablink" onclick="openTab(event,'books')">Books</button>
</div>

<!-- USERS -->
<div id="users" class="tab active">
<table>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Admin</th><th>Action</th>
</tr>
<?php while($u=mysqli_fetch_assoc($users)){ ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['name']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td><?= $u['admin'] ?></td>
<td>
<a class="delete" href="?delete_user=<?= $u['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
</td>
</tr>
<?php } ?>
</table>
</div>

<!-- BOOKS -->
<div id="books" class="tab">
<table>
<tr>
<th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Action</th>
</tr>
<?php while($b=mysqli_fetch_assoc($books)){ ?>
<tr>
<td><?= $b['id'] ?></td>
<td><?= htmlspecialchars($b['name']) ?></td>
<td><?= htmlspecialchars($b['catagory']) ?></td>
<td><?= $b['price'] ?></td>
<td>
<a class="delete" href="?delete_book=<?= $b['id'] ?>" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
</td>
</tr>
<?php } ?>
</table>
</div>

</div>
</body>
</html>