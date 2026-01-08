<?php
require 'conexion.php';

if (!isset($_GET['id_cita'])) {
    echo "ID de cita no proporcionado.";
    exit();
}

$id_cita = intval($_GET['id_cita']);

$sql = "SELECT * FROM citas WHERE id = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo "Error al preparar la consulta.";
    exit();
}

$stmt->bind_param("i", $id_cita);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "No se encontró la cita.";
    exit();
}

$cita = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Confirmación de Cita</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f1f5f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .contenedor {
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 400px;
      text-align: center;
    }
    h2 {
      color: #2d6a4f;
      margin-bottom: 20px;
    }
    p {
      margin: 10px 0;
      font-size: 16px;
    }
    .btn {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #2d6a4f;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn:hover {
      background-color: #1b4332;
    }
  </style>
</head>
<body>
  <div class="contenedor">
    <h2>¡Cita guardada correctamente!</h2>
    <p><strong>ID de Cita:</strong> <?= $cita['id'] ?></p>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($cita['nombre_completo']) ?></p>
    <p><strong>CURP:</strong> <?= htmlspecialchars($cita['curp']) ?></p>
    <p><strong>Fecha:</strong> <?= $cita['fecha'] ?></p>
    <p><strong>Horario:</strong> <?= $cita['hora'] ?></p>
    <p><strong>Lugar de Procedencia:</strong> <?= htmlspecialchars($cita['lugar_procedencia']) ?></p>

    <a href="guardar_cita.html"><button class="btn">Registrar otra cita</button></a>
  </div>
</body>
</html>
