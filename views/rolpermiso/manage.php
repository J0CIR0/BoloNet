<?php
$title = 'Asignar Permisos: ' . htmlspecialchars($rol['nombre']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Asignar Permisos a: <?php echo htmlspecialchars($rol['nombre']); ?></h2>
    <a href="rolpermiso.php" class="btn btn-secondary">Volver</a>
</div>

<?php if(isset($_SESSION['success'])): ?>
<div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Permisos Disponibles</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <?php 
                $modulos = [];
                foreach($permisos as $permiso) {
                    if(!isset($modulos[$permiso['modulo']])) {
                        $modulos[$permiso['modulo']] = [];
                    }
                    $modulos[$permiso['modulo']][] = $permiso;
                }
                ?>
                
                <?php foreach($modulos as $modulo_nombre => $permisos_modulo): ?>
                <div class="col-md-4 mb-4">
                    <div class="card bg-dark">
                        <div class="card-header bg-black">
                            <h5 class="mb-0"><?php echo htmlspecialchars($modulo_nombre); ?></h5>
                        </div>
                        <div class="card-body">
                            <?php foreach($permisos_modulo as $permiso): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permisos[]" 
                                       value="<?php echo $permiso['id']; ?>" 
                                       id="permiso_<?php echo $permiso['id']; ?>"
                                       <?php echo in_array($permiso['id'], $permisos_asignados) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="permiso_<?php echo $permiso['id']; ?>">
                                    <strong><?php echo htmlspecialchars($permiso['nombre']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($permiso['descripcion']); ?></small>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-success">Guardar Asignación</button>
                <a href="rolpermiso.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>