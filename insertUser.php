<?php
// ============================================
// REGISTRATION - FINAL FIXED VERSION
// ============================================

// Set error reporting FIRST
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start output buffering to prevent headers sent error
ob_start();

// Set JSON header
header("Content-Type: application/json; charset=UTF-8");

$response = ["status" => "error", "message" => "Unknown error"];
$mycon = null;

try {
    // ============================================
    // CHECK REQUEST METHOD
    // ============================================
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method");
    }

    // ============================================
    // GET FORM DATA
    // ============================================
    $uname = isset($_POST['username']) ? trim($_POST['username']) : "";
    $email = isset($_POST['emailId']) ? trim($_POST['emailId']) : "";
    $pass = isset($_POST['password']) ? $_POST['password'] : "";

    // ============================================
    // BASIC VALIDATION
    // ============================================
    if (empty($uname) || empty($email) || empty($pass)) {
        throw new Exception("All fields are required");
    }

    if (strlen($uname) < 3) {
        throw new Exception("Username must be at least 3 characters");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    if (strlen($pass) < 6) {
        throw new Exception("Password must be at least 6 characters");
    }

    // ============================================
    // DATABASE CONNECTION
    // ============================================
    require_once "config.php";

    // ============================================
    // CHECK USERNAME
    // ============================================
    $checkStmt = $mycon->prepare("SELECT Username FROM usertable WHERE Username = ?");
    if ($checkStmt === false) {
        throw new Exception("SQL error: " . $mycon->error);
    }

    $checkStmt->bind_param("s", $uname);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $checkStmt->close();
        throw new Exception("Username already exists");
    }
    $checkStmt->close();

    // ============================================
    // CHECK EMAIL
    // ============================================
    $checkStmt = $mycon->prepare("SELECT Email FROM usertable WHERE Email = ?");
    if ($checkStmt === false) {
        throw new Exception("SQL error: " . $mycon->error);
    }

    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $checkStmt->close();
        throw new Exception("Email already registered");
    }
    $checkStmt->close();

    // ============================================
    // HASH PASSWORD & INSERT USER
    // ============================================
    $hashedPass = password_hash($pass, PASSWORD_DEFAULT);

    $insertStmt = $mycon->prepare("INSERT INTO usertable (Username, Email, Password) VALUES (?, ?, ?)");
    if ($insertStmt === false) {
        throw new Exception("SQL error: " . $mycon->error);
    }

    $insertStmt->bind_param("sss", $uname, $email, $hashedPass);

    if (!$insertStmt->execute()) {
        throw new Exception("Insert failed: " . $insertStmt->error);
    }

    $insertStmt->close();

    // ============================================
    // SUCCESS RESPONSE
    // ============================================
    $response["status"] = "success";
    $response["message"] = "Registration successful! Redirecting...";

} catch (Throwable $e) {
    $response["status"] = "error";
    $response["message"] = $e->getMessage();
} catch (Exception $e) {
    $response["status"] = "error";
    $response["message"] = $e->getMessage();
} finally {
    // Close connection ONLY ONCE in finally block
    if ($mycon !== null) {
        $mycon->close();
    }
}

// Clear output buffer and send JSON
ob_end_clean();
echo json_encode($response);
exit;
?>
