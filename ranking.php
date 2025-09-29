<?php
// Incluye el archivo de conexión a la base de datos
require 'conexion.php';

// Consulta SQL para obtener los datos del ranking
$sql = "SELECT u.pfp, u.username, u.puntaje, COUNT(p.id) AS partidas_jugadas
        FROM usuarios AS u
        LEFT JOIN partidas AS p ON u.id = p.id_usuario
        GROUP BY u.id
        ORDER BY u.puntaje DESC";

$resultado = mysqli_query($conn, $sql);

// Verificar si la consulta fue exitosa
if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking de Usuarios</title>
    <link rel="stylesheet" href="css/estilo_ranking.css">
</head>
<body>

    <div class="container">
            <a href="index.php" class="btn-volver">⬅ Volver</a>

        <h1>Ranking</h1>

        <table>
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Usuario</th>
                    <th style="text-align: right;">Puntaje</th>
                    <th style="text-align: right;">Partidas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $puesto = 1;
                if (mysqli_num_rows($resultado) > 0) {
                    while($fila = mysqli_fetch_assoc($resultado)) {
                        // Determina la ruta de la imagen de perfil
                        $pfp_ruta = $fila['pfp'] ? $fila['pfp'] : 'default.png';
                ?>
                        <tr>
                            <td class="ranking-puesto"><?php echo $puesto++; ?></td>
                            <td>
                                <div class="user-info">
                                    <img src="<?php echo htmlspecialchars($pfp_ruta); ?>" alt="Foto de perfil" class="user-pfp">
                                    <span class="user-username"><?php echo htmlspecialchars($fila['username']); ?></span>
                                </div>
                            </td>
                            <td class="ranking-puntaje"><?php echo number_format($fila['puntaje'], 0, ',', '.'); ?></td>
                            <td class="ranking-partidas"><?php echo $fila['partidas_jugadas']; ?></td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='4'>No hay usuarios para mostrar.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
// Cierra la conexión a la base de datos
mysqli_close($conn);
?>