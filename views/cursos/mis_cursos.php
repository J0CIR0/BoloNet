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
            width: 100%; /* Botón "Explorar" ancho completo */
        }
        
        /* Filtros en celular ocupan todo el ancho y son más altos */
        .btn-group-mobile {
            display: flex;
            width: 100%;
        }
        .btn-group-mobile .btn {
            flex: 1; /* Distribuye espacio igual */
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

    <div class="row mb-4 g-3 align-items-center">
        <div class="col-md-6">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-search"></i></span>
                <input type="text" id="inputBuscador" class="form-control form-control-dark" placeholder="Buscar curso...">
            </div>
        </div>
        
        <div class="col-md-6 text-md-end">
            <div class="btn-group shadow-sm btn-group-mobile" role="group">
                <button type="button" class="btn btn-filter active" onclick="filtrarCursos('todos', this)">Todos</button>
                <button type="button" class="btn btn-filter" onclick="filtrarCursos('inscrito', this)">En Curso</button>
                <button type="button" class="btn btn-filter" onclick="filtrarCursos('aprobado', this)">Listos</button>
            </div>
        </div>
    </div>

    <?php if(empty($inscripciones)): ?>
        <div class="text-center py-5 rounded-3 border border-secondary border-dashed bg-dark mt-4 mx-2">
            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
            <h3 class="fw-bold text-white">No tienes cursos</h3>
            <p class="text-muted">Inscríbete en tu primer curso para empezar.</p>
            <a href="index.php?controller=Curso&action=index" class="btn btn-success mt-2 px-4 py-2">Ir al Catálogo</a>
        </div>
    <?php else: ?>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="contenedorCursos">
            <?php foreach($inscripciones as $fila): ?>
                <?php 
                    $est = $fila['estado_inscripcion'];
                    $nombreCurso = strtolower($fila['nombre']);

                    $progreso = rand(10, 80);
                    $badgeClass = 'bg-primary bg-opacity-75';
                    $btnTexto = 'Continuar';
                    $btnClass = 'btn-success';
                    $iconoBtn = 'fa-play';
                    $colorBarra = 'bg-success';

                    if ($est == 'aprobado') {
                        $progreso = 100;
                        $badgeClass = 'bg-success';
                        $btnTexto = 'Certificado';
                        $iconoBtn = 'fa-certificate';
                        $btnClass = 'btn-outline-success';
                    } elseif ($est == 'reprobado') {
                        $progreso = 100;
                        $badgeClass = 'bg-danger';
                        $btnTexto = 'Repasar';
                        $iconoBtn = 'fa-redo';
                        $btnClass = 'btn-outline-danger';
                        $colorBarra = 'bg-danger';
                    }

                    $imgSeed = $fila['id'] ?? rand(1, 1000);
                    $imgUrl = "https://picsum.photos/seed/{$imgSeed}/400/220";
                ?>

                <div class="col curso-item" data-nombre="<?php echo htmlspecialchars($nombreCurso); ?>" data-estado="<?php echo $est; ?>">
                    <div class="card card-dark h-100 shadow-sm">
                        <div class="position-relative">
                            <img src="<?php echo $imgUrl; ?>" class="card-img-top" alt="Portada" style="height: 180px; object-fit: cover; opacity: 0.8;">
                            <span class="position-absolute top-0 end-0 m-3 badge <?php echo $badgeClass; ?> shadow">
                                <?php echo ucfirst($est); ?>
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-light mb-1 text-truncate">
                                <?php echo htmlspecialchars($fila['nombre']); ?>
                            </h5>
                            
                            <p class="text-muted small mb-3">
                                <i class="fas fa-barcode me-1 text-success"></i> <?php echo htmlspecialchars($fila['codigo']); ?>
                            </p>

                            <p class="card-text text-secondary small flex-grow-1">
                                <?php 
                                    $desc = strip_tags($fila['descripcion']);
                                    echo strlen($desc) > 80 ? substr($desc, 0, 80) . '...' : $desc;
                                ?>
                            </p>

                            <div class="mt-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Progreso</span>
                                    <span class="text-white fw-bold"><?php echo $progreso; ?>%</span>
                                </div>
                                <div class="progress bg-secondary" style="height: 6px;">
                                    <div class="progress-bar <?php echo $colorBarra; ?>" role="progressbar" style="width: <?php echo $progreso; ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-top border-secondary p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if($fila['nota_final'] > 0): ?>
                                    <span class="badge bg-dark border border-secondary text-light">Nota: <?php echo $fila['nota_final']; ?></span>
                                <?php else: ?>
                                    <small class="text-muted">--</small>
                                <?php endif; ?>

                                <a href="#" class="btn btn-sm <?php echo $btnClass; ?> px-3 rounded-pill fw-bold">
                                    <i class="fas <?php echo $iconoBtn; ?> me-1"></i> <?php echo $btnTexto; ?>
                                </a>
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
    document.getElementById('inputBuscador').addEventListener('keyup', function() {
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