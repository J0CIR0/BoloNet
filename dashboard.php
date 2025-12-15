<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/constantes.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$title = 'Dashboard';
require_once 'views/layouts/header.php';
?>

<div class="container-fluid p-4">
    <h2 class="mb-4">Dashboard</h2>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4>Bienvenido, <?php echo $_SESSION['user_name']; ?></h4>
                    <p>Rol: <?php echo $_SESSION['user_role']; ?></p>
                    <p>Email: <?php echo $_SESSION['user_email']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>