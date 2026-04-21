<!-- <?php
require_once("../src/conexion.php");

//Mostrar vendedores, se usa inner join para enlazarlo con la tabla periodo
$sql = "SELECT idVendedor, nombre, apellidoP FROM vendedor";
$resultado = $conexion->query($sql);

if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}

?>-->
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

    <a href="portal.php" class="regresar-btn"><i class="fa-solid fa-arrow-left"></i></a>

    <div class="contenedor">
      <div class="tarjeta-perfil">

        <div class="encabezado-perfil">
          <div class="avatar">
            <i class="fa-solid fa-user"></i>
          </div>
          <div>
            <div class="nombre-alumno"></div>
            <div class="matricula-alumno"></div>
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
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

  </body>
</html>
