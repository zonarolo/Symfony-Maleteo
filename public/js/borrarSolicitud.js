boton = document.getElementsByClassName("BB");

for (let i = 0; i < boton.length; i++){
  boton[i].addEventListener("click", (e) => {
    myId = e.target.id;
    
    fetch(
      "/maleteo/solicitudes/"+myId+"/borrar",
      {
        method: "POST",
        body: myId
      }
    ).then((content) => {
      document.getElementById(myId).remove();
    });

    e.preventDefault();
  })
}