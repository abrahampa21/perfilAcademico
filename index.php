<!-- /*
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include("src/conexion.php");

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

// Inicializar variables de mensajes
$mensajeusuarioexiste = "";
$mensajeadmin = "";
$mensajeine = "";
$mensajeusuario = "";
$mensajevendedor = "";
$mensajecontraseña = "";
$mensajeresultado = "";

if (isset($_POST["ingresar"])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['contraseña'];
    $token    = $_POST['token'];
    $password_encriptada = sha1($password);

    // Administrador
    $stmt_admin = $conexion->prepare("SELECT usuario FROM administrador WHERE usuario = ? AND password = ? AND token = ? LIMIT 1");
    $stmt_admin->bind_param("sss", $usuario, $password_encriptada, $token);
    $stmt_admin->execute();
    $resultado_admin = $stmt_admin->get_result();

    if ($resultado_admin && $resultado_admin->num_rows > 0) {
        $row = $resultado_admin->fetch_assoc();
        $_SESSION['usuarioAdmin'] = $row['usuario'];
        header("Location: panelAdmin.php");
        exit();
    }

    // Vendedor
    $stmt_vendedor = $conexion->prepare("SELECT idVendedor, usuario FROM vendedor WHERE usuario = ? AND password = ? LIMIT 1");
    $stmt_vendedor->bind_param("ss", $usuario, $password_encriptada);
    $stmt_vendedor->execute();
    $resultado_vendedor = $stmt_vendedor->get_result();

    if ($resultado_vendedor && $resultado_vendedor->num_rows > 0) {
        $row = $resultado_vendedor->fetch_assoc();
        $_SESSION['usuarioVendedor'] = $row['usuario'];
        $_SESSION['idVendedor'] = $row['idVendedor'];
        header("Location: panelVendedor.php");
        exit();
    } else {
        $mensajeresultado = "error";
    }
}

// Registro para administrador
if (isset($_POST["registrar-admin"])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['email'];
    $usuario = $_POST['usuario'];
    $password = $_POST['contraseña'];

    if (!validar_contraseña($password)) {
        $mensajeadmin = "contraseña-invalida";
    } else {
        $password_encriptada = sha1($password);

        $stmt_verificar = $conexion->prepare("SELECT usuario FROM administrador WHERE usuario = ? LIMIT 1");
        $stmt_verificar->bind_param("s", $usuario);
        $stmt_verificar->execute();
        $resultado_verificar = $stmt_verificar->get_result();

        if ($resultado_verificar && $resultado_verificar->num_rows > 0) {
            $mensajeusuarioexiste = "error";
        } else {
            $stmt_insert = $conexion->prepare("INSERT INTO administrador (usuario, nombreCompleto, password, email) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $usuario, $nombre, $password_encriptada, $correo);
            if ($stmt_insert->execute()) {
                $mensajeadmin = "exito";
            } else {
                $mensajeadmin = "error";
            }
        }
    }
}

// Registro del vendedor
if (isset($_POST["registrar-vendedor"])) {
    // Recoge datos del formulario...
    $nombre = $_POST['nombre-vendedor'];
    $apellidoP = $_POST['apellidoP-vendedor'];
    $apellidoM = $_POST['apellidoM-vendedor'];
    $correo = $_POST['email-vendedor'];
    $usuario = $_POST['usuario-vendedor'];
    $password = $_POST['contraseña-vendedor'];
    $numeroCel = $_POST['noCelular-vendedor'];
    $numeroRef = $_POST['noReferencia-vendedor'];

    if (!validar_contraseña($password)) {
        $mensajevendedor = "contraseña-invalida";
    } else {
        $password_encriptada = sha1($password);

        // Procesar INE (igual que antes)
        if (isset($_FILES['ine-vendedor']) && $_FILES['ine-vendedor']['error'] === 0) {
            $ine = file_get_contents($_FILES['ine-vendedor']['tmp_name']);
        } else {
            $mensajeine = "error-foto";
        }

        // Procesar video para moverlo a la carpeta uploads/videos/
        $video_ruta = null;
        if (isset($_FILES['video']) && $_FILES['video']['error'] === 0) {
            $nombreArchivo = basename($_FILES['video']['name']);
            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);

            // Validar extensión si quieres (mp4, avi, etc)
            $ext_permitidas = ['mp4', 'avi', 'mov', 'wmv', 'mkv'];
            if (in_array(strtolower($extension), $ext_permitidas)) {
                $nuevoNombre = uniqid('video_') . '.' . $extension;
                $rutaDestino = __DIR__ . '/uploads/videos/' . $nuevoNombre;

                if (move_uploaded_file($_FILES['video']['tmp_name'], $rutaDestino)) {
                    $video_ruta = 'uploads/videos/' . $nuevoNombre; // Ruta relativa para guardar en DB
                } else {
                    $mensajevideo = "error-subida-video";
                }
            } else {
                $mensajevideo = "extension-no-permitida";
            }
        }

        if ($mensajeine !== "error-foto" && !isset($mensajevideo)) {
            $stmt_verificar = $conexion->prepare("SELECT usuario FROM vendedor WHERE usuario = ? LIMIT 1");
            $stmt_verificar->bind_param("s", $usuario);
            $stmt_verificar->execute();
            $resultado_verificar = $stmt_verificar->get_result();

            if ($resultado_verificar && $resultado_verificar->num_rows > 0) {
                $mensajeusuario = "error";
            } else {
                // Insertar datos con ruta de video
                $stmt_insert = $conexion->prepare("INSERT INTO vendedor(usuario, nombre, apellidoP, apellidoM, email, password, fotoINE, noCelular, noReferencia, video) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt_insert->bind_param("ssssssssss", $usuario, $nombre, $apellidoP, $apellidoM, $correo, $password_encriptada, $ine, $numeroCel, $numeroRef, $video_ruta);

                if ($stmt_insert->execute()) {
                    $mensajevendedor = "exito";
                } else {
                    $mensajevendedor = "error";
                }
            }
        }
    }
}


// Recuperar contraseña
if (isset($_POST["recuperar-btn"])) {
    $email = $_POST['email-recuperar'];
    $contraseñaNueva = $_POST['contraseña-recuperar'];

    if (!validar_contraseña($contraseñaNueva)) {
        $mensajecontraseña = "contraseña-invalida";
    } else {
        $contraseñaNuevaEncriptada = sha1($contraseñaNueva);

        $stmt_admin = $conexion->prepare("SELECT * FROM administrador WHERE email = ? LIMIT 1");
        $stmt_admin->bind_param("s", $email);
        $stmt_admin->execute();
        $resultadoAdmin = $stmt_admin->get_result();

        $stmt_vendedor = $conexion->prepare("SELECT * FROM vendedor WHERE email = ? LIMIT 1");
        $stmt_vendedor->bind_param("s", $email);
        $stmt_vendedor->execute();
        $resultadoVendedor = $stmt_vendedor->get_result();

        if ($resultadoAdmin && $resultadoAdmin->num_rows > 0) {
            $stmt_update = $conexion->prepare("UPDATE administrador SET password = ? WHERE email = ?");
            $stmt_update->bind_param("ss", $contraseñaNuevaEncriptada, $email);
            $mensajecontraseña = $stmt_update->execute() ? "exito" : "error";
        } elseif ($resultadoVendedor && $resultadoVendedor->num_rows > 0) {
            $stmt_update = $conexion->prepare("UPDATE vendedor SET password = ? WHERE email = ?");
            $stmt_update->bind_param("ss", $contraseñaNuevaEncriptada, $email);
            $mensajecontraseña = $stmt_update->execute() ? "exito" : "error";
        } else {
            $mensajecontraseña = "error1";
        }
    }
}
?>
*/ -->
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="img/ites.png" />
    <link rel="stylesheet" href="assets/css/index.css" />
    <script
      src="https://kit.fontawesome.com/e522357059.js"
      crossorigin="anonymous"
    ></script>
    <title>Portal del estudiante</title>
  </head>
  <body>
    <!--Componente de ingreso-->
    <form action="" id="ingreso">
      <h1>Ingreso al portal</h1>
      <img src="img/ites.png" alt="Logo-ites" />
      <div class="contenedor-campos">
        <div class="usuario campo-contenedor">
          <input
            type="text"
            name="matricula"
            placeholder="Introduce tu matricula"
            required
          />
          <i class="fa-solid fa-user"></i>
        </div>
        <div class="contrasena campo-contenedor">
          <input
            type="password"
            name="contrasena"
            id="ingreso-contrasena"
            placeholder="Introduce tu contraseña"
            required
          />
          <i
            class="fa-regular fa-eye-slash"
            title="Mostrar Contraseña"
            onclick="revelarContraseña(this)"
          ></i>
        </div>
      </div>
      <div class="contenedor-opciones">
        <span
          class="forgot-pass"
          onclick="mostrarComponente(recuperarContraseña)"
          >Olvidé mi contraseña</span
        >
        <div class="crear-cuenta">
          <p>
            ¿Aún no te has registrado?
            <span onclick="mostrarComponente(registro)"
              >Crea tu cuenta para acceder al portal del alumno</span
            >
          </p>
        </div>
      </div>
    </form>

    <!--Componente de registro-->
    <form action="" id="registro">
      <i
        id="regresar-icono"
        class="arrow fa-solid fa-arrow-left"
        title="Regresar"
        onclick="mostrarComponente(ingreso)"
      ></i>
      <h1>¡Regístrate!</h1>
      <div class="contenedor-campos">
        <div class="nombre campo-contenedor">
          <input
            type="text"
            name="nombre"
            class="campos-registro"
            placeholder="Introduce tu nombre"
            id="nombre"
            required
          />
          <i class="fa-solid fa-user"></i>
        </div>
        <div class="apellidos campo-contenedor">
          <div class="apellidoP apellido">
            <input
              type="text"
              name="apellidoP"
              id="apellidoP"
              placeholder="Apellido paterno"
              required
            />
            <i class="fa-solid fa-user-tie"></i>
          </div>
          <div class="apellidoM apellido">
            <input
              type="text"
              name="apellidoM"
              id="apellidoM"
              placeholder="Apellido Materno"
              required
            />
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
            placeholder="Introduce tu matrícula"
          />
          <i class="fa-solid fa-id-badge"></i>
        </div>
        <div class="email campo-contenedor">
          <input
            type="text"
            id="email"
            name="email"
            placeholder="Introduce tu correo electrónico"
          />

          <i class="fa-solid fa-envelope"></i>
        </div>
        <div class="contraseña campo-contenedor">
          <input
            type="password"
            id="contraseña-registro"
            name="contraseña"
            placeholder="Introduce una contraseña"
            title="Mostrar Contraseña"

          />
          <i
            class="fa-solid fa-eye-slash"
            onclick="revelarContraseña(this)"
          ></i>
        </div>
          <p class="validacion-contraseña"></p>

        <div class="direccion-fisica campo-contenedor">
          <input
            type="text"
            id="direccion"
            name="direccion"
            placeholder="Introduce tu dirección"
          />
          <i class="fa-solid fa-map"></i>
        </div>
        <div class="numero-telefonico campo-contenedor">
          <input
            type="text"
            id="numero-telefonico"
            name="numero"
            placeholder="Introduce tu número de celular"
          />
          <i class="fa-solid fa-mobile-screen"></i>
        </div>
        <button type="submit" class="registrar-btn" name="registrar-btn">
          Registrar
        </button>
      </div>
    </form>

    <!--Componente de recuperar contraseña-->
    <form action="" method="post" id="recuperar-contraseña">
      <i
        id="regresar-icono"
        class="arrow fa-solid fa-arrow-left"
        title="Regresar"
        onclick="mostrarComponente(ingreso)"
      ></i>
      <h2>Recuperar contraseña</h2>
      <p class="introduce-datos">Introduce los siguientes datos:</p>
      <div class="contenedor-campos">
        <div class="email campo-contenedor">
          <input
            type="text"
            name="email-recuperar"
            placeholder="Correo electrónico"
            required
          />
          <i class="fa-solid fa-envelope"></i>
        </div>
        <div class="contraseña campo-contenedor">
          <input
            type="password"
            name="contraseña-recuperar"
            id="contraseña-recuperar"
            placeholder="Escribe la nueva contraseña"
            required
            title="Mostrar Contraseña"
          />
          <i
            class="fa-regular fa-eye-slash"
            onclick="revelarContraseña(this)"
          ></i>
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
