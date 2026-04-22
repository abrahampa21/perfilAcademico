<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("src/conexion.php");
session_start();


function validar_contraseña($contraseña)
{
  // Mínimo 10 caracteres
  if (strlen($contraseña) < 10) {
    return false;
  }

  // Al menos una letra
  if (!preg_match('/[A-Za-z]/', $contraseña)) {
    return false;
  }

  // Al menos un carácter especial
  if (!preg_match('/[!@#$%^&*()_\-=\[\]{};\'":\\|,.<>\/?]/', $contraseña)) {
    return false;
  }

  return true;
}

//Inicio de sesión
if (isset($_POST["ingresar-btn"])) {
  $matricula = $_POST['matricula'];
  $password = $_POST['contrasena'];
  $password_encriptada = sha1($password);

  $consulta_vendedor = $conexion->prepare("SELECT matricula, contrasena FROM alumnos WHERE matricula = ? AND contrasena = ? LIMIT 1");

  if (!$consulta_vendedor) {
    echo "<script>alert('Error en la consulta: " . $conexion->error . "')</script>";
  } else {
    $consulta_vendedor->bind_param("ss", $matricula, $password_encriptada);
    $consulta_vendedor->execute();
    $resultado_vendedor = $consulta_vendedor->get_result();

    if ($resultado_vendedor && $resultado_vendedor->num_rows > 0) {
      $alumno = $resultado_vendedor->fetch_assoc();
      $_SESSION['matricula'] = $alumno['matricula'];
      echo "<script>window.location.href='portal.php?matricula=" . $alumno['matricula'] . "'</script>";
    } else {
      echo "<script>alert('Matrícula o contraseña incorrectos.'); window.location.href='index.php'</script>";
    }
  }
}

