<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// ---------------------
// DB Connection
$conn = mysqli_connect("localhost", "root", "123456", "library");
if (!$conn) die("DB Connection Failed: " . mysqli_connect_error());

// التحقق من صلاحية Admin
$user_email = $_SESSION['email'];
$res = mysqli_query($conn, "SELECT admin FROM users WHERE email = '".mysqli_real_escape_string($conn, $user_email)."'");
$row = mysqli_fetch_assoc($res);

if (!$row || $row['admin'] !== '1') {
    die("❌ Access Denied: Admins Only");
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_user'])) {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role  = $_POST['role'] ?? 0;

    // تشفير الباسورد
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, admin) VALUES (?, ?, ?, ?)");
    if (!$stmt) die("Prepare failed: " . mysqli_error($conn));

    mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $password_hashed, $role);

    if (mysqli_stmt_execute($stmt)) {
        $msg_user = "✅ User added successfully";
    } else {
        die("Execute failed: " . mysqli_stmt_error($stmt));
    }
}

// إضافة Book
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_book'])) {
    $name     = $_POST['book_name'];
    $catagory = $_POST['catagory'];
    $price    = $_POST['price'];

    $stmt = mysqli_prepare($conn, "INSERT INTO books (name, catagory, price) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssd", $name, $catagory, $price);
    mysqli_stmt_execute($stmt);
    $msg_book = "📘 Book added successfully";
}

// ---------------------------
// بحث عن الكتب
$search = "";
$results = [];

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['search'])) {
    $search = trim($_GET['search']);

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM books WHERE name LIKE ? OR catagory LIKE ?"
    );

    $like = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>

<style>
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:Arial, sans-serif;

    /* خلفية مكتبة */
    background-image: url("https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1600&q=80");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}

/* --- CONTAINER --- */
.container{
    width:90%;
    max-width:900px;
    background: rgba(255,255,255,0.95); /* شفافية بسيطة */
    padding:25px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,.4);
}

/* --- HEADINGS --- */
h1{text-align:center;margin-bottom:20px}

/* --- TABS --- */
.tabs{
    display:flex;
    justify-content:center;
    margin-bottom:20px;
}
.tablink{
    padding:10px 20px;
    border:none;
    cursor:pointer;
    background:#ddd;
    margin:0 5px;
    border-radius:5px;
    transition:0.3s;
}
.tablink.active{
    background:#007bff;
    color:white;
}
.tab{display:none}
.tab.active{display:block}

/* --- INPUTS --- */
input,select,button{
    width:100%;
    padding:10px;
    margin:8px 0;
}
button{
    background:#007bff;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}
button:hover{opacity:.9}

/* --- TABLE --- */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}
th,td{
    border:1px solid #ccc;
    padding:8px;
    text-align:center;
}
th{background:#f1f1f1}

/* --- MESSAGES --- */
.msg{color:green;font-weight:bold;text-align:center}
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
<h1>Admin Library</h1>

<div class="tabs">
    <button class="tablink active" onclick="openTab(event,'user')">Add User</button>
    <button class="tablink" onclick="openTab(event,'book')">Add Book</button>
    <button class="tablink" onclick="openTab(event,'search')">Search</button>
</div>

<!-- Add User -->
<div id="user" class="tab active">
<?php if(isset($msg_user)) echo "<p class='msg'>$msg_user</p>"; ?>
<form method="POST">
<input name="name" placeholder="Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<select name="role">
<option value="0">User</option>
<option value="1">Admin</option>
</select>
<button name="add_user">Add User</button>
</form>
</div>

<!-- Add Book -->
<div id="book" class="tab">
<?php if(isset($msg_book)) echo "<p class='msg'>$msg_book</p>"; ?>
<form method="POST">
<input name="book_name" placeholder="Book Name" required>
<input name="catagory" placeholder="Category" required>
<input type="number" step="0.01" name="price" placeholder="Price" required>
<button name="add_book">Add Book</button>
</form>
</div>

<!-- Search -->
<div id="search" class="tab">
<form method="GET">
<input name="search" placeholder="Search book" required>
<button>Search</button>
</form>

<?php if($results){ ?>
<table>
<tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th></tr>
<?php foreach($results as $b){ ?>
<tr>
<td><?= $b['id'] ?></td>
<td><?= htmlspecialchars($b['name']) ?></td>
<td><?= htmlspecialchars($b['catagory']) ?></td>
<td><?= $b['price'] ?></td>
</tr>
<?php } ?>
</table>
<?php } ?>
</div>

</div>
</body>
</html>