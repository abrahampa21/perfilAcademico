<?php
require_once("../src/conexion.php");
session_start();

if (!isset($_SESSION["matricula"])) {
  header("Location: ../index.php");
  exit();
}

$matricula = $_SESSION["matricula"];

//Obtener datos del alumno
$consulta_alumno = $conexion->prepare("
  SELECT id, nombre, apellido_paterno, apellido_materno, carrera
  FROM alumnos
  WHERE matricula = ?
");
$consulta_alumno->bind_param("s", $matricula);
$consulta_alumno->execute();
$resultado_alumno = $consulta_alumno->get_result();
$alumno = $resultado_alumno->fetch_assoc();
$alumno_id = $alumno["id"];

//Obbtener todos los periodos
$consulta_periodos = $conexion->query("SELECT id, nombre FROM periodos ORDER BY id");
$periodos = [];
while ($p = $consulta_periodos->fetch_assoc()) {
  $periodos[] = $p;
}

//Periodo seleccionado (default: primer periodo disponible)
$periodo_seleccionado = isset($_GET["periodo"]) ? (int)$_GET["periodo"] : (count($periodos) > 0 ? $periodos[0]["id"] : 1);

//Nombre del periodo seleccionado
$nombre_periodo = "";
foreach ($periodos as $p) {
  if ($p["id"] == $periodo_seleccionado) {
    $nombre_periodo = $p["nombre"];
    break;
  }
}

// Obtener calificaciones del alumno en el periodo seleccionado
// Se obtienen las materias + calificaciones; si el alumno no tiene calificación, la materia
// no aparece. Solo se muestran materias donde el alumno tiene al menos un registro en ese periodo.
$consulta_califs = $conexion->prepare("
  SELECT
    m.id AS materia_id,
    m.nombre AS materia,
    m.creditos,
    c.calificacion
  FROM calificaciones c
  JOIN materias m ON c.materia_id = m.id
  WHERE c.alumno_id = ? AND c.periodo_id = ?
  ORDER BY m.nombre
");
$consulta_califs->bind_param("ii", $alumno_id, $periodo_seleccionado);
$consulta_califs->execute();
$resultado_califs = $consulta_califs->get_result();

$calificaciones = [];
while ($row = $resultado_califs->fetch_assoc()) {
  $calificaciones[] = $row;
}

// Estadísticas 
// En la BD actual hay un registro por materia/periodo (calificación final).
// Se trata como calificación final; parciales no están en el esquema actual.
// Se calculan: promedio general, aprobadas (>= 6), en riesgo (>= 6 pero < 7),
// reprobadas (< 6), créditos acreditados (materias aprobadas).

$total_materias   = count($calificaciones);
$suma_promedios   = 0;
$aprobadas        = 0;
$creditos_acred   = 0;

foreach ($calificaciones as $c) {
  $cal = (float)$c["calificacion"];
  $suma_promedios += $cal;
  if ($cal >= 6) {
    $aprobadas++;
    $creditos_acred += (int)$c["creditos"];
  }
}

$promedio_general = $total_materias > 0
  ? round($suma_promedios / $total_materias, 1)
  : 0;

// Funciones auxiliares 
function estado_badge(float $cal): string {
  if ($cal >= 7) {
    return '<span class="badge badge-success">Aprobada</span>';
  } elseif ($cal >= 6) {
    return '<span class="badge badge-warning">En Riesgo</span>';
  } else {
    return '<span class="badge badge-danger">No Aprobada</span>';
  }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../img/ites.png" />
    <link rel="stylesheet" href="../assets/css/calificaciones.css" />
    <script
      src="https://kit.fontawesome.com/e522357059.js"
      crossorigin="anonymous"
    ></script>
    <title>Calificaciones</title>
  </head>
  <body>

    <!-- Botón de regreso al portal -->
    <a href="../portal.php" class="regresar-btn">
      <i class="fa-solid fa-arrow-left"></i>
    </a>

    <!-- Main Content -->
    <main class="main-container">

      <!-- Sidebar / Filtros -->
      <aside class="sidebar" id="sidebar">
        <!-- Filtro por periodo -->
        <div class="filters">
          <h3><i class="fa-solid fa-filter"></i> Filtros</h3>
          <div class="filter-group">
            <label for="periodo">Periodo:</label>
            <select id="periodo" onchange="filtrarPeriodo(this.value)">
              <?php foreach ($periodos as $p): ?>
                <option
                  value="<?php echo $p["id"]; ?>"
                  <?php echo ($p["id"] == $periodo_seleccionado) ? "selected" : ""; ?>
                >
                  <?php echo htmlspecialchars($p["nombre"]); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

      </aside>

      <!-- Content Section -->
      <section class="content">

        <!-- Título + barra de búsqueda -->
        <div class="section-header">
          <div>
            <h2>Mis Calificaciones</h2>
            <p class="periodo-label">
              <i class="fa-solid fa-calendar-days"></i>
              <?php echo htmlspecialchars($nombre_periodo); ?>
            </p>
          </div>
          <div class="search-bar">
            <input
              type="text"
              id="busqueda"
              placeholder="Buscar materia..."
              oninput="filtrarTabla(this.value)"
            />
            <button><i class="fas fa-search"></i></button>
          </div>
        </div>

        <!-- Tabla de calificaciones -->
        <div class="grades-container">
          <?php if (empty($calificaciones)): ?>
            <div class="no-datos">
              <i class="fa-solid fa-circle-info"></i>
              <p>No hay calificaciones registradas para este periodo.</p>
            </div>
          <?php else: ?>
          <table class="grades-table" id="tabla-calificaciones">
            <thead>
              <tr>
                <th>Materia</th>
                <th>Calificación</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($calificaciones as $c):
                $cal = (float)$c["calificacion"];
              ?>
              <tr class="fila-materia">
                <td class="materia-nombre"><?php echo htmlspecialchars($c["materia"]); ?></td>
                <td class="gpa-cell"><?php echo number_format($cal, 1); ?></td>
                <td><?php echo estado_badge($cal); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>

        <!-- Estadísticas -->
        <div class="statistics">
          <div class="stat-card">
            <h4>Asignaturas Aprobadas</h4>
            <p class="stat-number <?php echo ($aprobadas < $total_materias) ? 'warning' : ''; ?>">
              <?php echo $aprobadas; ?>
            </p>
            <span class="stat-label">de <?php echo $total_materias; ?></span>
          </div>
          <div class="stat-card">
            <h4>Promedio General</h4>
            <p class="stat-number <?php echo ($promedio_general < 6) ? 'warning' : ''; ?>">
              <?php echo $promedio_general; ?>
            </p>
            <span class="stat-label">GPA</span>
          </div>
          <div class="stat-card">
            <h4>Créditos Acreditados</h4>
            <p class="stat-number"><?php echo $creditos_acred; ?></p>
            <span class="stat-label">Créditos</span>
          </div>
        </div>

        <!-- Leyenda de estados -->
        <div class="leyenda">
          <span class="badge badge-success">Aprobada</span>
          <small>Calificación ≥ 7</small>
          <span class="badge badge-warning" style="margin-left:1rem;">En Riesgo</span>
          <small>Calificación entre 6 y 6.9</small>
          <span class="badge badge-danger" style="margin-left:1rem;">No Aprobada</span>
          <small>Calificación &lt; 6</small>
        </div>

      </section>
    </main>


    <script>
      // Redirige al periodo seleccionado
      function filtrarPeriodo(periodoId) {
        window.location.href = "calificaciones.php?periodo=" + periodoId;
      }

      // Filtra filas de la tabla según texto buscado
      function filtrarTabla(texto) {
        const filas = document.querySelectorAll(".fila-materia");
        const filtro = texto.toLowerCase().trim();
        filas.forEach(function (fila) {
          const nombre = fila.querySelector(".materia-nombre").textContent.toLowerCase();
          fila.style.display = nombre.includes(filtro) ? "" : "none";
        });
      }
    </script>
  </body>
</html>