// Registro del estudiante
if (isset($_POST["registrar-btn"])) {
  // Recoge datos del formulario...
  $nombre = $_POST['nombre'];
  $apellidoP = $_POST['apellidoP'];
  $apellidoM = $_POST['apellidoM'];
  $carrera = $_POST['carreras'];
  $matricula = $_POST['matricula'];
  $correo = $_POST['email'];
  $direccion = $_POST['direccion'];
  $password = $_POST['contraseña-registro'];
  $numeroTel = $_POST['numero-telefonico'];

  if (!validar_contraseña($password)) {
    echo "<script>alert('¡Registro exitoso del estudiante!'); window.location.href='index.php'</script>";
  } else {
    $password_encriptada = sha1($password);

    $consulta_verificar = $conexion->prepare("SELECT nombre, apellido_paterno, apellido_materno FROM alumnos WHERE nombre = ? AND apellido_paterno = ? AND apellido_materno = ? LIMIT 1");
    $consulta_verificar->bind_param("sss", $nombre, $apellidoP, $apellidoM);
    $consulta_verificar->execute();
    $resultado_verificar = $consulta_verificar->get_result();

    if ($resultado_verificar && $resultado_verificar->num_rows > 0) {
      echo "<script>alert('error')</script>";
    } else {
      $consulta_insert = $conexion->prepare("INSERT INTO alumnos(nombre, apellido_paterno, apellido_materno, matricula, correo, contrasena, carrera, direccion, celular) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

      $consulta_insert->bind_param("sssssssss", $nombre, $apellidoP, $apellidoM, $matricula, $correo, $password_encriptada, $carrera, $direccion, $numeroTel);

      if ($consulta_insert->execute()) {
        echo "<script>alert('¡Registro exitoso del estudiante!'); window.location.href='index.php'</script>";
      } else {
        echo "<script>alert('Error al registrar el alumno'); window.location.href='index.php'</script>";
      }
    }
  }
}


// Recuperar contraseña
// Recuperar contraseña
if (isset($_POST["recuperar-btn"])) {
  $email = $_POST['email-recuperar'];
  $contraseñaNueva = $_POST['contraseña-recuperar'];

  if (!validar_contraseña($contraseñaNueva)) {
    echo "<script>alert('Contraseña inválida. Debe tener mínimo 10 caracteres, una letra y un carácter especial.')</script>";
  } else {
    $contraseñaNuevaEncriptada = sha1($contraseñaNueva);

    $consulta_vendedor = $conexion->prepare("SELECT * FROM alumnos WHERE correo = ? LIMIT 1");

    if (!$consulta_vendedor) {
      echo "<script>alert('Error en la consulta: " . $conexion->error . "')</script>";
    } else {
      $consulta_vendedor->bind_param("s", $email);
      $consulta_vendedor->execute();
      $resultadoVendedor = $consulta_vendedor->get_result();

      if ($resultadoVendedor && $resultadoVendedor->num_rows > 0) {
        $consulta_update = $conexion->prepare("UPDATE alumnos SET contrasena = ? WHERE correo = ?");

        if (!$consulta_update) {
          echo "<script>alert('Error al preparar actualización: " . $conexion->error . "')</script>";
        } else {
          $consulta_update->bind_param("ss", $contraseñaNuevaEncriptada, $email);

          if ($consulta_update->execute()) {
            echo "<script>alert('Contraseña actualizada correctamente. Ya puedes iniciar sesión.')</script>";
            echo "<script>window.location.href='index.php';</script>";
          } else {
            echo "<script>alert('Error al actualizar la contraseña: " . $consulta_update->error . "')</script>";
          }
        }
      } else {
        echo "<script>alert('No se encontró ninguna cuenta con ese correo electrónico.')</script>";
      }
    }
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="img/ites.png" />
  <link rel="stylesheet" href="assets/css/index.css" />
  <script
    src="https://kit.fontawesome.com/e522357059.js"
    crossorigin="anonymous"></script>
  <title>Portal del estudiante</title>
</head>

<body>
  <!--Componente de ingreso-->
  <form action="" method="post" id="ingreso">
    <h1>Ingreso al portal</h1>
    <img src="img/ites.png" alt="Logo-ites" />
    <div class="contenedor-campos">
      <div class="usuario campo-contenedor">
        <input
          type="text"
          name="matricula"
          placeholder="Introduce tu matricula"
          required />
        <i class="fa-solid fa-user"></i>
      </div>
      <div class="contrasena campo-contenedor">
        <input
          type="password"
          name="contrasena"
          id="ingreso-contrasena"
          placeholder="Introduce tu contraseña"
          required />
        <i
          class="fa-regular fa-eye-slash"
          title="Mostrar Contraseña"
          onclick="revelarContraseña(this)"></i>
      </div>
      <button type="submit" name="ingresar-btn" class="ingresar-btn">Ingresar</button>
    </div>
    <div class="contenedor-opciones">
      <span
        class="forgot-pass"
        onclick="mostrarComponente(recuperarContraseña)">Olvidé mi contraseña</span>
      <div class="crear-cuenta">
        <p>
          ¿Aún no te has registrado?
          <span onclick="mostrarComponente(registro)">Crea tu cuenta para acceder al portal del alumno</span>
        </p>
      </div>
    </div>
  </form>

  <!--Componente de registro-->
  <form action="" method="post" autocomplete="on" id="registro">
    <i
      id="regresar-icono"
      class="arrow fa-solid fa-arrow-left"
      title="Regresar"
      onclick="mostrarComponente(ingreso)"></i>
    <h1>¡Regístrate!</h1>
    <div class="contenedor-campos">
      <div class="nombre campo-contenedor">
        <input
          type="text"
          name="nombre"
          class="campos-registro"
          placeholder="Introduce tu nombre"
          id="nombre"
          required />
        <i class="fa-solid fa-user"></i>
      </div>
      <div class="apellidos campo-contenedor">
        <div class="apellidoP apellido">
          <input
            type="text"
            name="apellidoP"
            id="apellidoP"
            placeholder="Apellido paterno"
            required />
          <i class="fa-solid fa-user-tie"></i>
        </div>
        <div class="apellidoM apellido">
          <input
            type="text"
            name="apellidoM"
            id="apellidoM"
            placeholder="Apellido Materno"
            required />
          <i class="fa-solid fa-user-nurse"></i>
        </div>
      </div>
      <div class="carrera campo-contenedor">
        <select name="carreras" id="carreras" title="carreras">
          <option value="">---Selecciona tu carrera---</option>
          <option value="programación">Programación y Webmaster</option>
          <option value="sistemas-computacionales">
            Sistemas Computacionales Administrativos
          </option>
          <option value="derecho">Derecho</option>
          <option value="contaduria">Contaduría</option>
          <option value="administracion">Administración de Empresas</option>
          <option value="artes-culinarias">
            Artes Culinarias y Negocios Gastronómicos
          </option>
          <option value="pedagogia">Pedagogía</option>
        </select>
      </div>
      <div class="matricula campo-contenedor">
        <input
          type="text"
          id="matricula"
          name="matricula"
          placeholder="Introduce tu matrícula" />
        <i class="fa-solid fa-id-badge"></i>
      </div>
      <div class="email campo-contenedor">
        <input
          type="email"
          id="email"
          name="email"
          placeholder="Introduce tu correo electrónico" />

        <i class="fa-solid fa-envelope"></i>
      </div>
      <div class="contraseña campo-contenedor">
        <input
          type="password"
          id="contraseña-registro"
          name="contraseña-registro"
          placeholder="Introduce una contraseña"
          title="Mostrar Contraseña" />
        <i
          class="fa-solid fa-eye-slash"
          onclick="revelarContraseña(this)"></i>
      </div>
      <p class="validacion-contraseña"></p>

      <div class="direccion-fisica campo-contenedor">
        <input
          type="text"
          id="direccion"
          name="direccion"
          placeholder="Introduce tu dirección" />
        <i class="fa-solid fa-map"></i>
      </div>
      <div class="numero-telefonico campo-contenedor">
        <input
          type="text"
          id="numero-telefonico"
          name="numero-telefonico"
          placeholder="Introduce tu número de celular" />
        <i class="fa-solid fa-mobile-screen"></i>
      </div>
      <button type="submit" class="registrar-btn" name="registrar-btn">
        Registrar
      </button>
    </div>
  </form>

  <!--Componente de recuperar contraseña-->
  <form action="" method="post" id="recuperar-contraseña" autocomplete="on">
    <i
      id="regresar-icono"
      class="arrow fa-solid fa-arrow-left"
      title="Regresar"
      onclick="mostrarComponente(ingreso)"></i>
    <h2>Recuperar contraseña</h2>
    <p class="introduce-datos">Introduce los siguientes datos:</p>
    <div class="contenedor-campos">
      <div class="email campo-contenedor">
        <input
          type="text"
          name="email-recuperar"
          placeholder="Correo electrónico"
          required />
        <i class="fa-solid fa-envelope"></i>
      </div>
      <div class="contraseña campo-contenedor">
        <input
          type="password"
          name="contraseña-recuperar"
          id="contraseña-recuperar"
          placeholder="Escribe la nueva contraseña"
          required
          title="Mostrar Contraseña" />
        <i
          class="fa-regular fa-eye-slash"
          onclick="revelarContraseña(this)"></i>
      </div>
      <p class="mensaje-contrasena"></p>
      <p class="validacion-contraseña"></p>
      <button type="submit" class="recuperar-btn" name="recuperar-btn">
        Enviar
      </button>
    </div>
  </form>

  <script src="assets/js/index.js"></script>
</body>

</html>