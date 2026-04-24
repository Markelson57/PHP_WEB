<?php
include 'auth.php';
include 'pantalla de carga.php';
session_start();

$user_data = get_user_data();
$user = $user_data['user'];
$role = $user_data['role'];

$page_title = 'Panel Dashboard';
$page_main_class = 'content-pad content-pad--wide';

ob_start();
?>
<style>
    :root {
        --accent: #38bdf8;
        --accent-glow: #0ea5e9;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #f97316;
    }

    .dashboard-shell {
        display: grid;
        gap: 1.6rem;
    }

    .dashboard-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(260px, 0.8fr);
        gap: 1.5rem;
        padding: 2rem;
        border-radius: 30px;
    }

    .dashboard-copy h1 {
        margin: 0;
        font-size: clamp(2rem, 4vw, 3rem);
    }

    .dashboard-copy p {
        margin: 1rem 0 0;
        max-width: 640px;
        color: var(--text-secondary);
    }

    .dashboard-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.45rem 0.85rem;
        margin-bottom: 1rem;
        border-radius: 999px;
        background: rgba(56, 189, 248, 0.12);
        color: #bae6fd;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        font-size: 0.84rem;
    }

    .dashboard-meta {
        display: grid;
        gap: 1rem;
        align-content: start;
    }

    .meta-card {
        padding: 1.1rem 1.2rem;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--glass-border);
    }

    .meta-card span {
        display: block;
        color: var(--text-secondary);
        font-size: 0.92rem;
        margin-bottom: 0.2rem;
    }

    .meta-card strong {
        font-size: 1.05rem;
    }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
    }

    .card {
        position: relative;
        padding: 1.75rem 1.4rem;
        border-radius: 26px;
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        border-color: rgba(125, 211, 252, 0.22);
        box-shadow: 0 22px 44px rgba(2, 6, 23, 0.18);
    }

    .card i {
        display: block;
        font-size: 2.8rem;
        margin-bottom: 0.9rem;
    }

    .card-title {
        margin: 0;
        font-size: 1.15rem;
    }

    .card p {
        margin: 0.7rem 0 0;
        color: var(--text-secondary);
    }

    @media (max-width: 900px) {
        .dashboard-hero {
            grid-template-columns: 1fr;
        }
    }
</style>
<?php
$page_styles = ob_get_clean();

ob_start();
?>
<div class="page-shell dashboard-shell">
    <section class="dashboard-hero glass-card">
        <div class="dashboard-copy">
            <span class="dashboard-kicker">
                <i class="fas fa-shield-halved"></i>
                <span>Panel seguro</span>
            </span>
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <p>Bienvenido al panel de control. Aquí puedes gestionar tu cuenta, revisar estadísticas y configurar tus preferencias de seguridad.</p>
        </div>

        <div class="dashboard-meta">
            <div class="meta-card">
                <span>Usuario</span>
                <strong><?php echo htmlspecialchars($user, ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="meta-card">
                <span>Rol</span>
                <strong><?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
        </div>
    </section>

    <section class="cards-grid">
        <article class="card glass-card">
            <i class="fas fa-chart-line" style="color: var(--accent);"></i>
            <h2 class="card-title">Stats</h2>
            <p>Visión general de tu actividad y rendimiento.</p>
        </article>

        <article class="card glass-card">
            <i class="fas fa-shield-alt" style="color: var(--success);"></i>
            <h2 class="card-title">Seguridad</h2>
            <p>Configura tus opciones de seguridad y privacidad.</p>
        </article>

        <article class="card glass-card">
            <i class="fas fa-cog" style="color: var(--warning);"></i>
            <h2 class="card-title">Config</h2>
            <p>Personaliza tu experiencia y preferencias.</p>
        </article>

        <?php if ($role === 'admin'): ?>
            <article class="card glass-card">
                <i class="fas fa-user-crown" style="color: var(--danger);"></i>
                <h2 class="card-title">Admin panel</h2>
                <p>Accede a herramientas avanzadas de administración.</p>
            </article>
        <?php endif; ?>
    </section>
</div>
<?php
$page_content = ob_get_clean();

ob_start();
?>
<script>
    gsap.from('.dashboard-hero', {
        duration: 0.85,
        y: 34,
        opacity: 0,
        ease: 'power3.out'
    });

    gsap.from('.card', {
        duration: 0.55,
        y: 38,
        opacity: 0,
        ease: 'power2.out',
        stagger: 0.12,
        delay: 0.15
    });
</script>
<?php
$page_scripts = ob_get_clean();

include 'layout.php';
