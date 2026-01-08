<?php
setlocale(LC_TIME, 'es_ES.UTF-8'); // para sistemas Unix
// Para Windows, usar: setlocale(LC_TIME, 'spanish');

$servidor = "localhost";
$usuario = "Clinica_user";
$password = "clinica99";
$base_de_datos = "CLINICA";
$conexion = new mysqli($servidor, $usuario, $password, $base_de_datos);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$sql_estadisticas_mensuales = "
SELECT 
    DATE_FORMAT(fecha, '%Y-%m') AS mes,
    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
    SUM(CASE WHEN estado = 'completo' THEN 1 ELSE 0 END) AS completas,
    SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) AS canceladas
FROM citas
GROUP BY mes
ORDER BY mes DESC
";
$result_estadisticas = $conexion->query($sql_estadisticas_mensuales);
$meses = [];
$pendientes = [];
$completas = [];
$canceladas = [];

while ($fila = $result_estadisticas->fetch_assoc()) {
    $mes_timestamp = strtotime($fila['mes'] . "-01");
    $mes_formateado = strftime("%B %Y", $mes_timestamp);
    $meses[] = ucfirst($mes_formateado); // Capitaliza la primera letra
    $pendientes[] = $fila['pendientes'];
    $completas[] = $fila['completas'];
    $canceladas[] = $fila['canceladas'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
     <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="icon" href="logo.png">
    
    <meta charset="UTF-8">
    <title>Estadísticas Mensuales de Citas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        h1 {
            color: #00796b;
            text-align: center;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #00796b;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f0f0f0;
        }
        .btn-regresar {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: block;
            width: 250px;
            margin: 30px auto;
            text-align: center;
            border-radius: 10px;
        }
        .btn-regresar:hover {
            background-color: #b71c1c;
        }
        .btn-descargar {
            background-color: #009688;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-descargar:hover {
            background-color: #00796b;
        }
        canvas {
            max-width: 90%;
            margin: 30px auto;
            display: block;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
        }
        .detalle-btn {
            background-color: #00796b;
            color: white;
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .detalle-btn:hover {
            background-color: #004d40;
        }
    </style>
</head>
<body>
    <h1>📊 Estadísticas Mensuales de Citas</h1>

    <!-- Tabla principal -->
    <table id="tablaResumen">
        <thead>
            <tr>
                <th>Mes</th>
                <th>Pendientes</th>
                <th>Completas</th>
                <th>Canceladas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php for ($i = 0; $i < count($meses); $i++) { ?>
            <tr>
                <td><?= $meses[$i] ?></td>
                <td><?= $pendientes[$i] ?></td>
                <td><?= $completas[$i] ?></td>
                <td><?= $canceladas[$i] ?></td>
                <td><button class="detalle-btn" onclick="mostrarDetalles('<?= $meses[$i] ?>')">Detalles</button></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <button onclick="descargarTabla('tablaResumen', 'resumen_citas')" class="btn-descargar">Descargar tabla resumen</button>

    <!-- Segunda tabla para los detalles -->
    <div id="seccionDetalles" style="display:none;">
        <h2 id="tituloDetalles"></h2>
        <table id="tablaDetalles">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>CURP</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button onclick="descargarTabla('tablaDetalles', 'detalles_mes')" class="btn-descargar">Descargar detalles</button>
    </div>

    <!-- Gráfica -->
    <canvas id="histograma" width="800" height="400"></canvas>
    <button onclick="descargarGrafica()" class="btn-descargar">
        <img src="icono_descargar.png" alt="Descargar" style="width:24px; vertical-align:middle;">
        Descargar gráfica
    </button>

    <form action="ver_citas.php">
        <button type="submit" class="btn-regresar">🔙 Regresar a Citas</button>
    </form>

    <script>
        const mesesDB = <?= json_encode(array_map(fn($m) => date("Y-m", strtotime($m)), $meses)) ?>;
        const mesesES = <?= json_encode($meses) ?>;

        function mostrarDetalles(mesEsp) {
            const mesIndex = mesesES.indexOf(mesEsp);
            const mesFormatoDB = mesesDB[mesIndex];

            fetch('obtener_detalles.php?mes=' + mesFormatoDB)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('tituloDetalles').innerText = "Detalles del mes: " + mesEsp;
                    const tbody = document.querySelector("#tablaDetalles tbody");
                    tbody.innerHTML = '';
                    data.forEach(fila => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `<td>${fila.estado}</td><td>${fila.curp}</td>`;
                        tbody.appendChild(tr);
                    });
                    document.getElementById('seccionDetalles').style.display = 'block';
                });
        }

        function descargarTabla(tablaID, nombreArchivo) {
            const tabla = document.getElementById(tablaID);
            const libro = XLSX.utils.table_to_book(tabla, {sheet: "Sheet1"});
            XLSX.writeFile(libro, nombreArchivo + ".xlsx");
        }

        function descargarGrafica() {
            const canvas = document.getElementById("histograma");
            const enlace = document.createElement("a");
            enlace.download = "grafica_citas.png";
            enlace.href = canvas.toDataURL("image/png");
            enlace.click();
        }

        const ctx = document.getElementById('histograma').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($meses) ?>,
                datasets: [
                    {
                        label: 'Pendientes',
                        backgroundColor: '#ff9800',
                        data: <?= json_encode($pendientes) ?>
                    },
                    {
                        label: 'Completas',
                        backgroundColor: '#4caf50',
                        data: <?= json_encode($completas) ?>
                    },
                    {
                        label: 'Canceladas',
                        backgroundColor: '#f44336',
                        data: <?= json_encode($canceladas) ?>
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Histograma de Citas por Estado' }
                }
            }
        });
    </script>
</body>
</html>
