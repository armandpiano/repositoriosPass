<?php

declare(strict_types=1);
ob_start();
?>
<div class="container py-5 d-flex align-items-center" style="min-height:100vh;">
    <div class="row justify-content-center w-100">
        <div class="col-lg-5 col-md-8">
            <div class="glass-card rounded-4 p-4 p-md-5 text-white">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-lock-fill" style="font-size:3rem;"></i>
                    <h1 class="h3 fw-bold mt-2 mb-1">Acceso Seguro</h1>
                    <p class="mb-0 opacity-75">Dashboard Prosa Natural / Pass</p>
                </div>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
                <form method="post" action="/login" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" name="username" required minlength="3" placeholder="Ingresa tu usuario">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Contrasena</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" class="form-control" name="password" required minlength="8" placeholder="Ingresa tu contrasena">
                        </div>
                    </div>
                    <button class="btn btn-light text-primary fw-bold w-100 py-2 shadow" type="submit">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar al Dashboard
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$scripts = '';
include __DIR__ . '/layout.php';
