<?php
session_start();
include 'auth.php';
// pantalla de carga en todos las páginas para mejorar UX
include 'pantalla de carga.php';

// 🔐 Protección de rol (admin only)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("⛔ Acceso denegado");
}

$conn = new mysqli("localhost", "web", "1234", "panel");

if ($conn->connect_error) {
    die("DB ERROR: " . $conn->connect_error);
}

// 🧠 CSRF TOKEN
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 📊 LOG FUNCTION
function log_action($conn, $action, $target = '') {
    $stmt = $conn->prepare("INSERT INTO logs (username, action, target, ip) VALUES (?, ?, ?, ?)");
    $user = $_SESSION['user'] ?? 'unknown';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt->bind_param("ssss", $user, $action, $target, $ip);
    $stmt->execute();
}

// =======================
// 🔥 ACCIONES ADMIN
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🛡 CSRF CHECK
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        die("⛔ CSRF inválido");
    }

    $action = $_POST['action'] ?? '';
    $username = $_POST['username'] ?? '';
    $role = $_POST['role'] ?? 'user';

    // ➕ CREAR USUARIO
    if ($action === 'create') {

        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        $stmt->execute();

        log_action($conn, "CREATE_USER", $username);
    }

    // ❌ ELIMINAR USUARIO
    if ($action === 'delete') {

        $stmt = $conn->prepare("DELETE FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        log_action($conn, "DELETE_USER", $username);
    }

    // 🔁 CAMBIAR ROLE
    if ($action === 'role') {

        $stmt = $conn->prepare("UPDATE users SET role=? WHERE username=?");
        $stmt->bind_param("ss", $role, $username);
        $stmt->execute();

        log_action($conn, "CHANGE_ROLE", "$username -> $role");
    }
}

// 👥 USERS
$users = $conn->query("SELECT id, username, role FROM users ORDER BY id DESC");

// 📜 LOGS
$logs = $conn->query("SELECT * FROM logs ORDER BY id DESC LIMIT 20");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<link rel="stylesheet" href="shared.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
.panel {
    max-width: 1200px;
    margin: auto;
    padding: 2rem;
}

.card {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    backdrop-filter: blur(25px);
    border-radius: 24px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

h2 {
    color: #38bdf8;
}

input, select, button {
    padding: 0.8rem;
    margin: 0.3rem;
    border-radius: 12px;
    border: none;
}

button {
    background: #38bdf8;
    color: white;
    cursor: pointer;
    transition: 0.2s;
}
button:hover {
    transform: scale(1.05);
}

table {
    width: 100%;
    color: white;
    margin-top: 1rem;
}

th, td {
    padding: 0.6rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.log {
    font-size: 0.9rem;
    opacity: 0.9;
}
</style>
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="content-pad panel">

<!-- ➕ CREAR -->
<div class="card">
    <h2>➕ Crear usuario</h2>

    <form method="POST">
        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">

        <input name="username" placeholder="Usuario" required>
        <input name="password" type="password" placeholder="Contraseña" required>

        <select name="role">
            <option value="user">user</option>
            <option value="moderador">moderador</option>
            <option value="admin">admin</option>
        </select>

        <button name="action" value="create">Crear</button>
    </form>
</div>

<!-- 👥 USERS -->
<div class="card">
    <h2>👥 Usuarios</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>

        <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td>

                <form method="POST" style="display:inline;">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="username" value="<?= $u['username'] ?>">
                    <button name="action" value="delete">❌</button>
                </form>

                <form method="POST" style="display:inline;">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="username" value="<?= $u['username'] ?>">

                    <select name="role">
                        <option value="user">user</option>
                        <option value="moderador">moderador</option>
                        <option value="admin">admin</option>
                    </select>

                    <button name="action" value="role">🔁</button>
                </form>

            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- 📊 LOGS -->
<div class="card">
    <h2>📊 Logs de actividad</h2>

    <?php while ($l = $logs->fetch_assoc()): ?>
        <div class="log">
            👤 <?= $l['username'] ?> |
            ⚡ <?= $l['action'] ?> |
            🎯 <?= $l['target'] ?> |
            🌐 <?= $l['ip'] ?>
        </div>
    <?php endwhile; ?>
</div>

</div>

</body>
</html>