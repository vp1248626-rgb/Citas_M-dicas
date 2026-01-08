<?php
require 'conexion.php';

$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

$sql = "SELECT * FROM citas 
        WHERE curp = ? OR nombre_completo LIKE ?
        ORDER BY fecha DESC, hora DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);
$likeBusqueda = "%" . $busqueda . "%";
$stmt->bind_param("ss", $busqueda, $likeBusqueda);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<p style='font-family: Arial, sans-serif; font-size: 18px;'>No se encontró ninguna cita con esa información.</p>";
    echo '<a href="buscar_cita.php"><button style="padding: 10px 20px; font-size: 16px;">Intentar otra vez</button></a>';
    exit();
}

$cita = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <link rel="icon" type="image/png" href="images/logo.png">
   <link rel="icon" href="logo.png">
    
  <meta charset="UTF-8">
  <title>Tu Cita</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f1f5f9;
      text-align: center;
      padding: 50px;
    }
    #citaInfo {
      background: white;
      padding: 40px;
      margin: auto;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      max-width: 700px;
    }
    h3 {
      font-size: 28px;
      color: #1d3557;
      margin-bottom: 30px;
    }
    p {
      font-size: 20px;
      margin: 15px 0;
    }
    .btn {
      margin: 20px 10px;
      padding: 14px 30px;
      border: none;
      border-radius: 8px;
      font-size: 18px;
      cursor: pointer;
    }
    .descargar {
      background-color: #1d3557;
      color: white;
    }
    .salir {
      background-color: #6c757d;
      color: white;
    }
    a {
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div id="citaInfo">
    <h3>📅 Detalles de tu cita</h3>
    <p><strong>No. de Cita:</strong> <?= htmlspecialchars($cita['ID']) ?></p>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($cita['nombre_completo']) ?></p>
    <p><strong>CURP:</strong> <?= htmlspecialchars($cita['curp']) ?></p>

    <!-- ✅ Nuevo campo agregado -->
    <p><strong>Número de Expediente:</strong> <?= htmlspecialchars($cita['numero_expediente']) ?></p>

    <p><strong>Fecha:</strong> <?= $cita['fecha'] ?></p>
    <p><strong>Hora:</strong> <?= $cita['hora'] ?></p>
    <p><strong>Lugar de Procedencia:</strong> <?= $cita['lugar_procedencia'] ?></p>
  </div>

  <br>
  <button class="btn descargar" onclick="descargarComoImagen()">📥 Descargar</button>
  <a href="buscar_cita.php"><button class="btn salir">⏪ Regresar</button></a>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script>
    function descargarComoImagen() {
      const cita = document.getElementById("citaInfo");
      html2canvas(cita).then(canvas => {
        const enlace = document.createElement("a");
        enlace.href = canvas.toDataURL("image/png");
        enlace.download = "cita.png";
        enlace.click();
      });
    }
  </script>
</body>
</html>
