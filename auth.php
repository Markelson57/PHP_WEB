<?php
session_start();

$conn = new mysqli("localhost", "web", "1234", "panel");
if ($conn->connect_error) {
    die("DB ERROR");
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }
    if (isset($_SESSION['banned']) && $_SESSION['banned']) {
        die("⛔ Estás baneado");
    }

    if (isset($_SESSION['locked_until']) && strtotime($_SESSION['locked_until']) > time()) {
        die("⛔ Cuenta bloqueada hasta " . $_SESSION['locked_until']);
    }

    // Refresh user data cada vez que se requiere login
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $_SESSION['user']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $_SESSION['role'] = $user['role'];
    $_SESSION['banned'] = $user['banned'];
    $_SESSION['locked_until'] = $user['locked_until'];
}

// 📊 LOG LOGIN ATTEMPT
function log_login_attempt($conn, $username, $success) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $stmt = $conn->prepare("INSERT INTO login_attempts (username, ip, success) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $ip, $success);
    $stmt->execute();
}

// 🚨 CHECK LOCK
function is_locked($user) {
    return !empty($user['locked_until']) && strtotime($user['locked_until']) > time();
}

// 👤 GET USER
function get_user_by_name($conn, $username) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// 🔐 LOGIN PROCESS
function login_user($conn, $username, $password) {

    $user = get_user_by_name($conn, $username);

    if (!$user) return false;

    // ⛔ bloqueado
    if (is_locked($user)) return "locked";

    if (password_verify($password, $user['password'])) {

        // reset attempts
        $stmt = $conn->prepare("UPDATE users SET failed_attempts=0, locked_until=NULL WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        log_login_attempt($conn, $username, 1);

        return true;
    }

    // ❌ FAILED LOGIN
    $attempts = $user['failed_attempts'] + 1;

    $lock = null;
    if ($attempts >= 5) {
        $lock = date("Y-m-d H:i:s", time() + 300); // 5 min lock
        $attempts = 0;
    }

    $stmt = $conn->prepare("UPDATE users SET failed_attempts=?, locked_until=? WHERE username=?");
    $stmt->bind_param("iss", $attempts, $lock, $username);
    $stmt->execute();

    log_login_attempt($conn, $username, 0);

    return false;
}

// 🔍 CHECK SESSION
function is_logged_in() {
    return isset($_SESSION['user']);
}

function get_user_data() {
    return [
        "user" => $_SESSION['user'] ?? null,
        "role" => $_SESSION['role'] ?? null
    ];
}

// 🛡 ROLE CHECK
function require_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        die("⛔ Access denied");
    }
}
?>