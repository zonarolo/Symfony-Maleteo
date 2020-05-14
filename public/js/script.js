form = document.getElementById("formulario");
formularioEnv = document.getElementById("formularioEnviado");

document.getElementById("enviar")
        .addEventListener("click", 
function (e) {
  let data = {
    nombre: document.getElementById("demo_form_nombre").value,
    email: document.getElementById("demo_form_email").value,
    ciudad: document.getElementById("demo_form_ciudad").value
  };

  //comprobamos si esta vacio
  if (data.nombre != "" && data.email != "" && data.ciudad != ""){
    fetch(
      "/demo/js/submit",
      {
        method: "POST",
        body: JSON.stringify(data)
      }
    )
    .then((response) => response.json())
    .then((content) => {
      formularioEnv.style.visibility = "visible";
      form.reset(); 
      formularioEnv.innerHTML = "<i class='fa fa-check'></i> Solicitud enviada correctamente."
      formularioEnv.className = "isa_success";

    });

  }
  e.preventDefault();
});