<?php
require_once("src/conexion.php");
session_start();

if (!isset($_SESSION["matricula"])) {
  header("Location: index.php");
  exit();
}

$matricula = $_SESSION["matricula"];

$consulta = $conexion->prepare("SELECT nombre, apellido_paterno FROM alumnos WHERE matricula=?");
$consulta->bind_param("s",$matricula);
$consulta->execute();
$resultado = $consulta->get_result();
$alumno = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="img/ites.png" />
    <link rel="stylesheet" href="assets/css/portal.css">
    <title>Portal del alumno</title>
  </head>
  <body>
    <!--Barra de navegación-->
    <header>
      <nav>
        <div class="titulo">
          <img src="img/ites.png" class="logo" alt="Logo" />
          <h1>Portal del Alumno</h1>
        </div>
        <div class="toggle" id="toggle">
          <i class="fa-solid fa-bars" id="bars"></i>
        </div>
        <ul class="links">
          <li><a href="#about-us">Sobre Nosotros</a></li>
          <li><a href="#focus">Enfoque</a></li>
          <li><a href="#contacto">Contacto</a></li>
        </ul>
        <i class="fa-solid fa-bell bell-icon"></i>
        <!--Menú responsive-->
        <div class="menu" id="menu">
          <i class="fa-solid fa-address-card"
            ><a href="components/datosGenerales.php?matricula=<?php echo $matricula; ?>">Datos Personales</a></i
          >
          <i class="fa-solid fa-calendar-days"
            ><a href="components/materias.php">Materias cursadas</a></i
          >
          <i class="fa-solid fa-pen"
            ><a href="components/calificaciones.php">Calificaciones</a></i
          >
        </div>
      </nav>
    </header>

    <!-- Menú lateral -->
    <aside class="menu-lateral" id="menu-lateral">
      <div class="menu-div">
        <i class="fa-solid fa-house" title="Panel"></i>
        <h4 class="titulo-menu">Menú</h4>
      </div>
      <div class="nombre-alumno">
        <i class="fa-solid fa-user-graduate" title="Ícono del alumno"></i>
        <h4><?php echo htmlspecialchars($alumno["nombre"] . " " . $alumno["apellido_paterno"]); ?></h4>
      </div>
      <div class="menu-opciones">
        <a href="components/datosGenerales.php">
          <div class="opcion">
            <i class="fa-solid fa-address-card"></i>
            <h4>Datos Personales</h4>
          </div>
        </a>
        <a href="components/materias.php">
          <div class="opcion">
            <i class="fa-solid fa-calendar-days"></i>
            <h4>Materias cursadas</h4>
          </div>
        </a>
        <a href="components/calificaciones.php">
          <div class="opcion">
            <i class="fa-solid fa-pen"></i>
            <h4>Calificaciones</h4>
          </div>
        </a>
      </div>
      <div class="salir">
        <a href="src/logout.php" title="Salir" name="salir">
          <i class="fa-solid fa-right-from-bracket"></i>
        </a>
        <h4>Salir</h4>
      </div>
    </aside>

    <!--Contenido principal-->
    <main>
      <section class="introduccion">
        <div class="intro-contenido">
          <h2>BIENVENIDO AL PORTAL DEL ALUMNO</h2>
          <p>
            Este portal académico permite a los estudiantes consultar su
            información escolar de forma clara y organizada. Aquí podrás
            visualizar tus datos generales, las materias cursadas, así como el
            total de créditos aprobados. Además, es posible filtrar por periodo
            para revisar tus boletas de calificaciones y acceder a tu historial académico directamente desde la base de datos, todo en un solo lugar.
          </p>
        </div>
      </section>
    </main>
    <script src="assets/js/portal.js"></script>
  </body>
</html>
