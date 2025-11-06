<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'conexion.php';

// Definir criterio de orden: puntaje por defecto
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'puntaje';

// Seguridad: solo permitir ciertas opciones
$orden_permitidos = [
    'puntaje' => 'u.puntaje DESC',
    'racha' => 'u.mejor_racha DESC',
    'partidas' => 'partidas_jugadas DESC'
];
$orderBy = isset($orden_permitidos[$orden]) ? $orden_permitidos[$orden] : $orden_permitidos['puntaje'];

// Consulta SQL (nos aseguramos de incluir u.id)
$sql = "SELECT u.id, u.pfp, u.username, u.puntaje, u.mejor_racha, COUNT(p.id) AS partidas_jugadas
        FROM usuarios AS u
        LEFT JOIN partidas AS p ON u.id = p.id_usuario
        GROUP BY u.id, u.pfp, u.username, u.puntaje, u.mejor_racha
        ORDER BY $orderBy";

$resultado = mysqli_query($conn, $sql);

if (!$resultado) {
    die("Error en la consulta SQL: " . mysqli_error($conn));
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

        <!-- Selector de orden -->
        <form method="get" class="orden-form">
            <label for="orden">Ordenar por:</label>
            <select name="orden" id="orden" onchange="this.form.submit()">
                <option value="puntaje" <?php if($orden==='puntaje') echo 'selected'; ?>>Puntaje</option>
                <option value="racha" <?php if($orden==='racha') echo 'selected'; ?>>Mejor racha</option>
                <option value="partidas" <?php if($orden==='partidas') echo 'selected'; ?>>Partidas jugadas</option>
            </select>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Usuario</th>
                    <th style="text-align: right;">Puntaje</th>
                    <th style="text-align: right;">Partidas</th>
                    <th style="text-align: right;">Mejor racha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $puesto = 1;
                if (mysqli_num_rows($resultado) > 0) {
                    while($fila = mysqli_fetch_assoc($resultado)) {
                        $pfp_ruta = !empty($fila['pfp']) ? $fila['pfp'] : 'img/default.jpg';
                ?>
                        <tr>
                            <td class="ranking-puesto"><?php echo $puesto++; ?></td>
                            <td>
                                <div class="user-info">
                                    <a href="cuenta.php?id=<?php echo $fila['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($pfp_ruta); ?>" alt="Foto de perfil" class="user-pfp">
                                    </a>
                                    <a href="cuenta.php?id=<?php echo $fila['id']; ?>" class="user-username">
                                        <?php echo htmlspecialchars($fila['username']); ?>
                                    </a>
                                </div>
                            </td>
                            <td class="ranking-puntaje"><?php echo number_format($fila['puntaje'], 0, ',', '.'); ?></td>
                            <td class="ranking-partidas"><?php echo $fila['partidas_jugadas']; ?></td>
                            <td class="ranking-racha"><?php echo (int)$fila['mejor_racha']; ?></td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay usuarios para mostrar.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
