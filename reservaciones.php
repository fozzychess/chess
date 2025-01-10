<?php
session_start();

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";

// Crear conexión
$conn = new mysqli($servername, $username, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS metalviajes";
if (!$conn->query($sql)) {
    die("Error al crear la base de datos: " . $conn->error);
}

// Seleccionar base de datos
$conn->select_db("metalviajes");

// Crear tabla reservas si no existe
$sql = "CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    destino VARCHAR(50) NOT NULL,
    fecha DATE NOT NULL
)";
$conn->query($sql);

// Verificar si se envió el formulario
if (isset($_POST['submit'])) {
    $nombre = trim($_POST['nombre']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $destino = $_POST['destino'];
    $fecha = $_POST['fecha'];

    if (!$email) {
        die("El email ingresado no es válido.");
    }

    // Usar consultas preparadas para mayor seguridad
    $stmt = $conn->prepare("INSERT INTO reservas (nombre, email, destino, fecha) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $destino, $fecha);

    if ($stmt->execute()) {
        // Enviar correo electrónico
        $asunto = "Confirmación de Reserva - MetalViajes";
        $mensaje = "Hola $nombre,\n\nGracias por reservar con MetalViajes.\n\n"
                 . "Detalles de tu reserva:\n"
                 . "Destino: $destino\n"
                 . "Fecha: $fecha\n\n"
                 . "Nos pondremos en contacto contigo para más información.\n\n"
                 . "¡Gracias por confiar en nosotros!";
        $headers = "From: alfredofos417@gmail.com\r\n";

        if (mail($email, $asunto, $mensaje, $headers)) {
            echo "<p>Reserva exitosa. Se envió un correo de confirmación a $email.</p>";
        } else {
            echo "<p>Reserva exitosa, pero no se pudo enviar el correo de confirmación.</p>";
        }
    } else {
        error_log($stmt->error);
        echo "<p>Ocurrió un error al registrar la reserva. Por favor, intenta más tarde.</p>";
    }

    $stmt->close();
} else {
    echo "<p>No se recibieron datos del formulario.</p>";
}

// Cerrar conexión
$conn->close();
?>
