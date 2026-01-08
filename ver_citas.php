<?php
// Datos de conexión
$servidor = "localhost";
$usuario = "Clinica_user";
$password = "clinica99";
$base_de_datos = "CLINICA";

$conexion = new mysqli($servidor, $usuario, $password, $base_de_datos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if (isset($_POST['eliminar'])) {
    $id = $_POST['ID'];
    $sql_delete = "DELETE FROM citas WHERE id = ?";
    $stmt = $conexion->prepare($sql_delete);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Cita eliminada correctamente'); window.location.href = 'ver_citas.php';</script>";
}

if (isset($_POST['ID']) && isset($_POST['estado'])) {
    $id = $_POST['ID'];
    $nuevo_estado = $_POST['estado'];
    
    $sql_update = "UPDATE citas SET estado = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql_update);
    $stmt->bind_param("si", $nuevo_estado, $id);
    $stmt->execute();
    $stmt->close();
    echo "<script>
        var modal = document.createElement('div');
        modal.style.position = 'fixed';
        modal.style.top = '50%';
        modal.style.left = '50%';
        modal.style.transform = 'translate(-50%, -50%)';
        modal.style.padding = '20px';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
        modal.style.color = 'white';
        modal.style.borderRadius = '10px';
        modal.style.zIndex = '1000';
        modal.innerHTML = 'Estado actualizado correctamente';
        document.body.appendChild(modal);
        setTimeout(function() { modal.style.display = 'none'; window.location.href = 'ver_citas.php'; }, 2000);
    </script>";
}

$busqueda = "";
if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $busqueda = $_GET['buscar'];
    $sql_pendientes = "SELECT * FROM citas WHERE (curp LIKE ? OR nombre_completo LIKE ?) AND estado = 'pendiente' ORDER BY fecha DESC";
    $sql_completas = "SELECT * FROM citas WHERE (curp LIKE ? OR nombre_completo LIKE ?) AND estado = 'completo' ORDER BY fecha DESC";
    $sql_canceladas = "SELECT * FROM citas WHERE (curp LIKE ? OR nombre_completo LIKE ?) AND estado = 'cancelado' ORDER BY fecha DESC";
    
    $param = "%$busqueda%";
    $stmt_pendientes = $conexion->prepare($sql_pendientes);
    $stmt_pendientes->bind_param("ss", $param, $param);
    $stmt_pendientes->execute();
    $result_pendientes = $stmt_pendientes->get_result();
    $stmt_completas = $conexion->prepare($sql_completas);
    $stmt_completas->bind_param("ss", $param, $param);
    $stmt_completas->execute();
    $result_completas = $stmt_completas->get_result();
    $stmt_canceladas = $conexion->prepare($sql_canceladas);
    $stmt_canceladas->bind_param("ss", $param, $param);
    $stmt_canceladas->execute();
    $result_canceladas = $stmt_canceladas->get_result();
} else {
    
    $sql_pendientes = "SELECT * FROM citas WHERE estado = 'pendiente' ORDER BY fecha DESC";
    $sql_completas = "SELECT * FROM citas WHERE estado = 'completo' ORDER BY fecha DESC";
    $sql_canceladas = "SELECT * FROM citas WHERE estado = 'cancelado' ORDER BY fecha DESC";
    
    $result_pendientes = $conexion->query($sql_pendientes);
    $result_completas = $conexion->query($sql_completas);
    $result_canceladas = $conexion->query($sql_canceladas);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
     <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="icon" href="logo.png">
    
    <meta charset="UTF-8">
    <title>Ver Citas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1, h2 {
            text-align: center;
            color: #00796b;
        }
        .container {
            width: 100%;
            margin: auto;
        }
        .buscador {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        input, button {
            padding: 10px;
            font-size: 14px;
        }
        button {
            background-color: #00796b;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #005f56;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin: 20px 0;
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
            background-color: #f2f2f2;
        }
        .btn-actualizar {
            background-color: #4CAF50;
        }
        .btn-actualizar:hover {
            background-color: #45a049;
        }
        .btn-cerrar-sesion {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
            text-align: center;
        }
        .btn-cerrar-sesion:hover {
            background-color: #c2185b;
        }
        .btn-eliminar {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-eliminar:hover {
            background-color: #d32f2f;
        }
        .btn-estadisticas {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            transition: 0.3s;
            display: block;
            margin: 30px auto 0 auto;
        }
        .btn-estadisticas:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📋 Listado de Citas</h1>
    <!-- Buscador y Botón Actualizar -->
    <div class="buscador">
        <form method="GET" action="ver_citas.php">
            <input type="text" name="buscar" placeholder="Buscar por CURP o Nombre" value="<?= htmlspecialchars($busqueda) ?>">
            <button type="submit">Buscar Paciente</button>
        </form>
        <form method="GET" action="ver_citas.php">
            <button type="submit" class="btn-actualizar">Actualizar Citas</button>
        </form>
    </div>

    <!-- Tabla de citas pendientes -->
    <h2>📌 Citas Pendientes</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>CURP</th>
                <th>Nombre Completo</th>
                <th>Número de Expediente</th> <!-- ✅ Campo nuevo -->
                <th>Fecha</th>
                <th>Hora</th>
                <th>Lugar Procedencia</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = $result_pendientes->fetch_assoc()) { ?>
            <tr>
                <td><?= $fila['ID'] ?></td>
                <td><?= $fila['curp'] ?></td>
                <td><?= $fila['nombre_completo'] ?></td>
                <td><?= $fila['numero_expediente'] ?></td> <!-- ✅ Nuevo -->
                <td><?= $fila['fecha'] ?></td>
                <td><?= $fila['hora'] ?></td>
                <td><?= $fila['lugar_procedencia'] ?></td>
                <td>
                    <form method="POST" action="ver_citas.php" style="display: inline;">
                        <input type="hidden" name="ID" value="<?= $fila['ID'] ?>">
                        <select name="estado">
                            <option value="pendiente" <?= $fila['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="completo" <?= $fila['estado'] == 'completo' ? 'selected' : '' ?>>Completo</option>
                            <option value="cancelado" <?= $fila['estado'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                        <button type="submit">Actualizar</button>
                    </form>
                    <form method="POST" action="ver_citas.php" style="display: inline;">
                        <input type="hidden" name="ID" value="<?= $fila['ID'] ?>">
                        <button type="submit" class="btn-eliminar" name="eliminar">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Tabla de citas completas -->
    <h2>✅ Citas Completas</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>CURP</th>
                <th>Nombre Completo</th>
                <th>Número de Expediente</th> <!-- ✅ Campo nuevo -->
                <th>Fecha</th>
                <th>Hora</th>
                <th>Lugar Procedencia</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = $result_completas->fetch_assoc()) { ?>
            <tr>
                <td><?= $fila['ID'] ?></td>
                <td><?= $fila['curp'] ?></td>
                <td><?= $fila['nombre_completo'] ?></td>
                <td><?= $fila['numero_expediente'] ?></td> <!-- ✅ Nuevo -->
                <td><?= $fila['fecha'] ?></td>
                <td><?= $fila['hora'] ?></td>
                <td><?= $fila['lugar_procedencia'] ?></td>
                <td>
                    <form method="POST" action="ver_citas.php" style="display: inline;">
                        <input type="hidden" name="ID" value="<?= $fila['ID'] ?>">
                        <select name="estado">
                            <option value="pendiente" <?= $fila['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="completo" <?= $fila['estado'] == 'completo' ? 'selected' : '' ?>>Completo</option>
                            <option value="cancelado" <?= $fila['estado'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                        <button type="submit">Actualizar</button>
                    </form>
                    <form method="POST" action="ver_citas.php" style="display: inline;">
                        <input type="hidden" name="ID" value="<?= $fila['ID'] ?>">
                        <button type="submit" class="btn-eliminar" name="eliminar">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Tabla de citas canceladas -->
    <h2>❌ Citas Canceladas</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>CURP</th>
                <th>Nombre Completo</th>
                <th>Número de Expediente</th> <!-- ✅ Campo nuevo -->
                <th>Fecha</th>
                <th>Hora</th>
                <th>Lugar Procedencia</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila = $result_canceladas->fetch_assoc()) { ?>
            <tr>
                <td><?= $fila['ID'] ?></td>
                <td><?= $fila['curp'] ?></td>
                <td><?= $fila['nombre_completo'] ?></td>
                <td><?= $fila['numero_expediente'] ?></td> <!-- ✅ Nuevo -->
                <td><?= $fila['fecha'] ?></td>
                <td><?= $fila['hora'] ?></td>
                <td><?= $fila['lugar_procedencia'] ?></td>
                <td>
                    <form method="POST" action="ver_citas.php" style="display: inline;">
                        <input type="hidden" name="ID" value="<?= $fila['ID'] ?>">
                        <select name="estado">
                            <option value="pendiente" <?= $fila['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="completo" <?= $fila['estado'] == 'completo' ? 'selected' : '' ?>>Completo</option>
                            <option value="cancelado" <?= $fila['estado'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                        <button type="submit">Actualizar</button>
                    </form>
                    <form method="POST" action="ver_citas.php" style="display: inline;">
                        <input type="hidden" name="ID" value="<?= $fila['ID'] ?>">
                        <button type="submit" class="btn-eliminar" name="eliminar">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <form method="GET" action="estadisticas.php">
        <button type="submit" class="btn-estadisticas">Ver Estadísticas</button>
    </form>

    <form method="GET" action="login.html">
        <button type="submit" class="btn-cerrar-sesion">Cerrar sesión</button>
    </form>
</div>
</body>
</html>
