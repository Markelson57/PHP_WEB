<?php
session_start();
include 'auth.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'moderador' && $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "web", "1234", "panel");

if ($conn->connect_error) {
    die("DB ERROR: " . $conn->connect_error);
}

// 🔥 ACCIONES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';
    $target = $_POST['target'] ?? '';
    $reason = $_POST['reason'] ?? '';

    // 🧨 BAN USER
    if ($action === 'ban') {

        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $target);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            $uid = $user['id'];

            $ban = $conn->prepare("INSERT INTO bans (user_id, reason) VALUES (?, ?)");
            $ban->bind_param("is", $uid, $reason);
            $ban->execute();

            $log = $conn->prepare("INSERT INTO moderation_logs (moderator, action, target) VALUES (?, ?, ?)");
            $mod = $_SESSION['user'];
            $act = "BANEADO: $reason";
            $log->bind_param("sss", $mod, $act, $target);
            $log->execute();
        }
    }

    // ❌ UNBAN
    if ($action === 'unban') {

        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $target);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            $uid = $user['id'];

            $del = $conn->prepare("DELETE FROM bans WHERE user_id=?");
            $del->bind_param("i", $uid);
            $del->execute();

            $log = $conn->prepare("INSERT INTO moderation_logs (moderator, action, target) VALUES (?, ?, ?)");
            $mod = $_SESSION['user'];
            $act = "UNBAN";
            $log->bind_param("sss", $mod, $act, $target);
            $log->execute();
        }
    }
}

// 📊 USERS
$users = $conn->query("SELECT username, role FROM users");

// 📜 LOGS
$logs = $conn->query("SELECT * FROM moderation_logs ORDER BY created_at DESC LIMIT 20");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Moderación Panel</title>
<link rel="stylesheet" href="shared.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
.panel {
    max-width: 1100px;
    margin: auto;
    padding: 2rem;
}

.card {
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 24px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

h2 {
    color: #38bdf8;
}

input, button {
    padding: 0.8rem;
    border-radius: 12px;
    border: none;
    margin: 0.3rem;
}

button {
    background: #38bdf8;
    color: white;
    cursor: pointer;
}
button:hover {
    transform: scale(1.05);
}

table {
    width: 100%;
    color: white;
}
th, td {
    padding: 0.6rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
</style>
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="content-pad panel">

    <div class="card">
        <h2>🔥 Sistema de Moderación</h2>

        <form method="POST">
            <input type="text" name="target" placeholder="Usuario objetivo" required>
            <input type="text" name="reason" placeholder="Motivo (ban)">
            <button name="action" value="ban">Banear</button>
            <button name="action" value="unban">Desbanear</button>
        </form>
    </div>

    <div class="card">
        <h2>👥 Usuarios</h2>
        <table>
            <tr><th>Usuario</th><th>Rol</th></tr>
            <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="card">
        <h2>📜 Logs de Moderación</h2>
        <table>
            <tr><th>Moderador</th><th>Acción</th><th>Target</th><th>Fecha</th></tr>
            <?php while ($l = $logs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($l['moderator']) ?></td>
                    <td><?= htmlspecialchars($l['action']) ?></td>
                    <td><?= htmlspecialchars($l['target']) ?></td>
                    <td><?= $l['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

</body>
</html>