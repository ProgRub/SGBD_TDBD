document.getElementById("nome_subitem").addEventListener("click", function () {
  this.className = "textInput";
      this.placeholder = "";
});

document.getElementById("item").addEventListener("click", function () {
  this.className = "textInput textoLabels";
      this.placeholder = "";
});

document.getElementById("ordem_campo_form").addEventListener("click", function () {
  this.className = "textInput";
      this.placeholder = "";
});


document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    let input = document.getElementById("nome_subitem");
    if (input.value === "") {
      input.className = "textInputWrong";
      input.placeholder = "Nome do subitem é obrigatório.";
      event.preventDefault();
    }
	input = document.getElementById("item");
    if (input.value === "selecione_um_item") {
      input.className = "textInputWrong textoLabels";
      event.preventDefault();
    }
    input = document.getElementById("ordem_campo_form");
    if (input.value === "") {
      input.className = "textInputWrong";
      input.placeholder = "Ordem do campo no formulário é obrigatório.";
      event.preventDefault();
    }
  });