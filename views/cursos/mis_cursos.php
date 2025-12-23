<?php
$title = 'Mis Cursos';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #000000 0%, #0f2e1b 100%);
        border-bottom: 1px solid #198754;
    }

    /* Inputs y Tarjetas Oscuras */
    .form-control-dark {
        background-color: #121212;
        border: 1px solid #333;
        color: #e0e0e0;
    }

    .form-control-dark:focus {
        background-color: #121212;
        border-color: #198754;
        color: #fff;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }

    .card-dark {
        background-color: #1a1a1a;
        border: 1px solid #333;
        color: #e0e0e0;
        transition: transform 0.3s ease;
    }

    .card-dark:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(25, 135, 84, 0.15);
        border-color: #198754;
    }

    /* Botones de filtro */
    .btn-filter.active {
        background-color: #198754;
        color: white;
        border-color: #198754;
    }

    .btn-filter {
        background-color: #121212;
        color: #aaa;
        border-color: #333;
    }

    /* --- AJUSTES PARA CELULAR (Mobile) --- */
    @media (max-width: 768px) {
        .hero-section {
            text-align: center;
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
        }

        .hero-actions {
            margin-top: 1rem;
            display: block;
            width: 100%;
        }

        .hero-actions .btn {
            width: 100%;
            /* Botón "Explorar" ancho completo */
        }

        /* Filtros en celular ocupan todo el ancho y son más altos */
        .btn-group-mobile {
            display: flex;
            width: 100%;
        }

        .btn-group-mobile .btn {
            flex: 1;
            /* Distribuye espacio igual */
            padding: 10px 5px;
            font-size: 0.9rem;
        }
    }
</style>

