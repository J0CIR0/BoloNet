<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Finalización</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .certificado-container {
            width: 900px;
            height: 600px;
            background-color: white;
            padding: 20px;
            position: relative;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            border: 20px solid #2c3e50;
            text-align: center;
            background-image: url('https://www.transparenttextures.com/patterns/cream-paper.png');
        }

        .borde-interno {
            border: 5px double #c0392b;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .header-cert {
            font-size: 48px;
            color: #2c3e50;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .sub-header {
            font-size: 24px;
            color: #7f8c8d;
            margin-bottom: 40px;
            font-style: italic;
        }

        .presentado-a {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .nombre-estudiante {
            font-size: 42px;
            color: #c0392b;
            font-family: 'Pinyon Script', cursive;
            border-bottom: 1px solid #7f8c8d;
            display: inline-block;
            min-width: 400px;
            margin-bottom: 30px;
            padding-bottom: 5px;
        }

        .texto-cuerpo {
            font-size: 18px;
            line-height: 1.6;
            color: #34495e;
            margin-bottom: 50px;
        }

        .curso-nombre {
            font-weight: bold;
            font-size: 22px;
        }

        .footer-cert {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
        }

        .firma {
            border-top: 1px solid #2c3e50;
            width: 200px;
            padding-top: 10px;
            font-size: 16px;
        }

        .medalla {
            position: absolute;
            bottom: 40px;
            right: 40px;
            color: #f1c40f;
            font-size: 80px;
            text-shadow: 2px 2px 2px rgba(0, 0, 0, 0.2);
        }

        @media print {
            body {
                background: none;
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }

        @import url('https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap');
    </style>
</head>

<body>
    <div class="no-print" style="position: fixed; top: 20px; left: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #2980b9; color: white; border: none; cursor: pointer; border-radius: 5px;">
            <i class="fas fa-print"></i> Imprimir / Guardar PDF
        </button>
        <button onclick="window.history.back()"
            style="padding: 10px 20px; background: #7f8c8d; color: white; border: none; cursor: pointer; border-radius: 5px; margin-left: 10px;">
            <i class="fas fa-arrow-left"></i> Volver
        </button>
    </div>

    <div class="certificado-container">
        <div class="borde-interno">
            <h1 class="header-cert">Certificado de Finalización</h1>
            <p class="sub-header">Se otorga el presente reconocimiento a:</p>

            <h2 class="nombre-estudiante" style="font-family: 'Pinyon Script', cursive;">
                <?php echo htmlspecialchars($inscripcion['nombre'] . ' ' . $inscripcion['apellido']); ?>
            </h2>

            <p class="texto-cuerpo">
                Por haber completado y aprobado satisfactoriamente el curso de:<br>
                <span class="curso-nombre"><?php echo htmlspecialchars($curso->nombre); ?></span><br>
                Con una calificación final de: <strong><?php echo $inscripcion['nota_final']; ?> / 100</strong>
            </p>

            <div class="footer-cert">
                <div class="firma">
                    <strong>BoloNet Academy</strong><br>
                    Director Académico
                </div>
                <div class="firma">
                    <strong><?php echo date('d/m/Y', strtotime($inscripcion['fecha_inscripcion'])); ?></strong><br>
                    Fecha de Emisión
                </div>
            </div>

            <div class="medalla">
                <i class="fas fa-award"></i>
            </div>
        </div>
    </div>
</body>

</html>