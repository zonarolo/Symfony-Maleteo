boton = document.getElementsByClassName("BotonBorrar");

for (let i = 0; i < boton.length; i++){
  boton[i].addEventListener("click", (e) => {
    myId = e.target.id;
    
    if(confirm("Seguro que quieres borrar este registro?")){

      fetch(
      "/maleteo/opiniones/"+myId+"/borrar",
      {
        method: "POST",
        body: myId
  
      })
      
      .then((content) => {
        document.getElementById(myId).remove();
      });
    }
    e.preventDefault();
  });
}

botonEditar = document.getElementsByClassName("BotonEditar");

for (let j = 0; j < botonEditar.length; j++){
  botonEditar[j].addEventListener("click", (r) => {
    myEditID = r.target.id;
    
    if(confirm("Seguro que quieres editar este registro?")){

      fetch(
      "/maleteo/opiniones/"+myEditID+"/editar",
      {
        method: "POST",
        body: myEditID
  
      })
    }
  });
}

