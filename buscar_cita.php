<!-- buscar_cita.php -->
<!DOCTYPE html>
<html lang="es">
<head>
   <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="icon" href="logo.png">
    
  <meta charset="UTF-8">
  <title>Buscar Cita</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8fafc;
      padding: 40px;
      text-align: center;
    }
    .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 50px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,0,0,0.15);
    }
    h2 {
      font-size: 32px;
      margin-bottom: 30px;
      color: #1d3557;
    }
    input {
      width: 100%;
      padding: 15px;
      margin: 20px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 18px;
    }
    button {
      padding: 14px 30px;
      background-color: #2d6a4f;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin: 20px 10px 0 10px;
      font-size: 18px;
    }
    .btn-exit {
      background-color: #6c757d;
    }
    a {
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>🔍 Buscar tu cita</h2>
    <form action="mostrar_cita.php" method="get">
      <input type="text" name="busqueda" placeholder="Ingresa tu CURP o nombre completo" required>
      <button type="submit">Buscar</button>
    </form>
    <a href="guardar_cita.html"><button class="btn-exit">⏪ Regresar</button></a>
  </div>
</body>
</html>
