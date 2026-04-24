<?php
include 'auth.php';
include 'pantalla de carga.php';
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = 'Formulario CSRF';
$page_main_class = 'content-pad content-pad--centered';

ob_start();
?>
<style>
    :root {
        --accent: #38bdf8;
        --accent-glow: #0ea5e9;
    }

    .csrf-card {
        position: relative;
        padding: 3rem 2.5rem;
        border-radius: 32px;
        overflow: hidden;
    }

    .csrf-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top, rgba(56, 189, 248, 0.24), transparent 34%),
            linear-gradient(145deg, rgba(255, 255, 255, 0.08), transparent 62%);
        pointer-events: none;
    }

    .csrf-card > * {
        position: relative;
        z-index: 1;
    }

    .csrf-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .csrf-header i {
        font-size: 2.7rem;
        color: var(--accent);
        margin-bottom: 0.9rem;
    }

    .csrf-header h1 {
        margin: 0;
        font-size: clamp(1.95rem, 4vw, 2.3rem);
    }

    .csrf-header p {
        margin: 0.85rem 0 0;
        color: var(--text-secondary);
    }

    .csrf-form {
        display: grid;
        gap: 1.2rem;
    }

    .form-group {
        position: relative;
    }

    .form-group i {
        position: absolute;
        left: 1.05rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent);
    }

    .form-group input {
        width: 100%;
        min-height: 58px;
        padding: 0.95rem 1rem 0.95rem 2.95rem;
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
        border-color: rgba(125, 211, 252, 0.48);
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
        transform: translateY(-1px);
    }

    .csrf-submit {
        position: relative;
        min-height: 58px;
        border: 0;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--accent), var(--accent-glow));
        color: #fff;
        font-weight: 700;
        cursor: pointer;
        overflow: hidden;
        box-shadow: 0 18px 38px rgba(14, 165, 233, 0.28);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .csrf-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 42px rgba(14, 165, 233, 0.34);
    }

    .csrf-submit.loading {
        pointer-events: none;
    }

    .spinner {
        position: absolute;
        right: 1.1rem;
        top: 50%;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.26);
        border-top-color: #fff;
        display: none;
        animation: spin 1s linear infinite;
    }

    .csrf-submit.loading .spinner {
        display: block;
    }

    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.45);
        transform: scale(0);
        animation: rippleAnim 0.55s linear;
        pointer-events: none;
    }

    @keyframes rippleAnim {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 520px) {
        .csrf-card {
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
    <section class="csrf-card glass-card">
        <div class="csrf-header">
            <i class="fas fa-shield-halved"></i>
            <h1>Formulario CSRF seguro</h1>
            <p>Este formulario incluye protección CSRF para garantizar la seguridad de tus datos.</p>
        </div>

        <form method="POST" action="procesar.php" id="csrfForm" class="csrf-form">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="usuario" placeholder="Ingresa tu usuario" required autocomplete="username">
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <button type="submit" class="csrf-submit">
                <span id="btnText">Enviar</span>
                <span class="spinner" id="spinner"></span>
            </button>
        </form>
    </section>
</div>
<?php
$page_content = ob_get_clean();

ob_start();
?>
<script>
    const form = document.getElementById('csrfForm');
    const submitButton = form ? form.querySelector('.csrf-submit') : null;
    const buttonText = document.getElementById('btnText');

    submitButton?.addEventListener('click', function (event) {
        const ripple = document.createElement('span');
        ripple.classList.add('ripple');

        const rect = submitButton.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.width = `${size}px`;
        ripple.style.height = `${size}px`;
        ripple.style.left = `${x}px`;
        ripple.style.top = `${y}px`;

        submitButton.appendChild(ripple);
        window.setTimeout(() => ripple.remove(), 550);
    });

    form?.addEventListener('submit', function () {
        submitButton.classList.add('loading');
        if (buttonText) {
            buttonText.textContent = 'Enviando...';
        }
    });

    gsap.from('.csrf-card', {
        duration: 1,
        y: 56,
        opacity: 0,
        ease: 'power3.out'
    });
</script>
<?php
$page_scripts = ob_get_clean();

include 'layout.php';
