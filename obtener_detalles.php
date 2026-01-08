<?php
$conexion = new mysqli("localhost", "Clinica_user", "clinica99", "CLINICA");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$mes = $_GET['mes'];
$sql = "
    SELECT estado, curp
    FROM citas
    WHERE DATE_FORMAT(fecha, '%Y-%m') = ?
";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $mes);
$stmt->execute();
$result = $stmt->get_result();
$detalles = [];

while ($row = $result->fetch_assoc()) {
    $detalles[] = $row;
}
header('Content-Type: application/json');
echo json_encode($detalles);
