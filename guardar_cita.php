<?php
// Datos de la conexión
$servidor = "localhost";           
$usuario = "Clinica_user";         
$password = "clinica99";           
$base_de_datos = "CLINICA";        

$conexion = new mysqli($servidor, $usuario, $password, $base_de_datos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Asegurar que todos los campos requeridos existan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
    isset($_POST['curp'], $_POST['nombre_completo'], $_POST['fecha'], $_POST['hora'], $_POST['lugar_procedencia'], $_POST['numero_expediente'])) {

    $curp = trim($_POST['curp']);
    $nombre_completo = trim($_POST['nombre_completo']);
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $lugar_procedencia = $_POST['lugar_procedencia'];
    $numero_expediente = trim($_POST['numero_expediente']);

    // Validación del CURP
    if (!preg_match("/^[A-Z0-9]{18}$/", $curp)) {
        echo "error=curp_invalido";
        exit();
    }

    // Validación del número de expediente (mínimo 3 caracteres)
    if (strlen($numero_expediente) < 3) {
        echo "error=expediente_invalido";
        exit();
    }

    // Verificar fecha
    $fecha_actual = date("Y-m-d");
    if ($fecha < $fecha_actual) {
        echo "error=fecha_pasada";
        exit();
    }

    // Verificar si la fecha y hora ya están ocupadas
    $consulta = "SELECT id FROM citas WHERE fecha = ? AND hora = ?";
    if ($stmt = $conexion->prepare($consulta)) {
        $stmt->bind_param("ss", $fecha, $hora);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Si ya existe una cita en ese horario
            echo "error=ocupado";
            $stmt->close();
            exit();
        }
        $stmt->close();
    } else {
        echo "error=consulta_no_exitosa";
        exit();
    }

    // Insertar los datos en la tabla (incluye el nuevo campo numero_expediente)
    $sql = "INSERT INTO citas (curp, nombre_completo, fecha, hora, lugar_procedencia, numero_expediente, estado) 
            VALUES (?, ?, ?, ?, ?, ?, 'pendiente')";
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param("ssssss", $curp, $nombre_completo, $fecha, $hora, $lugar_procedencia, $numero_expediente);
        if ($stmt->execute()) {
            // Devolver éxito
            echo "success";
            exit();
        } else {
            echo "error=guardar";
            exit();
        }
        $stmt->close();
    } else {
        echo "error=preparar_consulta";
        exit();
    }
} else {
    echo "error=faltan_datos";
    exit();
}

// Cerrar la conexión
$conexion->close();
?>
