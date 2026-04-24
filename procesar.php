<?php
include 'auth.php';
include 'pantalla de carga.php';
session_start();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: formulario.php');
    exit;
}

if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $error = 'Token CSRF invalido';
} else {
    $usuario = htmlspecialchars(trim($_POST['usuario'] ?? ''), ENT_QUOTES, 'UTF-8');
    unset($_SESSION['csrf_token']);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $success = 'Formulario procesado correctamente. Usuario: ' . $usuario;
}

$page_title = 'Resultado CSRF';
$page_main_class = 'content-pad content-pad--centered';

ob_start();
?>
<style>
    :root {
        --accent: #38bdf8;
        --success: #10b981;
        --danger: #ef4444;
    }

    .result-card {
        padding: 3rem 2.4rem;
        border-radius: 32px;
        text-align: center;
    }

    .result-icon {
        display: inline-grid;
        place-items: center;
        width: 90px;
        height: 90px;
        border-radius: 28px;
        margin-bottom: 1rem;
        font-size: 2.6rem;
    }

    .result-icon.success {
        background: rgba(16, 185, 129, 0.14);
        color: var(--success);
    }

    .result-icon.error {
        background: rgba(239, 68, 68, 0.14);
        color: var(--danger);
    }

    .result-card h1 {
        margin: 0;
        font-size: clamp(2rem, 4vw, 2.5rem);
    }

    .result-card p {
        margin: 1rem 0 0;
        color: var(--text-secondary);
    }

    .result-card p.success {
        color: #d1fae5;
    }

    .result-card p.error {
        color: #fecaca;
    }

    .result-action {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        margin-top: 1.8rem;
        padding: 0.95rem 1.35rem;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--accent), #0ea5e9);
        color: #fff;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 16px 34px rgba(14, 165, 233, 0.24);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .result-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 38px rgba(14, 165, 233, 0.3);
    }

    @media (max-width: 520px) {
        .result-card {
            padding: 2.35rem 1.35rem;
            border-radius: 28px;
        }
    }
</style>
<?php
$page_styles = ob_get_clean();

ob_start();
?>
<div class="page-shell page-shell--medium">
    <section class="result-card glass-card">
        <?php if (isset($success)): ?>
            <div class="result-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Exito</h1>
            <p class="success"><?php echo $success; ?></p>
            <a href="formulario.php" class="result-action">
                <i class="fas fa-rotate-right"></i>
                <span>Nuevo formulario</span>
            </a>
        <?php else: ?>
            <div class="result-icon error">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1>Error</h1>
            <p class="error"><?php echo htmlspecialchars($error ?? 'Error desconocido', ENT_QUOTES, 'UTF-8'); ?></p>
            <a href="formulario.php" class="result-action">
                <i class="fas fa-arrow-left"></i>
                <span>Volver al formulario</span>
            </a>
        <?php endif; ?>
    </section>
</div>
<?php
$page_content = ob_get_clean();

ob_start();
?>
<script>
    gsap.from('.result-card', {
        duration: 0.9,
        y: 56,
        opacity: 0,
        scale: 0.98,
        ease: 'power3.out'
    });
</script>
<?php
$page_scripts = ob_get_clean();

include 'layout.php';
