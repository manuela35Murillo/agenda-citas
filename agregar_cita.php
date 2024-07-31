<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $fecha_cita = $_POST['fecha_cita'];
    $hora_cita = $_POST['hora_cita'];

    // Verificar si ya existe una cita para el usuario en la fecha y hora especificadas
    $consulta_existencia = $conexion->prepare("SELECT COUNT(*) FROM citas WHERE usuario = ? AND fecha_cita = ? AND hora_cita = ?");
    $consulta_existencia->bind_param("iss", $usuario_id, $fecha_cita, $hora_cita);
    $consulta_existencia->execute();
    $consulta_existencia->bind_result($cantidad_citas);
    $consulta_existencia->fetch();
    $consulta_existencia->close();

    if ($cantidad_citas > 0) {
        // Si ya existe una cita, mostrar alerta
        echo '<script>alert("Ya existe una cita para este día y esa hora.");</script>';
    } else {
        // Si no existe una cita, agregarla a la base de datos
        $consulta_agregar = $conexion->prepare("INSERT INTO citas (usuario, fecha_cita, hora_cita) VALUES (?, ?, ?)");
        $consulta_agregar->bind_param("iss", $usuario_id, $fecha_cita, $hora_cita);
        $consulta_agregar->execute();
        $consulta_agregar->close();
        header("Location: ver_citas.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 50%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border: 2px solid #6A5ACD; /* Borde morado oscuro */
            border-radius: 20px; /* Bordes redondos */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #6A5ACD; /* Morado oscuro */
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            text-align: left;
        }

        input[type="date"],
        input[type="time"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 2px solid #6A5ACD; /* Morado oscuro */
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 16px;
            font-family: Arial, sans-serif;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #6A5ACD; /* Morado oscuro */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #483D8B; /* Morado más oscuro */
        }

        a {
            display: inline-block;
            margin-top: 20px;
            color: #6A5ACD; /* Morado oscuro */
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #483D8B; /* Morado más oscuro */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Agendar Cita</h1>
        <img src="imagenes/agendarcita.png" alt="Imagen de agendar cita">
        <form action="agregar_cita.php" method="post">
            <label for="fecha_cita">Fecha de la cita:</label>
            <input type="date" name="fecha_cita" required>
            <label for="hora_cita">Hora de la cita:</label>
            <input type="time" name="hora_cita" required>
            <button type="submit">Agendar Cita</button>
        </form>
        <a href="inicio.php">Volver</a>
    </div>
</body>
</html>
