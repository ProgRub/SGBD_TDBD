document.getElementById("nome_unidade").addEventListener("click", function () {
  this.className = "textInput";
      this.placeholder = "";
});

document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    let input = document.getElementById("nome_unidade");
    if (input.value === "") {
      input.className = "textInputWrong";
      input.placeholder = "Nome é obrigatório.";
      event.preventDefault();
    }
  });
