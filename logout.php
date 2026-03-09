<?php
session_start();

// Remove remember-me cookie
setcookie(
    "remember_token",
    "",
    time() - 3600,
    "/",
    "",
    true,
    true
);

// Clear session
session_unset();
session_destroy();

// Redirect
header("Location: index.html");
exit();
