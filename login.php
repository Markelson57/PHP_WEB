<?php
include 'auth.php';

$page_title = 'Login Seguro';
$page_main_class = 'content-pad content-pad--centered';
$error = '';

$conn = new mysqli('localhost', 'web', '1234', 'panel');

if ($conn->connect_error) {
    die('DB ERROR: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT * FROM users WHERE username=?');
    if (!$stmt) {
        die('ERROR PREPARE SELECT: ' . $conn->error);
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Login incorrecto';
}

ob_start();
?>
<style>
    :root {
        --accent: #38bdf8;
        --accent-glow: #0ea5e9;
        --danger: #ef4444;
    }

    .login-card {
        position: relative;
        padding: 3rem 2.5rem;
        border-radius: 32px;
        overflow: hidden;
    }

    .login-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.22), transparent 36%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.06), transparent 58%);
        pointer-events: none;
    }

    .login-card > * {
        position: relative;
        z-index: 1;
    }

    .login-logo {
        width: 82px;
        height: 82px;
        margin: 0 auto 1.5rem;
        border-radius: 24px;
        display: grid;
        place-items: center;
        background: rgba(56, 189, 248, 0.14);
        color: var(--accent);
        font-size: 2.2rem;
        box-shadow: inset 0 0 0 1px rgba(125, 211, 252, 0.2);
    }

    .login-card h1 {
        margin: 0;
        text-align: center;
        font-size: clamp(2rem, 4vw, 2.35rem);
    }

    .login-subtitle {
        margin: 0.9rem 0 2rem;
        text-align: center;
        color: var(--text-secondary);
    }

    .login-error {
        margin-bottom: 1.25rem;
        padding: 0.95rem 1rem;
        border-radius: 16px;
        background: rgba(239, 68, 68, 0.14);
        border: 1px solid rgba(239, 68, 68, 0.28);
        color: #fecaca;
        text-align: center;
        font-weight: 600;
    }

    .login-form {
        display: grid;
        gap: 1.15rem;
    }

    .form-group {
        position: relative;
    }

    .form-group i:first-child {
        position: absolute;
        left: 1.1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
    }

    .form-group input {
        width: 100%;
        min-height: 56px;
        padding: 0.95rem 3.1rem 0.95rem 2.9rem;
        border-radius: 18px;
        border: 1px solid var(--glass-border);
        background: rgba(255, 255, 255, 0.06);
        color: var(--text-primary);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .form-group input::placeholder {
        color: var(--text-secondary);
    }

    .form-group input:focus {
        outline: none;
        border-color: rgba(125, 211, 252, 0.55);
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
        transform: translateY(-1px);
    }

    .toggle-password {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        cursor: pointer;
    }

    .toggle-password:hover {
        color: var(--accent);
    }

    .login-submit {
        min-height: 58px;
        border: 0;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--accent), var(--accent-glow));
        color: #fff;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 18px 38px rgba(14, 165, 233, 0.28);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .login-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 42px rgba(14, 165, 233, 0.34);
    }

    @media (max-width: 520px) {
        .login-card {
            padding: 2.35rem 1.35rem;
            border-radius: 26px;
        }
    }
</style>
<?php
$page_styles = ob_get_clean();

ob_start();
?>
<div class="page-shell page-shell--narrow">
    <section class="login-card glass-card">
        <div class="login-logo">
            <i class="fas fa-lock"></i>
        </div>

        <h1>Bienvenido</h1>
        <p class="login-subtitle">Accede al panel con tu usuario y tu contrasena.</p>

        <?php if ($error): ?>
            <div class="login-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Usuario" required autocomplete="username">
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Contrasena" required autocomplete="current-password">
                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>

            <button type="submit" class="login-submit">Acceder al panel</button>
        </form>
    </section>
</div>
<?php
$page_content = ob_get_clean();

ob_start();
?>
<script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword?.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    gsap.from('.login-card', {
        duration: 0.9,
        y: 48,
        opacity: 0,
        ease: 'power3.out'
    });
</script>
<?php
$page_scripts = ob_get_clean();

include 'layout.php';
