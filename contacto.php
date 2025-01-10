<section id="reservas">
    <h2>Reservas</h2>
    <?php
    session_start();

    // Configuración de la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "metalviajes";

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Eliminar o editar registros
    if (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM reservas WHERE id = $id");
        echo "<p>Reserva eliminada exitosamente.</p>";
    } elseif (isset($_POST['edit'])) {
        $id = intval($_POST['id']);
        $nombre = trim($_POST['nombre']);
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $destino = $_POST['destino'];
        $fecha = $_POST['fecha'];

        if ($email) {
            $stmt = $conn->prepare("UPDATE reservas SET nombre = ?, email = ?, destino = ?, fecha = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nombre, $email, $destino, $fecha, $id);
            $stmt->execute();
            $stmt->close();
            echo "<p>Reserva actualizada exitosamente.</p>";
        } else {
            echo "<p>Email inválido.</p>";
        }
    }

    // Obtener y mostrar las reservas
    $result = $conn->query("SELECT * FROM reservas");
    if ($result->num_rows > 0) {
        echo "<div class='tabla-responsiva'>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Destino</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nombre']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['destino']}</td>
                    <td>{$row['fecha']}</td>
                    <td>
                        <form method='POST' style='display:inline-block;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <input type='text' name='nombre' value='{$row['nombre']}' required>
                            <input type='email' name='email' value='{$row['email']}' required>
                            <input type='text' name='destino' value='{$row['destino']}' required>
                            <input type='date' name='fecha' value='{$row['fecha']}' required>
                            <button type='submit' name='edit'>Editar</button>
                        </form>
                        <form method='POST' style='display:inline-block;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='delete'>Eliminar</button>
                        </form>
                    </td>
                  </tr>";
        }
        echo "</tbody>
              </table>
              </div>";
    } else {
        echo "<p>No hay reservas registradas.</p>";
    }

    $conn->close();
    ?>
</section>