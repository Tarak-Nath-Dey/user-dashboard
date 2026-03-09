<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require_once "config.php";
$successMsg = "";

if ($_SERVER['REQUEST_METHOD']=="POST") {
    $newEmail = $_POST['email'];
    $newUser  = $_POST['username'];
    $oldUser  = $_SESSION['username'];

    $stmt = $mycon->prepare("UPDATE usertable SET Username=?, Email=? WHERE Username=?");
    $stmt->bind_param("sss",$newUser,$newEmail,$oldUser);
    $stmt->execute();

    $_SESSION['username'] = $newUser;
    $_SESSION['email']    = $newEmail;

    $successMsg = "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Reuse your existing CSS -->
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

        .profile-box {
            background: #1e1e1e;
            padding: 35px 40px;
            border-radius: 12px;
            width: 90%;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
        }

        .profile-box h2 {
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

        .success {
            text-align: center;
            margin-bottom: 15px;
            color: #4ecdc4;
            font-size: 14px;
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
    <div class="profile-box">
        <h2>Edit Profile</h2>

        <?php if ($successMsg): ?>
            <div class="success"><?php echo $successMsg; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username"
                       value="<?php echo htmlspecialchars($_SESSION['username']); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?php echo htmlspecialchars($_SESSION['email']); ?>"
                       required>
            </div>

            <button class="btn" type="submit">Update Profile</button>
        </form>

        <a href="home.php" class="back-link">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
