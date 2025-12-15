<?php
$title = 'Restablecer Contraseña';
require_once __DIR__ . '/../layouts/header.php';

if(!isset($_SESSION['reset_email'])) {
    header('Location: forgot_password.php');
    exit();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Restablecer Contraseña</h4>
                </div>
                <div class="card-body">
                    
                    <?php if(!isset($_SESSION['reset_valid'])): ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Código de 6 dígitos</label>
                            <input type="text" name="codigo" class="form-control text-center" 
                                   maxlength="6" pattern="[0-9]{6}" required 
                                   style="font-size: 24px; letter-spacing: 10px;">
                            <small class="text-muted">Se envió a: <?php echo $_SESSION['reset_email']; ?></small>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Verificar Código</button>
                    </form>
                    <?php else: ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Cambiar Contraseña</button>
                    </form>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>