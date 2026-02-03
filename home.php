<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* DB Connection */
$conn = mysqli_connect("localhost", "root", "123456", "library");
if (!$conn) {
    die("DB Connection Failed: " . mysqli_connect_error());
}

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
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Library Search</title>

<style>
/* --- BODY --- */
body {
    margin:0;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 0;

    /* Static background image */
    background-image: url("https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1600&q=80");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}

/* --- CONTAINER --- */
.container {
    width: 90%;
    max-width: 1000px;
    background: rgba(255,255,255,0.95);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

/* --- FORM --- */
form {
    display:flex;
    justify-content:center;
    gap:10px;
    margin-bottom:25px;
}
input[type="text"]{
    padding:10px;
    border-radius:5px;
    border:1px solid #ccc;
    flex:1;
}
button {
    padding:10px 20px;
    border:none;
    background:#007bff;
    color:white;
    border-radius:5px;
    cursor:pointer;
}
button:hover {opacity:.9}

/* --- BOOK CARDS --- */
.books {
    display:grid;
    grid-template-columns: repeat(auto-fill,minmax(220px,1fr));
    gap:20px;
}
.book-card {
    background:#fff;
    padding:15px;
    border-radius:10px;
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
    transition:0.3s;
}
.book-card:hover {
    transform: translateY(-5px);
    box-shadow:0 12px 30px rgba(0,0,0,0.3);
}
.book-card h3 {margin:0 0 10px 0;}
.book-card p {margin:5px 0; font-size:14px;}
.book-card span {font-weight:bold;}

/* --- No results --- */
.no-result { color:red; font-weight:bold; text-align:center; margin-top:20px; }

</style>
</head>
<body>

<div class="container">
    <h2 style="text-align:center;">📚 Library Search</h2>

    <form method="get">
        <input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by Name or Category" required>
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($results)) { ?>
        <div class="books">
            <?php foreach($results as $book) { ?>
                <div class="book-card">
                    <h3><?= htmlspecialchars($book['name']) ?></h3>
                    <p>Category: <span><?= htmlspecialchars($book['catagory']) ?></span></p>
                    <p>Price: <span>$<?= $book['price'] ?></span></p>
                    <p>ID: <span><?= $book['id'] ?></span></p>
                </div>
            <?php } ?>
        </div>
    <?php } elseif(isset($_GET['search'])) { ?>
        <p class="no-result">❌ No results found</p>
    <?php } ?>
</div>

</body>
</html>