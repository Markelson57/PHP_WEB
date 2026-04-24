<?php
include 'pantalla de carga.php';
session_start();
$page_title = $page_title ?? 'Panel Premium';
$page_styles = $page_styles ?? '';
$page_content = $page_content ?? '';
$page_scripts = $page_scripts ?? '';
$page_main_class = trim($page_main_class ?? 'content-pad content-pad--wide');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="shared.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <?php echo $page_styles; ?>
</head>
<body data-theme="dark">
    <?php include 'navbar.php'; ?>

    <main class="<?php echo htmlspecialchars($page_main_class, ENT_QUOTES, 'UTF-8'); ?>">
        <?php echo $page_content; ?>
    </main>

    <script>
        (function () {
            const body = document.body;
            const navbar = document.getElementById('navbar');
            const themeToggle = document.getElementById('themeToggle');
            const menuToggle = document.getElementById('menuToggle');
            const mobileMenu = document.getElementById('mobileMenu');

            const applyTheme = (theme) => {
                body.setAttribute('data-theme', theme);
                const icon = themeToggle ? themeToggle.querySelector('i') : null;
                if (icon) {
                    icon.classList.toggle('fa-moon', theme === 'dark');
                    icon.classList.toggle('fa-sun', theme === 'light');
                }
                localStorage.setItem('theme', theme);
            };

            const closeMobileMenu = () => {
                if (!mobileMenu || !menuToggle) {
                    return;
                }

                mobileMenu.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');

                const icon = menuToggle.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                }
            };

            const savedTheme = localStorage.getItem('theme') || 'dark';
            applyTheme(savedTheme);

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const nextTheme = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    applyTheme(nextTheme);
                });
            }

            if (menuToggle && mobileMenu) {
                menuToggle.addEventListener('click', () => {
                    const isActive = mobileMenu.classList.toggle('active');
                    menuToggle.setAttribute('aria-expanded', isActive ? 'true' : 'false');

                    const icon = menuToggle.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-bars', !isActive);
                        icon.classList.toggle('fa-times', isActive);
                    }
                });

                document.querySelectorAll('#mobileMenu .nav-link').forEach((link) => {
                    link.addEventListener('click', closeMobileMenu);
                });

                window.addEventListener('resize', () => {
                    if (window.innerWidth > 768) {
                        closeMobileMenu();
                    }
                });
            }

            if (navbar) {
                const syncNavbar = () => {
                    navbar.classList.toggle('scrolled', window.scrollY > 24);
                };

                syncNavbar();
                window.addEventListener('scroll', syncNavbar, { passive: true });
            }
        })();
    </script>
    <?php echo $page_scripts; ?>
</body>
</html>
