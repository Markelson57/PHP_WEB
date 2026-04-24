<?php
include_once 'auth.php';

$current_page = basename($_SERVER['PHP_SELF']);
$user_data = is_logged_in() ? get_user_data() : null;
$home_target = $user_data ? 'dashboard.php' : 'login.php';
?>
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="<?php echo $home_target; ?>" class="nav-logo">
            <i class="fas fa-bolt"></i>
            <span>PANEL PREMIUM</span>
        </a>

        <div class="nav-links">
            <?php if (!$user_data): ?>
                <a href="login.php" class="nav-link <?php echo $current_page === 'login.php' ? 'active' : ''; ?>">
                    <i class="fas fa-right-to-bracket"></i>
                    <span>Login</span>
                </a>
                <a href="signin.php" class="nav-link <?php echo $current_page === 'signin.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-plus"></i>
                    <span>Registro</span>
                </a>
            <?php endif; ?>

            <a href="formulario.php" class="nav-link <?php echo $current_page === 'formulario.php' ? 'active' : ''; ?>">
                <i class="fas fa-shield-halved"></i>
                <span>CSRF</span>
            </a>

            <?php if ($user_data): ?>
                <a href="dashboard.php" class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>

                <?php if (($user_data['role'] ?? '') === 'admin'): ?>
                    <a href="admin.php" class="nav-link <?php echo $current_page === 'admin.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin</span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="nav-actions">
            <?php if ($user_data): ?>
                <span class="nav-user">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($user_data['user'], ENT_QUOTES, 'UTF-8'); ?></span>
                </span>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                    <span>Salir</span>
                </a>
            <?php endif; ?>

            <button class="theme-toggle" id="themeToggle" type="button" title="Cambiar tema" aria-label="Cambiar tema">
                <i class="fas fa-moon"></i>
            </button>
            <button class="menu-toggle" id="menuToggle" type="button" aria-expanded="false" aria-controls="mobileMenu" aria-label="Abrir menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <div class="mobile-menu" id="mobileMenu">
        <?php if (!$user_data): ?>
            <a href="login.php" class="nav-link <?php echo $current_page === 'login.php' ? 'active' : ''; ?>">
                <i class="fas fa-right-to-bracket"></i>
                <span>Login</span>
            </a>
            <a href="signin.php" class="nav-link <?php echo $current_page === 'signin.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-plus"></i>
                <span>Registro</span>
            </a>
        <?php endif; ?>

        <a href="formulario.php" class="nav-link <?php echo $current_page === 'formulario.php' ? 'active' : ''; ?>">
            <i class="fas fa-shield-halved"></i>
            <span>CSRF</span>
        </a>

        <?php if ($user_data): ?>
            <a href="dashboard.php" class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            <?php if (($user_data['role'] ?? '') === 'admin'): ?>
                <a href="admin.php" class="nav-link <?php echo $current_page === 'admin.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </a>
            <?php endif; ?>

            <span class="nav-user">
                <i class="fas fa-user"></i>
                <span><?php echo htmlspecialchars($user_data['user'], ENT_QUOTES, 'UTF-8'); ?></span>
            </span>

            <a href="logout.php" class="nav-link">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span>Salir</span>
            </a>
        <?php endif; ?>
    </div>
</nav>