<div class="hero-section text-white py-5 mb-4 shadow">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold"><i class="fas fa-graduation-cap me-2 text-success"></i>Mis Cursos</h1>
                <p class="text-light mb-0 opacity-75">Gestiona tu aprendizaje y visualiza tu progreso.</p>
            </div>
            <div class="col-md-4 text-md-end hero-actions">
                <a href="index.php?controller=Curso&action=index" class="btn btn-outline-success">
                    <i class="fas fa-plus me-2"></i> Explorar Catálogo
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">

    <?php if (isset($showUpsellBanner) && $showUpsellBanner): ?>
        <div class="alert alert-warning border-warning shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h4 class="alert-heading fw-bold mb-1">¡Mejora tu experiencia!</h4>
                    <p class="mb-0">Hemos notado que has tenido <?php echo $interruptionCount; ?> interrupciones en tu
                        sesión recientemente.</p>
                    <hr class="my-2">
                    <p class="mb-0"><?php echo $upsellMessage; ?></p>
                </div>
                <div class="flex-shrink-0 ms-3">
                    <a href="index.php?controller=Pago&action=planes" class="btn btn-warning fw-bold text-dark">
                        <i class="fas fa-arrow-circle-up me-1"></i> Ver Planes
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row mb-4 g-3 align-items-center">
        <div class="col-md-6">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-dark border-secondary text-secondary"><i
                        class="fas fa-search"></i></span>
                <input type="text" id="inputBuscador" class="form-control form-control-dark"
                    placeholder="Buscar curso...">
            </div>
        </div>

        <div class="col-md-6 text-md-end">
            <div class="btn-group shadow-sm btn-group-mobile" role="group">
                <button type="button" class="btn btn-filter active"
                    onclick="filtrarCursos('todos', this)">Todos</button>
                <button type="button" class="btn btn-filter" onclick="filtrarCursos('inscrito', this)">En Curso</button>
                <button type="button" class="btn btn-filter" onclick="filtrarCursos('aprobado', this)">Listos</button>
            </div>
        </div>
    </div>

    <?php if (empty($inscripciones)): ?>
        <div class="text-center py-5 rounded-3 border border-secondary border-dashed bg-dark mt-4 mx-2">
            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
            <h3 class="fw-bold text-white">No tienes cursos</h3>
            <p class="text-muted">Inscríbete en tu primer curso para empezar.</p>
            <a href="index.php?controller=Curso&action=index" class="btn btn-success mt-2 px-4 py-2">Ir al Catálogo</a>
        </div>
    <?php else: ?>

        <?php
        // Definir una paleta de degradados premium para las tarjetas
        $gradients = [
            'linear-gradient(135deg, #1e2024 0%, #23272b 100%)', // Dark Modern
            'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)', // Slate Blue
            'linear-gradient(135deg, #18181b 0%, #27272a 100%)', // Zinc
            'linear-gradient(135deg, #14532d 0%, #166534 100%)', // Dark Green (Subtle)
            'linear-gradient(135deg, #312e81 0%, #3730a3 100%)', // Indigo Night
        ];
        ?>

        <?php foreach ($inscripciones as $index => $fila): ?>
            <?php
            $est = $fila['estado_inscripcion'];
            $nombreCurso = isset($fila['nombre']) ? $fila['nombre'] : 'Curso sin nombre';
            $codigo = isset($fila['codigo']) ? $fila['codigo'] : '---';

            // --- CÁLCULO DE PROGRESO BASADO EN FECHAS ---
            $now = time();
            $fechaInicioStr = $fila['fecha_inicio'] ?? null;
            $fechaFinStr = $fila['fecha_fin'] ?? null;

            $start = $fechaInicioStr ? strtotime($fechaInicioStr) : 0;
            $end = $fechaFinStr ? strtotime($fechaFinStr) : 0;

            $progreso = 0;
            $statusText = "Por iniciar";
            $statusColor = "secondary"; // default
    
            if ($start > 0 && $end > 0 && $end > $start) {
                if ($now >= $end) {
                    // Curso finalizado
                    $progreso = 100;
                    $statusText = "Finalizado";
                    $statusColor = "success";
                } elseif ($now < $start) {
                    // Aún no empieza
                    $progreso = 0;
                    $statusText = "Próximamente";
                    $statusColor = "info";
                } else {
                    // En progreso
                    $totalSeconds = $end - $start;
                    $elapsed = $now - $start;
                    $progreso = round(($elapsed / $totalSeconds) * 100);
                    $statusText = "En curso";
                    $statusColor = "primary";
                }
            } else {
                // Fechas inválidas, asumimos 0
                $progreso = 0;
            }

            // Si ya está aprobado manualmente, forzamos 100%
            if ($est === 'aprobado') {
                $progreso = 100;
                $statusText = "Aprobado";
                $statusColor = "success";
            }

            $fechaMostrar = isset($fila['fecha_inicio']) ? date('d M, Y', strtotime($fila['fecha_inicio'])) : '---';
            $modalidad = "Virtual"; // Placeholder fijo por el momento o dinámico si existe en DB
    
            // Asignar gradiente basado en índice para variedad ordenada
            $bgGradient = $gradients[$index % count($gradients)];
            ?>

            <div class="col curso-item" data-nombre="<?php echo strtolower($nombreCurso); ?>" data-estado="<?php echo $est; ?>">
                <!-- Card con diseño Premium -->
                <div class="card h-100 border-0 shadow-lg position-relative overflow-hidden text-white"
                    style="background: <?php echo $bgGradient; ?>; border-radius: 16px; min-height: 320px; transition: transform 0.3s ease, box-shadow 0.3s ease;">

                    <!-- Hover Effect Trigger (CSS needed elsewhere or inline) -->
                    <style>
                        .curso-item:hover .card {
                            transform: translateY(-5px);
                            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3) !important;
                        }

                        .curso-item:hover .btn-light {
                            background-color: #fff;
                            transform: scale(1.05);
                        }
                    </style>

                    <!-- Header de la Card -->
                    <div class="p-4 d-flex flex-column h-100">

                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-dark bg-opacity-50 border border-secondary text-white-50 rounded-pill px-3">
                                <i class="far fa-calendar-alt me-1"></i> <?php echo $fechaMostrar; ?>
                            </span>

                            <!-- Menú (Visual) -->
                            <i class="fas fa-ellipsis-h text-white-50"></i>
                        </div>

                        <div class="mb-auto">
                            <h3 class="fw-bold mb-1 text-white" style="letter-spacing: -0.5px;">
                                <?php echo htmlspecialchars($nombreCurso); ?>
                            </h3>
                            <p class="text-white-50 mb-0 small">
                                <?php echo htmlspecialchars($codigo); ?> • <?php echo $modalidad; ?>
                            </p>
                        </div>

                        <!-- Footer Info y Progreso -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div>
                                    <span class="d-block h2 fw-bold mb-0 text-white"><?php echo $progreso; ?>%</span>
                                    <small class="text-<?php echo ($progreso == 100) ? 'success' : 'light'; ?> opacity-75">
                                        <?php echo $statusText; ?>
                                    </small>
                                </div>
                                <a href="index.php?controller=Aula&action=index&id=<?php echo $fila['id']; ?>"
                                    class="btn btn-light rounded-pill px-4 fw-bold shadow-sm border-0 d-flex align-items-center">
                                    Aula <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>

                            <div class="progress bg-white bg-opacity-10" style="height: 6px; border-radius: 10px;">
                                <div class="progress-bar bg-<?php echo $statusColor; ?>" role="progressbar"
                                    style="width: <?php echo $progreso; ?>%; border-radius: 10px; transition: width 1s ease-in-out;">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Decoración de Fondo (Círculos sutiles) -->
                    <div class="position-absolute top-0 end-0 translate-middle-y me-n4 mt-n4 rounded-circle bg-white bg-opacity-10"
                        style="width: 150px; height: 150px; filter: blur(40px);"></div>
                    <div class="position-absolute bottom-0 start-0 translate-middle-y ms-n4 mb-n4 rounded-circle bg-black bg-opacity-25"
                        style="width: 200px; height: 200px; filter: blur(40px);"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
    // Buscador
    document.getElementById('inputBuscador').addEventListener('keyup', function () {
        let texto = this.value.toLowerCase();
        let cursos = document.querySelectorAll('.curso-item');
        cursos.forEach(curso => {
            let nombre = curso.getAttribute('data-nombre');
            curso.style.display = nombre.includes(texto) ? 'block' : 'none';
        });
    });

    // Filtros
    function filtrarCursos(estadoFiltro, botonClick) {
        document.querySelectorAll('.btn-filter').forEach(btn => btn.classList.remove('active'));
        botonClick.classList.add('active');

        let cursos = document.querySelectorAll('.curso-item');
        cursos.forEach(curso => {
            let estadoCurso = curso.getAttribute('data-estado');
            if (estadoFiltro === 'todos' || estadoCurso === estadoFiltro) {
                curso.style.display = 'block';
            } else {
                curso.style.display = 'none';
            }
        });
    }
</script>