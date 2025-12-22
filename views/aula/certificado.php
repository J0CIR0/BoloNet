<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Finalización - <?php echo htmlspecialchars($cursoData['nombre']); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Great+Vibes&family=Roboto:wght@300;400;500&display=swap');

        body {
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .certificate-container {
            width: 900px;
            /* A4 Landscape approx width in px for screen */
            height: 640px;
            background-color: white;
            padding: 40px;
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border: 20px solid #2c3e50;
            text-align: center;
            color: #333;
        }

        .inner-border {
            border: 2px solid #daa520;
            /* Gold */
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header h1 {
            font-family: 'Cinzel', serif;
            font-size: 48px;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: #2c3e50;
            margin-bottom: 10px;
            margin-top: 0;
        }

        .header h2 {
            font-family: 'Roboto', sans-serif;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #daa520;
            margin-top: 0;
        }

        .content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .presented-to {
            font-style: italic;
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        .student-name {
            font-family: 'Great Vibes', cursive;
            font-size: 64px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            display: inline-block;
            padding-bottom: 10px;
            margin-bottom: 30px;
            min-width: 500px;
        }

        .course-text {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .course-name {
            font-weight: bold;
            font-size: 28px;
            color: #2c3e50;
            margin: 10px 0;
        }

        .footer {
            display: flex;
            justify-content: space-around;
            margin-top: 50px;
        }

        .signature {
            border-top: 1px solid #333;
            padding-top: 10px;
            width: 250px;
            text-align: center;
        }

        .signature p {
            margin: 5px 0 0;
            font-weight: bold;
            font-size: 14px;
        }

        .signature span {
            display: block;
            font-size: 12px;
            color: #7f8c8d;
        }

        .date-info {
            font-size: 12px;
            color: #999;
            margin-top: 20px;
        }

        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2c3e50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-family: sans-serif;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-print:hover {
            background: #34495e;
        }

        @media print {
            body {
                background: none;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .certificate-container {
                box-shadow: none;
                width: 100%;
                height: 100vh;
                border: 20px solid #2c3e50;
                page-break-inside: avoid;
            }

            .btn-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="btn-print">Imprimir / Guardar PDF</button>

    <div class="certificate-container">
        <div class="inner-border">

            <div class="header">
                <h1>Certificado</h1>
                <h2>De Finalización</h2>
            </div>

            <div class="content">
                <p class="presented-to">Este certificado se otorga a:</p>

                <div class="student-name">
                    <?php echo htmlspecialchars($nombreEstudiante); ?>
                </div>

                <div class="course-text">
                    Por haber completado satisfactoriamente el curso:
                </div>

                <div class="course-name">
                    <?php echo htmlspecialchars($cursoData['nombre']); ?>
                </div>

                <p style="margin-top: 20px; font-size: 14px; color: #555;">
                    Duración: <?php echo htmlspecialchars($cursoData['duracion_horas']); ?> horas &bull;
                    Fecha Fin: <?php echo date('d/m/Y', strtotime($cursoData['fecha_fin'])); ?>
                </p>
            </div>

            <div class="footer">
                <div class="signature">
                    <!-- Espacio para firma o nombre en fuente script -->
                    <div style="font-family: 'Great Vibes', cursive; font-size: 24px; margin-bottom: 5px;">
                        BoloNet
                    </div>
                    <p>Plataforma BoloNet</p>
                    <span>Certificación Académica</span>
                </div>

                <div class="signature">
                    <div style="font-family: 'Great Vibes', cursive; font-size: 24px; margin-bottom: 5px;">
                        <?php echo htmlspecialchars($cursoData['profesor_nombre']); ?>
                    </div>
                    <p><?php echo htmlspecialchars($cursoData['profesor_nombre']); ?></p>
                    <span>Profesor del Curso</span>
                </div>
            </div>

            <div class="date-info">
                Certificado Generado el: <?php echo date('d/m/Y'); ?> | ID: <?php echo uniqid(); ?>
            </div>

        </div>
    </div>

</body>

</html>