<?php
session_start();
require_once "config.php";

/* =========================
   AUTO LOGIN (REMEMBER ME)
========================= */
if (!isset($_SESSION['username']) && isset($_COOKIE['remember_token'])) {
    require_once "config.php";
    if (!$mycon->connect_error) {
        $token = $_COOKIE['remember_token'];
        $stmt = $mycon->prepare("SELECT Username, Email, Role FROM usertable WHERE RememberToken=?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();
            $_SESSION['username'] = $user['Username'];
            $_SESSION['email']    = $user['Email'];
            $_SESSION['role']     = $user['Role'];
        }
    }
}

/* =========================
   PROTECT PAGE
========================= */
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];
$email    = $_SESSION['email'];
$role     = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="style.css">

<style>
    :root {
    /* Light Mode Colors */
    --color-bg: #f5f5f5;
    --color-text: #121212;
    --color-text-light: #555;
    --color-navbar-bg: #ffffff;
    --color-card-bg: #ffffff;
    --color-card-shadow: rgba(0, 0, 0, 0.1);
    --color-primary: #218d8d;
    --color-primary-hover: #166666;
    --color-accent: #4ecdc4;
    --color-border: #ddd;
    --color-utility-bg: #fafafa;
    --color-utility-item-bg: #eee;
    }

    @media (prefers-color-scheme: dark) {
    :root {
        --color-bg: #121212;
        --color-text: #ffffff;
        --color-text-light: #cccccc;
        --color-navbar-bg: #1e1e1e;
        --color-card-bg: #1e1e1e;
        --color-card-shadow: rgba(0, 0, 0, 0.6);
        --color-primary: #218d8d;
        --color-primary-hover: #1a7575;
        --color-accent: #4ecdc4;
        --color-border: #333;
        --color-utility-bg: #1e1e1e;
        --color-utility-item-bg: #222;
    }
    }

    /* Manual theme switching */
    [data-color-scheme="light"] {
    --color-bg: #f5f5f5;
    --color-text: #121212;
    --color-text-light: #555;
    --color-navbar-bg: #ffffff;
    --color-card-bg: #ffffff;
    --color-card-shadow: rgba(0, 0, 0, 0.1);
    --color-primary: #218d8d;
    --color-primary-hover: #166666;
    --color-accent: #218d8d;
    --color-border: #ddd;
    --color-utility-bg: #fafafa;
    --color-utility-item-bg: #eee;
    }

    [data-color-scheme="dark"] {
    --color-bg: #121212;
    --color-text: #ffffff;
    --color-text-light: #cccccc;
    --color-navbar-bg: #1e1e1e;
    --color-card-bg: #1e1e1e;
    --color-card-shadow: rgba(0, 0, 0, 0.6);
    --color-primary: #218d8d;
    --color-primary-hover: #1a7575;
    --color-accent: #4ecdc4;
    --color-border: #333;
    --color-utility-bg: #1e1e1e;
    --color-utility-item-bg: #222;
    }

    /* Base styles */
    body {
    margin: 0;
    background: var(--color-bg);
    color: var(--color-text);
    font-family: Arial, Helvetica, sans-serif;
    }

    .navbar {
    background: var(--color-navbar-bg);
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 15px var(--color-card-shadow);
    }

    .navbar h2 {
    color: var(--color-accent);
    }

    .navbar a {
    color: var(--color-text);
    text-decoration: none;
    font-weight: bold;
    margin-left: 20px;
    }

    .navbar a:hover {
    color: var(--color-accent);
    }

    .dashboard {
    padding: 40px;
    text-align: center;
    }

    .cards {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 40px;
    flex-wrap: wrap;
    }

    .card {
    background: var(--color-card-bg);
    width: 280px;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 8px 20px var(--color-card-shadow);
    }

    .card h3 {
    color: var(--color-accent);
    margin-bottom: 10px;
    }

    .card p {
    color: var(--color-text-light);
    font-size: 14px;
    }

    .role-badge {
    display: inline-block;
    margin-top: 10px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    background: var(--color-primary);
    color: #fff;
    }

    .utility {
    margin-top: 30px;
    padding: 20px;
    background: var(--color-utility-bg);
    border-radius: 10px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    text-align: left;
    }

    .utility input {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid var(--color-border);
    margin-right: 10px;
    background: var(--color-card-bg);
    color: var(--color-text);
    }

    .utility button {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    background: var(--color-accent);
    color: #000;
    font-weight: bold;
    cursor: pointer;
    }

    .utility button:hover {
    background: var(--color-primary-hover);
    }

    .utility ul {
    list-style: none;
    padding: 0;
    }

    .utility li {
    background: var(--color-utility-item-bg);
    padding: 8px;
    border-radius: 6px;
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    }

    .utility a {
    text-decoration: none;
    font-weight: bold;
    color: var(--color-text);
    }
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <h2><i class="fa-solid fa-user" style="color: #4ecdc4;"></i>&nbsp;User Dashboard</h2>
    <div>
        <a href="home.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="change_password.php">Change Password</a>
        <?php if ($role === "admin"): ?>
            <a href="admin.php">Admin Panel</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- DASHBOARD -->
