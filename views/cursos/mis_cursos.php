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

        <?php foreach ($inscripciones as $fila): ?>
            <?php
            $est = $fila['estado_inscripcion'];
            $nombreCurso = isset($fila['nombre']) ? $fila['nombre'] : 'Curso sin nombre';
            $codigo = isset($fila['codigo']) ? $fila['codigo'] : '---';

            // Simulación de datos para diseño (adaptar con datos reales si existen)
            $progreso = rand(0, 100);
            $fecha = isset($fila['fecha_inscripcion']) ? date('Y / n / j', strtotime($fila['fecha_inscripcion'])) : '2025 / 1 / 1';
            $modalidad = "Presencial/Mañana"; // Placeholder
    
            // Colores de tarjeta según el índice para variar (opcional)
            $cardColors = ['#1a1a1a', '#0f2e1b', '#1c1c1c'];
            $bgColor = $cardColors[rand(0, 2)];

            // Determinamos color de fondo específico si es que queremos variar por curso
            // Usando hash del nombre para mantener consistencia
            $hash = crc32($nombreCurso);
            $hue = $hash % 360;
            // Generar un color oscuro basado en el nombre
            $dynamicBg = "hsl($hue, 40%, 15%)";
            ?>

            <div class="col curso-item" data-nombre="<?php echo strtolower($nombreCurso); ?>" data-estado="<?php echo $est; ?>">
                <!-- Card con diseño Overlay -->
                <div class="card h-100 border-0 shadow-lg position-relative overflow-hidden text-white"
                    style="background-color: <?php echo $dynamicBg; ?>; border-radius: 12px; min-height: 350px;">

                    <!-- Fondo Semi-transparente o patrón -->
                    <div class="position-absolute w-100 h-100"
                        style="background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.8) 100%); z-index: 1;">
                    </div>

                    <!-- Menú de opciones (tres puntos) -->
                    <div class="position-absolute top-0 end-0 m-3" style="z-index: 2;">
                        <button class="btn btn-sm btn-dark rounded-circle bg-opacity-50 border-0">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>

                    <!-- Contenido Principal -->
                    <div class="card-body d-flex flex-column justify-content-center position-relative"
                        style="z-index: 2; margin-top: 40px;">

                        <h4 class="fw-bold mb-1" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                            <?php echo htmlspecialchars($nombreCurso); ?>
                        </h4>

                        <p class="mb-3 text-light opacity-75 fw-bold" style="font-size: 0.9rem;">
                            (<?php echo $modalidad; ?>/<?php echo htmlspecialchars($codigo); ?>)
                        </p>

                        <p class="text-white-50 mb-0" style="font-size: 0.85rem;">
                            <?php echo $fecha; ?>
                        </p>

                    </div>

                    <!-- Footer con Barra de Progreso -->
                    <div class="card-footer bg-transparent border-0 position-relative pb-4" style="z-index: 2;">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <span class="small fw-bold text-white"><?php echo $progreso; ?>% completado</span>
                            <a href="index.php?controller=Aula&action=index&id=<?php echo $fila['id']; ?>"
                                class="btn btn-sm btn-light px-3 rounded-pill fw-bold" style="font-size: 0.8rem;">
                                Aula <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="progress bg-secondary bg-opacity-25" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: <?php echo $progreso; ?>%; border-radius: 4px;"></div>
                        </div>
                    </div>
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