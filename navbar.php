<?php
include_once 'auth.php';

session_start();

// 👤 Usuario centralizado
$user_data = is_logged_in() ? get_user_data() : null;

// 🔐 Role seguro
$role = $user_data['role'] ?? null;

// 📄 Página actual
$current_page = basename($_SERVER['PHP_SELF']);

// 🏠 Home dinámico
$home_target = $user_data ? 'dashboard.php' : 'login.php';
?>

<nav class="navbar" id="navbar">
    <div class="nav-container">

        <a href="<?= $home_target ?>" class="nav-logo">
            <i class="fas fa-bolt"></i>
            <span>PANEL PREMIUM</span>
        </a>

        <div class="nav-links">

            <!-- 🔓 GUEST -->
            <?php if (!$user_data): ?>
                <a href="login.php" class="nav-link <?= $current_page === 'login.php' ? 'active' : '' ?>">
                    <i class="fas fa-right-to-bracket"></i> Login
                </a>

                <a href="signin.php" class="nav-link <?= $current_page === 'signin.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-plus"></i> Registro
                </a>
            <?php endif; ?>

            <!-- 📄 SIEMPRE -->
            <a href="formulario.php" class="nav-link <?= $current_page === 'formulario.php' ? 'active' : '' ?>">
                <i class="fas fa-shield-halved"></i> CSRF
            </a>

            <!-- 👤 LOGGED -->
            <?php if ($user_data): ?>

                <a href="dashboard.php" class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>

                <!-- 🟡 MODERADOR + ADMIN -->
                <?php if ($role === 'moderador' || $role === 'admin'): ?>
                    <a href="moderador.php" class="nav-link <?= $current_page === 'moderador.php' ? 'active' : '' ?>">
                        <i class="fas fa-user-check"></i> Moderación
                    </a>
                <?php endif; ?>

                <!-- 🔴 SOLO ADMIN -->
                <?php if ($role === 'admin'): ?>
                    <a href="admin.php" class="nav-link <?= $current_page === 'admin.php' ? 'active' : '' ?>">
                        <i class="fas fa-user-shield"></i> Admin
                    </a>
                <?php endif; ?>

            <?php endif; ?>
        </div>

        <div class="nav-actions">

            <?php if ($user_data): ?>
                <span class="nav-user">
                    <i class="fas fa-user"></i>
                    <?= htmlspecialchars($user_data['username']) ?>
                </span>

                <a href="logout.php" class="nav-link">
                    <i class="fas fa-arrow-right-from-bracket"></i> Salir
                </a>
            <?php endif; ?>

            <button class="theme-toggle" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>

        </div>
    </div>
</nav>