<div class="dashboard">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>
    <p><?php echo htmlspecialchars($email); ?></p>
    <span class="role-badge">Role: <?php echo strtoupper($role); ?></span>

    <div class="cards">
        <div class="card">
            <h3>👤 Profile</h3>
            <p>Edit your username and email details.</p>
        </div>
        <div class="card">
            <h3>🔑 Password</h3>
            <p>Change your account password securely.</p>
        </div>
        <div class="card">
            <h3>📝 To‑Do List</h3>
            <p>Manage your personal tasks.</p>
        </div>
        <div class="card">
            <h3>🛠️ Utilities</h3>
            <p>Calculator & random quote generator.</p>
        </div>
        <?php if ($role === "admin"): ?>
        <div class="card">
            <h3>🧑‍💼 Admin</h3>
            <p>Manage users and system settings.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- TO-DO LIST -->
    <div class="utility">
        <h2 style="text-align: center; margin-bottom:10px;">My To‑Do List</h2>
        <form method="post" style="margin-bottom:15px;">
            <input type="text" name="new_task" placeholder="Enter new task..." required>
            <button type="submit" name="add_task" style="display:flex; justify-self: center; margin-top:10px;">Add Task</button>
        </form>

        <?php
        
        if (!$mycon->connect_error) {
            // Add task
            if (isset($_POST['add_task'])) {
                $task = trim($_POST['new_task']);
                if ($task !== '') {
                    $stmt = $mycon->prepare("INSERT INTO tasks (username, task) VALUES (?, ?)");
                    $stmt->bind_param("ss", $username, $task);
                    $stmt->execute();
                }
            }

            // Mark done
            if (isset($_GET['done'])) {
                $id = intval($_GET['done']);
                $stmt = $mycon->prepare("UPDATE tasks SET status='done' WHERE id=? AND username=?");
                $stmt->bind_param("is", $id, $username);
                $stmt->execute();
            }

            // Delete
            if (isset($_GET['delete'])) {
                $id = intval($_GET['delete']);
                $stmt = $mycon->prepare("DELETE FROM tasks WHERE id=? AND username=?");
                $stmt->bind_param("is", $id, $username);
                $stmt->execute();
            }

            // Clear all completed
            if (isset($_POST['clear_completed'])) {
                $stmt = $mycon->prepare("DELETE FROM tasks WHERE status='done' AND username=?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
            }

            // Fetch tasks
            $stmt = $mycon->prepare("SELECT id, task, status FROM tasks WHERE username=? ORDER BY created_at DESC");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $res = $stmt->get_result();

            echo "<ul>";
            while ($row = $res->fetch_assoc()) {
                echo "<li>";
                if ($row['status'] === 'done') {
                    echo "<span style='text-decoration:line-through;color:#aaa;'>".htmlspecialchars($row['task'])."</span>";
                } else {
                    echo htmlspecialchars($row['task']);
                }
                echo "<span>";
                if ($row['status'] !== 'done') {
                    echo " <a href='?done=".$row['id']."' style='color:#4ecdc4;margin-left:10px;'>✔</a>";
                }
                echo " <a href='?delete=".$row['id']."' style='color:#ff4444;margin-left:10px;'>✖</a>";
                echo "</span></li>";
            }
            echo "</ul>";
        }
        ?>

        <form method="post" style="margin-top:10px;">
            <button type="submit" name="clear_completed" style="display:flex; justify-self: center; margin-top:10px;">Clear All Completed Tasks</button>
        </form>
    </div>

        <!-- UTILITIES -->
    <div class="utility">
        <h2 style="text-align:center; margin-bottom:10px;">Quick Calculator</h2>
        <input type="text" id="calcInput" placeholder="e.g. 12+7*3">
        <button onclick="calculate()" style="display:flex; justify-self: center; margin-top:10px;">Calculate</button>
        <p id="calcResult"></p>
    </div>

    <div class="utility">
        <h2 style="text-align:center; margin-bottom:10px;">Random Quote Generator</h2>
        <button onclick="showQuote()" style="display:flex; justify-self: center; margin-top:10px;">Get Quote</button>
        <p id="quoteBox" style="text-align:center; margin: 10px;"></p>
    </div>
</div>

<script>
// Calculator
function calculate(){
    const input = document.getElementById("calcInput").value;
    try {
        const result = Function('"use strict";return ('+input+')')();
        document.getElementById("calcResult").innerText = "Result: " + result;
    } catch(e){
        document.getElementById("calcResult").innerText = "Error in expression!";
    }
}

// Quotes
const quotes = [
    "The best way to get started is to quit talking and begin doing.",
    "Don’t let yesterday take up too much of today.",
    "It’s not whether you get knocked down, it’s whether you get up.",
    "If you are working on something exciting, it will keep you motivated.",
    "Success is not in what you have, but who you are."
];
function showQuote(){
    const q = quotes[Math.floor(Math.random()*quotes.length)];
    document.getElementById("quoteBox").innerText = q;
}
</script>

</body>
</html>