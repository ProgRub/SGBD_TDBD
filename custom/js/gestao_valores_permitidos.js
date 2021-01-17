document.getElementById("valor_permitido").addEventListener("click", function () {
  this.className = "textInput";
      this.placeholder = "";
});

document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    let input = document.getElementById("valor_permitido");
    if (input.value === "") {
      input.className = "textInputWrong";
      input.value="";
      input.placeholder = "Valor é obrigatório.";
      event.preventDefault();
    }
  });