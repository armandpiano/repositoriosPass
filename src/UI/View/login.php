<?php

declare(strict_types=1);
ob_start();
?>
<div class="container premium-shell d-flex align-items-center" style="min-height:100vh;">
    <div class="w-100">
        <div class="premium-glass login-card text-white">
            <div class="text-center mb-4">
                <div class="login-brand-icon"><i class="bi bi-buildings-fill"></i></div>
                <h1 class="h3 fw-bold mb-1">Acceso Corporativo</h1>
                <p class="mb-0 text-light opacity-75">Repositorio de Proyectos PASS Consultores</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="premium-alert px-3 py-2 mb-3" role="alert">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8'); ?>" novalidate>
                <div class="mb-3">
                    <label class="form-label" for="username">Usuario</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-person-workspace"></i></span>
                        <input id="username" type="text" class="form-control" name="username" required minlength="3" placeholder="Ingresa tu usuario">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                        <input id="password" type="password" class="form-control" name="password" required minlength="8" placeholder="Ingresa tu contraseña">
                    </div>
                </div>
                <button class="btn btn-pro w-100 py-2" type="submit">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar
                </button>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$scripts = '';
include __DIR__ . '/layout.php';
