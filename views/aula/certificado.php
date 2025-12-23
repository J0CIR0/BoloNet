<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Finalización | BoloNet</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --gold-gradient: linear-gradient(135deg, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            --dark-blue: #1a237e;
        }

        body {
            font-family: 'Playfair Display', serif;
            background-color: #2c3034;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .certificado-container {
            width: 1100px;
            /* Landscape layout standard */
            height: 750px;
            background-color: #fffdf5;
            /* Cream paper color */
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            padding: 15px;
            box-sizing: border-box;
            background-image: url('https://www.transparenttextures.com/patterns/cream-paper.png');
        }

        /* Gold Foil Border Container */
        .gold-border {
            width: 100%;
            height: 100%;
            background: var(--gold-gradient);
            padding: 10px;
            box-sizing: border-box;
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .inner-content {
            width: 100%;
            height: 100%;
            background: #fffdf5;
            background-image: radial-gradient(#d4af37 0.5px, transparent 0.5px);
            background-size: 20px 20px;
            background-color: #fffdf5;
            /* Fallback/Base */
            border: 2px solid #b38728;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 40px;
        }

        /* Decorative Corners */
        .corner {
            position: absolute;
            width: 60px;
            height: 60px;
            border: 4px solid #b38728;
            z-index: 10;
        }

        .top-left {
            top: 20px;
            left: 20px;
            border-bottom: none;
            border-right: none;
        }

        .top-right {
            top: 20px;
            right: 20px;
            border-bottom: none;
            border-left: none;
        }

        .bottom-left {
            bottom: 20px;
            left: 20px;
            border-top: none;
            border-right: none;
        }

        .bottom-right {
            bottom: 20px;
            right: 20px;
            border-top: none;
            border-left: none;
        }

        /* Typography */
        .institution-name {
            font-family: 'Cinzel', serif;
            font-size: 28px;
            letter-spacing: 5px;
            color: #333;
            margin-bottom: 30px;
            text-transform: uppercase;
            border-bottom: 2px solid #b38728;
            padding-bottom: 10px;
        }

        .certificate-title {
            font-family: 'Cinzel', serif;
            font-size: 56px;
            font-weight: 700;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .presented-to {
            font-size: 20px;
            font-style: italic;
            color: #666;
            margin: 20px 0 10px;
        }

        .student-name {
            font-family: 'Great Vibes', cursive;
            font-size: 82px;
            color: #1a237e;
            margin: 10px 0 20px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            line-height: 1.2;
        }

        .divider {
            width: 60%;
            height: 2px;
            background: var(--gold-gradient);
            margin: 10px auto 30px;
        }

        .description {
            font-size: 20px;
            color: #444;
            max-width: 80%;
            text-align: center;
            line-height: 1.6;
        }

        .course-name {
            font-weight: bold;
            font-size: 28px;
            color: #333;
            display: block;
            margin-top: 10px;
            font-family: 'Playfair Display', serif;
        }

        .grade {
            font-size: 18px;
            color: #666;
            margin-top: 15px;
            display: inline-block;
            background: rgba(179, 135, 40, 0.1);
            padding: 5px 15px;
            border-radius: 20px;
            border: 1px solid rgba(179, 135, 40, 0.3);
        }

        /* Signatures Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            width: 80%;
            margin-top: 0px;
            align-items: flex-end;
        }

        .signature-block {
            text-align: center;
            width: 250px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 10px;
            height: 50px;
        }

        .signature-img {
            max-height: 60px;
            /* Placeholder styling for logic */
            font-family: 'Great Vibes', cursive;
            font-size: 32px;
            color: #333;
        }

        .signature-title {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
            font-weight: bold;
        }

        /* Seal */
        .seal-container {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 120px;
        }

        .seal {
            width: 100%;
            height: 100%;
            background: var(--gold-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .seal::before {
            content: '';
            position: absolute;
            width: 90%;
            height: 90%;
            border: 2px dashed #b38728;
            border-radius: 50%;
        }

        .seal i {
            font-size: 50px;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .ribbon {
            position: absolute;
            bottom: -20px;
            width: 40px;
            height: 60px;
            background: #b38728;
            z-index: -1;
        }

        .ribbon-left {
            left: 20px;
            transform: rotate(15deg);
        }

        .ribbon-right {
            right: 20px;
            transform: rotate(-15deg);
        }


        .logo-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.03;
            font-size: 400px;
            color: #000;
            z-index: 0;
            pointer-events: none;
        }

        /* Controls */
        .controls {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: sans-serif;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-print {
            background: #1a237e;
            color: white;
        }

        .btn-back {
            background: #546e7a;
            color: white;
        }

        @media print {
            body {
                background: none;
                padding: 0;
                display: block;
            }

            .no-print,
            .controls {
                display: none;
            }

            .certificado-container {
                box-shadow: none;
                margin: 0 auto;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="controls no-print">
        <button onclick="window.print()" class="btn btn-print">
            <i class="fas fa-print"></i> Imprimir / Guardar
        </button>
        <button onclick="window.history.back()" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Volver
        </button>
    </div>

    <div class="certificado-container">
        <div class="gold-border">
            <div class="inner-content">
                <!-- Watermark -->
                <i class="fas fa-graduation-cap logo-watermark"></i>

                <!-- Corners -->
                <div class="corner top-left"></div>
                <div class="corner top-right"></div>
                <div class="corner bottom-left"></div>
                <div class="corner bottom-right"></div>

                <div class="institution-name">BoloNet Academy</div>

                <h1 class="certificate-title">Certificado de Finalización</h1>

                <p class="presented-to">Se otorga el presente reconocimiento a</p>

                <!-- FIXED: Array access instead of object access -->
                <div class="student-name">
                    <?php echo htmlspecialchars($inscripcion['nombre'] . ' ' . $inscripcion['apellido']); ?>
                </div>

                <div class="divider"></div>

                <div class="description">
                    Por haber completado y aprobado satisfactoriamente el curso academico de:
                    <span class="course-name"><?php echo htmlspecialchars($curso['nombre']); ?></span>

                    <div class="grade">
                        Nota Final: <strong><?php echo number_format($inscripcion['nota_final'], 2); ?> / 100</strong>
                    </div>
                </div>

                <div class="seal-container">
                    <div class="ribbon ribbon-left"></div>
                    <div class="ribbon ribbon-right"></div>
                    <div class="seal">
                        <i class="fas fa-award"></i>
                    </div>
                </div>

                <div class="footer">
                    <div class="signature-block">
                        <div class="signature-line">
                            <div class="signature-img">BoloNet</div>
                        </div>
                        <div class="signature-title">Director Académico</div>
                    </div>

                    <div class="signature-block">
                        <div class="signature-line">
                            <div class="signature-img">
                                <?php echo date('d/m/Y', strtotime($inscripcion['fecha_inscripcion'])); ?></div>
                        </div>
                        <div class="signature-title">Fecha de Emisión</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>