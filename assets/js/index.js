const campos = document.querySelectorAll("input");
const registro = document.getElementById("registro");
const inicioSesion = document.getElementById("ingreso");
const recuperarContraseña = document.getElementById("recuperar-contraseña");
const regex = /^(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>/?])(?=.*[A-Za-z]).{10,}$/; //Para que cumpla con los requisitos de contraseña
const formularios = [registro, inicioSesion, recuperarContraseña];
const contraseñaRecuperar = document.getElementById("contraseña-recuperar");
const contraseñaRegistro = document.getElementById("contraseña-registro");

//No dejar copiar los contenidos de las contraseñas
function bloquearCopiadoContraseñas() {
    const inputs = document.querySelectorAll('input[type="password"]');
    inputs.forEach(input => {
        input.addEventListener("copy", e => e.preventDefault());
        input.addEventListener("contextmenu", e => e.preventDefault());
        input.addEventListener("keydown", e => {
            if ((e.ctrlKey || e.metaKey) && ["c", "x", "a"].includes(e.key.toLowerCase())) {
                e.preventDefault();
            }
        });  
});
}

function mostrarComponente(componente) {
    formularios.forEach(form => form.style.display = "none");
    componente.style.display = "flex";
    campos.forEach(input => input.value = "");
}

function revelarContraseña(icono) {
  const contenedor = icono.parentElement;
  const input = contenedor.querySelector("input");

  const estaOculta = input.type === "password";
  input.type = estaOculta ? "text" : "password";

  icono.classList.toggle("fa-eye");
  icono.classList.toggle("fa-eye-slash");
}

bloquearCopiadoContraseñas();

function validarContraseña(input, mensaje) {
  input.addEventListener("input", () => {
    if (regex.test(input.value)) {
        mensaje.style.display = "block";
      mensaje.textContent = "Contraseña válida";
      mensaje.style.color = "green";
      mensaje.style.fontSize = "12px";
    } else {
        mensaje.style.display = "block";
      mensaje.textContent =
        "Debe tener al menos 10 caracteres, letras y por lo menos 1 carácter especial (ej. !2%4a9328#)";
      mensaje.style.color = "red";
      mensaje.style.fontSize = "12px";
    }
  });
}

validarContraseña(contraseñaRecuperar, document.querySelector("#recuperar-contraseña .validacion-contraseña"));
validarContraseña(contraseñaRegistro, document.querySelector("#registro .validacion-contraseña"));