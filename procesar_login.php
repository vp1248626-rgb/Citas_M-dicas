<?php
session_start();

$usuario_fijo = "TOTOMOXTLE";
$contraseña_fija = "TOTOMOXTLE2025";

$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];
// Verificar credenciales
if ($usuario === $usuario_fijo && $contraseña === $contraseña_fija) {
    $_SESSION['usuario'] = $usuario;  // Crear la sesión
    header("Location: ver_citas.php");  // Redirige a ver citas
    exit();
} else {
    
    $mensaje_error = urlencode('❌ Usuario o contraseña incorrectos.');
    header("Location: login.html?error=$mensaje_error");
    exit();
}
?>
