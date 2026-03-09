<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require_once "config.php";
$msg = "";
$msgType = "";

if ($_SERVER['REQUEST_METHOD']=="POST") {
    $old = $_POST['old'];
    $new = $_POST['new'];

    $stmt = $mycon->prepare("SELECT Password FROM usertable WHERE Username=?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res && password_verify($old, $res['Password'])) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $u = $mycon->prepare("UPDATE usertable SET Password=? WHERE Username=?");
        $u->bind_param("ss", $hash, $_SESSION['username']);
        $u->execute();
        $msg = "Password changed successfully!";
        $msgType = "success";
    } else {
        $msg = "Old password is incorrect!";
        $msgType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Existing CSS -->
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            margin: 0;
            background: #121212;
            color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-box {
            background: #1e1e1e;
            padding: 35px 40px;
            border-radius: 12px;
            width: 90%;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
        }

        .password-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #4ecdc4;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #cccccc;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: none;
            outline: none;
            font-size: 14px;
        }

        .form-group input:focus {
            outline: 2px solid #4ecdc4;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            background: #218d8d;
            color: #ffffff;
            transition: background 0.2s ease;
        }

        .btn:hover {
            background: #1a7575;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .message.success {
            color: #4ecdc4;
        }

        .message.error {
            color: #ff6b6b;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #cccccc;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: #4ecdc4;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="password-box">
        <h2>Change Password</h2>

        <?php if ($msg): ?>
            <div class="message <?php echo $msgType; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Old Password</label>
                <input type="password" name="old" required>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new" required>
            </div>

            <button class="btn" type="submit">Change Password</button>
        </form>

        <a href="home.php" class="back-link">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
