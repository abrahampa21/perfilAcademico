<?php
require_once("../src/conexion.php");

// Obtener datos de los alumnos de la tabla alumnos
$sql = "SELECT id, nombre, apellido_paterno, apellido_materno, matricula, correo, carrera, direccion, celular FROM alumnos";
$resultado = $conexion->query($sql);

if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}

// Convertir carrera enum a nombre legible
function getNombreCarrera($carrera) {
    $carreras = array(
        'programacion' => 'Licenciatura en Programación',
        'sistemas-computacionales' => 'Licenciatura en Sistemas Computacionales',
        'derecho' => 'Licenciatura en Derecho',
        'contaduria' => 'Licenciatura en Contaduría',
        'administracion' => 'Licenciatura en Administración',
        'artes-culinarias' => 'Licenciatura en Artes Culinarias'
    );
    return isset($carreras[$carrera]) ? $carreras[$carrera] : $carrera;
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../img/ites.png" />
    <link rel="stylesheet" href="../assets/css/datosGenerales.css" />
    <script
      src="https://kit.fontawesome.com/e522357059.js"
      crossorigin="anonymous"
    ></script>
    <title>Datos Generales</title>
  </head>
  <body>
    <!--Botón de regreso al panel-->
    <a href="../portal.php" title="Regresar" class="regresar-btn">
      <i class="fa-solid fa-arrow-left"></i>
    </a>
    <main class="contenedor">
      <!--Encabezado con avatar y nombre-->
      <div class="tarjeta-perfil">
        <?php
        if ($resultado && $resultado->num_rows > 0) {
            $alumno = $resultado->fetch_assoc();
            $iniciales = strtoupper(substr($alumno['nombre'], 0, 1)) . strtoupper(substr($alumno['apellido_paterno'], 0, 1));
            $carrera_completa = getNombreCarrera($alumno['carrera']);
        ?>
        <div class="encabezado-perfil">
          <div class="avatar"><?php echo htmlspecialchars($iniciales); ?></div>
          <div>
            <p class="nombre-alumno"><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido_paterno']); ?></p>
            <p class="matricula-alumno"><?php echo htmlspecialchars($alumno['matricula']); ?></p>
          </div>
        </div>

        <!--Secciones-->
        <div class="cuerpo-perfil">
          <!--Información Personal-->
          <div class="bloque-seccion">
            <p class="titulo-seccion">Información personal</p>
            <div class="cuadricula-campos">
              <div class="campo">
                <p class="etiqueta-campo">Nombre(s)</p>
                <p class="valor-campo"><?php echo htmlspecialchars($alumno['nombre']); ?></p>
              </div>
              <div class="campo">
                <p class="etiqueta-campo">Matrícula</p>
                <p class="valor-campo"><?php echo htmlspecialchars($alumno['matricula']); ?></p>
              </div>
              <div class="campo">
                <p class="etiqueta-campo">Apellido paterno</p>
                <p class="valor-campo"><?php echo htmlspecialchars($alumno['apellido_paterno']); ?></p>
              </div>
              <div class="campo">
                <p class="etiqueta-campo">Apellido materno</p>
                <p class="valor-campo <?php echo empty($alumno['apellido_materno']) ? 'vacio' : ''; ?>"><?php echo htmlspecialchars($alumno['apellido_materno'] ?? 'No registrado'); ?></p>
              </div>
            </div>
          </div>

          <!--Contacto-->
          <div class="bloque-seccion">
            <p class="titulo-seccion">Contacto</p>
            <div class="cuadricula-campos">
              <div class="campo ancho-completo">
                <p class="etiqueta-campo">Correo electrónico</p>
                <p class="valor-campo"><?php echo htmlspecialchars($alumno['correo']); ?></p>
              </div>
              <div class="campo">
                <p class="etiqueta-campo">Número de celular</p>
                <p class="valor-campo <?php echo empty($alumno['celular']) ? 'vacio' : ''; ?>"><?php echo htmlspecialchars($alumno['celular'] ?? 'No registrado'); ?></p>
              </div>
              <div class="campo">
                <p class="etiqueta-campo">Dirección física</p>
                <p class="valor-campo <?php echo empty($alumno['direccion']) ? 'vacio' : ''; ?>"><?php echo htmlspecialchars($alumno['direccion'] ?? 'No registrado'); ?></p>
              </div>
            </div>
          </div>

          <!--Académico-->
          <div class="bloque-seccion">
            <p class="titulo-seccion">Académico</p>
            <div class="cuadricula-campos academico-div">
              <div class="campo ancho-completo">
                <p class="etiqueta-campo">Licenciatura</p>
                <p class="valor-campo"><?php echo htmlspecialchars($carrera_completa); ?></p>
              </div>
            </div>
          </div>
        </div>
        <?php } else { ?>
        <p>No se encontraron datos de alumnos.</p>
        <?php } ?>
      </div>
    </main>
  </body>
</html>
