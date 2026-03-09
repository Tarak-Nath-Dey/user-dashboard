<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

// =========================
// SECURITY: ONLY POST
// =========================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request"
    ]);
    exit();
}

// =========================
// DB CONNECTION
// =========================
require_once "config.php";

if ($mycon->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit();
}

// =========================
// INPUT FETCH & VALIDATION
// =========================
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$remember = $_POST['remember'] ?? false;

if ($username === "" || $password === "") {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required"
    ]);
    exit();
}

// =========================
// USER FETCH
// =========================
$sql = "SELECT Username, Email, Password, Role FROM usertable WHERE Username = ?";
$stmt = $mycon->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Query preparation failed"
    ]);
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

// =========================
// LOGIN CHECK
// =========================
if ($res->num_rows === 1) {
    $user = $res->fetch_assoc();

    if (password_verify($password, $user['Password'])) {

        // 🔐 Prevent session fixation
        session_regenerate_id(true);

        // =========================
        // SESSION SET
        // =========================
        $_SESSION['username'] = $user['Username'];
        $_SESSION['email']    = $user['Email'];
        $_SESSION['role']     = $user['Role'];

        // =========================
        // REMEMBER ME COOKIE
        // =========================
        if ($remember === "true") {
            $token = bin2hex(random_bytes(32));

            setcookie(
                "remember_token",
                $token,
                time() + (86400 * 30), // 30 days
                "/",
                "",
                true,   // Secure (HTTPS)
                true    // HttpOnly
            );

            $upd = $mycon->prepare(
                "UPDATE usertable SET RememberToken = ? WHERE Username = ?"
            );
            $upd->bind_param("ss", $token, $username);
            $upd->execute();
            $upd->close();
        }

        $stmt->close();

        echo json_encode([
            "status" => "success",
            "message" => "Login successful"
        ]);
        exit();
    }
}

// =========================
// FAILURE
// =========================
$stmt->close();

echo json_encode([
    "status" => "error",
    "message" => "Invalid username or password"
]);
exit();
