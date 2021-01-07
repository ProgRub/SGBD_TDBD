document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    let input = document.getElementById("nome_item");
    if (input.value === "") {
      //   console.log("VAZIO");
	  input.className = "textInputWrong";
	  input.placeholder="Nome é obrigatório!";
      event.preventDefault();
    }
  });
