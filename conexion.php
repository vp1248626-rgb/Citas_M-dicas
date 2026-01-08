<?php
// Datos de conexión
$servidor = "localhost";
$usuario = "Clinica_user";          // Usuario correcto         
$base_de_datos = "CLINICA";      // Base de datos 
$password = "clinica99";

$conexion = new mysqli($servidor, $usuario, $password, $base_de_datos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
