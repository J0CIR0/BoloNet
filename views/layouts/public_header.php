<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #121212 !important; color: #ffffff !important; }
        .navbar { background-color: #000000 !important; }
        .navbar-brand { color: #28a745 !important; }
        .card { background-color: #1e1e1e !important; border-color: #28a745 !important; }
        .card-header { background-color: #000000 !important; color: #28a745 !important; }
        .btn-success { background-color: #28a745; border-color: #28a745; }
        .btn-outline-success { color: #28a745; border-color: #28a745; }
        .btn-outline-success:hover { background-color: #28a745; color: #000000; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?php echo SITE_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'public_cursos.php' ? 'active' : ''; ?>" href="public_cursos.php">Cursos</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Registrarse</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_SESSION['error'])): ?>
    Swal.fire({
        title: 'Error',
        text: '<?php echo str_replace("'", "\\'", $_SESSION['error']); ?>',
        icon: 'error',
        confirmButtonColor: '#28a745',
        background: '#121212',
        color: '#ffffff'
    });
    <?php unset($_SESSION['error']); endif; ?>
    <?php if(isset($_SESSION['success'])): ?>
    Swal.fire({
        title: 'Éxito',
        text: '<?php echo str_replace("'", "\\'", $_SESSION['success']); ?>',
        icon: 'success',
        confirmButtonColor: '#28a745',
        background: '#121212',
        color: '#ffffff'
    });
    <?php unset($_SESSION['success']); endif; ?>
    <?php if(isset($_SESSION['warning'])): ?>
    Swal.fire({
        title: 'Advertencia',
        text: '<?php echo str_replace("'", "\\'", $_SESSION['warning']); ?>',
        icon: 'warning',
        confirmButtonColor: '#28a745',
        background: '#121212',
        color: '#ffffff'
    });
    <?php unset($_SESSION['warning']); endif; ?>
});
</script>