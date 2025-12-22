<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-dark shadow-lg border-success">
            <div
                class="card-header bg-dark border-bottom border-success d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-success"><i class="fas fa-user-circle me-2"></i>Mi Perfil</h4>
                <span class="badge bg-secondary"><?php echo htmlspecialchars($perfil_data['rol_nombre']); ?></span>
            </div>
            <div class="card-body bg-dark text-white">

                <form action="index.php?controller=Usuario&action=perfil" method="POST">

                    <h5 class="text-white-50 mb-3 border-bottom pb-2">Información Personal (Solo lectura)</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Nombre Completo</label>
                            <input type="text" class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($perfil_data['persona_nombre'] . ' ' . $perfil_data['persona_apellido']); ?>"
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Cédula de Identidad</label>
                            <input type="text" class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($perfil_data['ci']); ?>" readonly>
                        </div>
                    </div>

                    <h5 class="text-white-50 mb-3 mt-4 border-bottom pb-2">Datos de Contacto (Editable)</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control bg-dark text-white border-secondary"
                                value="<?php echo htmlspecialchars($perfil_data['email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono / WhatsApp</label>
                            <input type="text" name="telefono" class="form-control bg-dark text-white border-secondary"
                                value="<?php echo isset($perfil_data['telefono']) ? htmlspecialchars($perfil_data['telefono']) : ''; ?>">
                        </div>
                    </div>

                    <h5 class="text-white-50 mb-3 mt-4 border-bottom pb-2">Seguridad (Opcional)</h5>

                    <div class="alert alert-dark border border-secondary text-muted small">
                        <i class="fas fa-info-circle me-1"></i> Deja estos campos vacíos si no deseas cambiar tu
                        contraseña.
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password"
                                class="form-control bg-dark text-white border-secondary" placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="confirm_password"
                                class="form-control bg-dark text-white border-secondary" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i> Guardar Cambios
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <?php if ($perfil_data['plan_type']): ?>
            <div class="card card-dark shadow-sm border-secondary mt-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Suscripción Actual</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-success fw-bold text-uppercase mb-0">
                                <?php echo htmlspecialchars($perfil_data['plan_type']); ?>
                            </h4>
                            <small class="text-white-50">Estado:
                                <?php echo htmlspecialchars($perfil_data['subscription_status']); ?></small>
                        </div>
                        <?php if ($perfil_data['subscription_end']): ?>
                            <div class="text-end">
                                <span class="d-block text-white-50 small">Válido hasta</span>
                                <span
                                    class="fw-bold"><?php echo date('d/m/Y', strtotime($perfil_data['subscription_end'])); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>