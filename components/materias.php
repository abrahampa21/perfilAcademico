<?php
session_start();
require_once("../src/conexion.php");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['matricula'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener datos del alumno logueado
$matricula = $_SESSION['matricula'];
$sql_alumno = "SELECT id, nombre, apellido_paterno FROM alumnos WHERE matricula = ?";
$consulta_alumno = $conexion->prepare($sql_alumno);
$consulta_alumno->bind_param("s", $matricula);
$consulta_alumno->execute();
$resultado_alumno = $consulta_alumno->get_result();

if (!$resultado_alumno || $resultado_alumno->num_rows === 0) {
    die("Error: Alumno no encontrado");
}

$alumno = $resultado_alumno->fetch_assoc();
$alumno_id = $alumno['id'];

// Obtener materias del alumno usando JOIN con calificaciones
$sql_materias = "SELECT DISTINCT m.id, m.nombre, m.creditos, p.nombre as periodo 
                 FROM materias m
                 INNER JOIN calificaciones c ON m.id = c.materia_id
                 INNER JOIN periodos p ON c.periodo_id = p.id
                 WHERE c.alumno_id = ?
                 ORDER BY p.id DESC, m.nombre ASC";

$consulta_materias = $conexion->prepare($sql_materias);
if (!$consulta_materias) {
    die("Error al preparar consulta: " . $conexion->error);
}

$consulta_materias->bind_param("i", $alumno_id);
$consulta_materias->execute();
$resultado_materias = $consulta_materias->get_result();

if (!$resultado_materias) {
    die("Error en la consulta: " . $conexion->error);
}

?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../img/ites.png" />
    <link rel="stylesheet" href="../assets/css/materias.css" />
    <script src="https://kit.fontawesome.com/e522357059.js" crossorigin="anonymous"></script>
    <title>Materias</title>
  </head>
  <body>

    <a href="../portal.php" class="regresar-btn"><i class="fa-solid fa-arrow-left"></i></a>

    <div class="contenedor">
      <div class="tarjeta-perfil">

        <div class="encabezado-perfil">
          <div class="avatar">
            <i class="fa-solid fa-user"></i>
          </div>
          <div>
            <div class="nombre-alumno"><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido_paterno']); ?></div>
            <div class="matricula-alumno"><?php echo htmlspecialchars($matricula); ?></div>
          </div>
        </div>

        <div class="cuerpo-perfil">
          <p class="titulo-seccion">Materias inscritas</p>
          <div class="tabla-wrapper">
            <table class="tabla-materias">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Créditos</th>
                  <th>Periodo</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($resultado_materias && $resultado_materias->num_rows > 0) {
                    while ($materia = $resultado_materias->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($materia['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($materia['creditos']) . "</td>";
                        echo "<td>" . htmlspecialchars($materia['periodo']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align: center; padding: 20px;'>No hay materias inscritas</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

  </body>
</html>
