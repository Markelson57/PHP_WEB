<?php
include 'auth.php';
include 'pantalla de carga.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = 'Registro Premium';
$page_main_class = 'content-pad content-pad--centered';
$message = '';
$messageType = '';

$conn = new mysqli('localhost', 'web', '1234', 'panel');

if ($conn->connect_error) {
    die('DB ERROR: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['username'], $_POST['password'])) {
        die('Faltan datos');
    }

    $username = trim($_POST['username']);

    if (strlen($username) < 3) {
        $message = 'Usuario demasiado corto';
        $messageType = 'error';
    } else {
        $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare('SELECT id FROM users WHERE username=?');
        if (!$stmt) {
            die('ERROR PREPARE SELECT: ' . $conn->error);
        }

        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $message = 'Usuario ya existe';
            $messageType = 'error';
        } else {
            $stmt = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            if (!$stmt) {
                die('ERROR PREPARE INSERT: ' . $conn->error);
            }

            $stmt->bind_param('ss', $username, $passwordHash);

            if ($stmt->execute()) {
                $message = 'Usuario creado exitosamente';
                $messageType = 'success';
            } else {
                $message = 'Error al crear usuario';
                $messageType = 'error';
            }
        }
    }
}

ob_start();
?>
<style>
    :root {
        --accent: #10b981;
        --accent-glow: #059669;
        --danger: #ef4444;
    }

    .signup-card {
        position: relative;
        padding: 3rem 2.5rem;
        border-radius: 34px;
        overflow: hidden;
    }

    .signup-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top left, rgba(16, 185, 129, 0.24), transparent 34%),
            linear-gradient(160deg, rgba(255, 255, 255, 0.08), transparent 60%);
        pointer-events: none;
    }

    .signup-card > * {
        position: relative;
        z-index: 1;
    }

    .signup-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .signup-icon {
        width: 88px;
        height: 88px;
        margin: 0 auto 1rem;
        border-radius: 26px;
        display: grid;
        place-items: center;
        background: rgba(16, 185, 129, 0.14);
        color: var(--accent);
        font-size: 2.3rem;
        box-shadow: inset 0 0 0 1px rgba(74, 222, 128, 0.2);
    }

    .signup-header h1 {
        margin: 0;
        font-size: clamp(2rem, 4vw, 2.4rem);
    }

    .signup-header p {
        margin: 0.8rem 0 0;
        color: var(--text-secondary);
    }

    .signup-message {
        margin-bottom: 1.4rem;
        padding: 1rem 1.1rem;
        border-radius: 18px;
        font-weight: 600;
        text-align: center;
    }

    .signup-message.success {
        color: #d1fae5;
        background: rgba(16, 185, 129, 0.16);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .signup-message.error {
        color: #fecaca;
        background: rgba(239, 68, 68, 0.14);
        border: 1px solid rgba(239, 68, 68, 0.28);
    }

    .signup-form {
        display: grid;
        gap: 1.2rem;
    }

    .form-group {
        position: relative;
    }

    .form-group i {
        position: absolute;
        left: 1.1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
    }

    .form-group input {
        width: 100%;
        min-height: 58px;
        padding: 0.95rem 1rem 0.95rem 3rem;
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
        border-color: rgba(74, 222, 128, 0.48);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.14);
        transform: translateY(-1px);
    }

    .signup-submit {
        min-height: 60px;
        border: 0;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--accent), var(--accent-glow));
        color: #fff;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 18px 38px rgba(5, 150, 105, 0.28);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .signup-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 42px rgba(5, 150, 105, 0.34);
    }

    @media (max-width: 520px) {
        .signup-card {
            padding: 2.35rem 1.35rem;
            border-radius: 28px;
        }
    }
</style>
<?php
$page_styles = ob_get_clean();

ob_start();
?>
<div class="page-shell page-shell--narrow">
    <section class="signup-card glass-card">
        <div class="signup-header">
            <div class="signup-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1>Crear cuenta premium</h1>
            <p>Registro Premium gratis.</p>
        </div>

        <?php if ($message): ?>
            <div class="signup-message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="signup-form">
            <div class="form-group">
                <i class="fas fa-user-circle"></i>
                <input type="text" name="username" placeholder="Usuario unico (min 3 caracteres)" required autocomplete="username" maxlength="20">
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Contrasena segura (min 6 caracteres)" required autocomplete="new-password" minlength="6">
            </div>

            <button type="submit" class="signup-submit">Crear usuario premium</button>
        </form>
    </section>
</div>
<?php
$page_content = ob_get_clean();

ob_start();
?>
<script>
    gsap.from('.signup-card', {
        duration: 0.9,
        y: 56,
        opacity: 0,
        ease: 'power3.out'
    });

    gsap.to('.signup-icon', {
        duration: 3.2,
        y: -8,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut'
    });

    <?php if ($messageType === 'success'): ?>
    for (let i = 0; i < 16; i += 1) {
        const dot = document.createElement('div');
        dot.style.position = 'fixed';
        dot.style.left = '50%';
        dot.style.top = '50%';
        dot.style.width = '10px';
        dot.style.height = '10px';
        dot.style.borderRadius = '50%';
        dot.style.background = `hsl(${120 + Math.random() * 50}, 70%, 60%)`;
        dot.style.pointerEvents = 'none';
        dot.style.zIndex = '1200';
        document.body.appendChild(dot);

        gsap.to(dot, {
            duration: 1.6,
            x: (Math.random() - 0.5) * 360,
            y: (Math.random() - 0.2) * 320,
            scale: 0,
            opacity: 0,
            ease: 'power2.out',
            onComplete: () => dot.remove()
        });
    }
    <?php endif; ?>
</script>
<?php
$page_scripts = ob_get_clean();

include 'layout.php';
