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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #121212 !important;
            color: #ffffff !important;
        }

        .navbar {
            background-color: #000000 !important;
        }

        .navbar-brand {
            color: #28a745 !important;
        }

        .sidebar {
            background-color: #000000;
            min-height: calc(100vh - 56px);
            padding-top: 20px;
        }

        .sidebar a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
        }

        .sidebar a:hover {
            background-color: #28a745;
            color: #000000;
        }

        .card {
            background-color: #1e1e1e !important;
            border-color: #28a745 !important;
        }

        .card-header {
            background-color: #000000 !important;
            color: #28a745 !important;
        }

        .table-dark {
            background-color: #1e1e1e !important;
        }

        .table-dark th {
            background-color: #000000 !important;
            color: #28a745 !important;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .form-control {
            background-color: #2d2d2d !important;
            border-color: #444 !important;
            color: #ffffff !important;
        }

        .form-control:focus {
            background-color: #2d2d2d !important;
            border-color: #28a745 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><?php echo SITE_NAME; ?></a>
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="nav-link"><?php echo $_SESSION['user_name']; ?>
                        (<?php echo $_SESSION['user_role']; ?>)</span>
                    <a class="nav-link" href="index.php?controller=Usuario&action=perfil">Mi Perfil</a>
                    <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2 sidebar">
                    <div class="position-sticky pt-3">
                        <a href="dashboard.php"
                            class="<?php echo $current_page == 'dashboard.php' ? 'bg-success text-dark' : ''; ?>">Dashboard</a>
                        <?php
                        require_once __DIR__ . '/../../models/Usuario.php';
                        $usuario = new Usuario();
                        if ($usuario->hasPermission($_SESSION['user_id'], 'ver_usuarios')):
                            ?>
                            <a href="usuarios.php"
                                class="<?php echo $current_page == 'usuarios.php' ? 'bg-success text-dark' : ''; ?>">Usuarios</a>
                        <?php endif; ?>
                        <?php if ($usuario->hasPermission($_SESSION['user_id'], 'ver_personas')): ?>
                            <a href="personas.php"
                                class="<?php echo $current_page == 'personas.php' ? 'bg-success text-dark' : ''; ?>">Personas</a>
                        <?php endif; ?>
                        <?php if ($usuario->hasPermission($_SESSION['user_id'], 'ver_cursos')): ?>
                            <a href="cursos.php"
                                class="<?php echo $current_page == 'cursos.php' ? 'bg-success text-dark' : ''; ?>">Cursos</a>
                        <?php endif; ?>
                        <?php if ($usuario->hasPermission($_SESSION['user_id'], 'ver_roles')): ?>
                            <a href="roles.php"
                                class="<?php echo $current_page == 'roles.php' ? 'bg-success text-dark' : ''; ?>">Roles</a>
                        <?php endif; ?>
                        <?php if ($usuario->hasPermission($_SESSION['user_id'], 'ver_permisos')): ?>
                            <a href="permisos.php"
                                class="<?php echo $current_page == 'permisos.php' ? 'bg-success text-dark' : ''; ?>">Permisos</a>
                        <?php endif; ?>
                        <?php if ($usuario->hasPermission($_SESSION['user_id'], 'asignar_permisos')): ?>
                            <a href="rolpermiso.php"
                                class="<?php echo $current_page == 'rolpermiso.php' ? 'bg-success text-dark' : ''; ?>">Asignar
                                Permisos</a>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2)): ?>
                            <a href="index.php?controller=Session&action=index"
                                class="<?php echo $current_page == 'index.php?controller=Session&action=index' ? 'bg-success text-dark' : ''; ?>">
                                Monitor Sesiones
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-10 p-4">
                <?php endif; ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        <?php if (isset($_SESSION['error'])): ?>
                            Swal.fire({
                                title: 'Error',
                                text: '<?php echo str_replace("'", "\\'", $_SESSION['error']); ?>',
                                icon: 'error',
                                confirmButtonColor: '#28a745',
                                background: '#121212',
                                color: '#ffffff'
                            });
                            <?php unset($_SESSION['error']); endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            Swal.fire({
                                title: 'Éxito',
                                text: '<?php echo str_replace("'", "\\'", $_SESSION['success']); ?>',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                background: '#121212',
                                color: '#ffffff'
                            });
                            <?php unset($_SESSION['success']); endif; ?>
                        <?php if (isset($_SESSION['warning'])): ?>
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