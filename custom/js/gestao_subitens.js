document.getElementById("nome_subitem").addEventListener("click", function () {
  this.className = "textInput";
  this.placeholder = "";
});

document.getElementById("item").addEventListener("click", function () {
  this.className = "textInput textoLabels";
  this.placeholder = "";
});

document
  .getElementById("ordem_campo_form")
  .addEventListener("click", function () {
    this.className = "textInput";
    this.placeholder = "";
  });

document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    let input = document.getElementById("nome_subitem");
    if (input.value === "" || /\d/.test(input.value)) {
      input.className = "textInputWrong";
      input.value = "";
      input.placeholder = "Nome do subitem é obrigatório e não deve ter dígitos.";
      event.preventDefault();
    }
    input = document.getElementById("item");
    if (input.value === "selecione_um_item") {
      input.className = "textInputWrong textoLabels";
      event.preventDefault();
    }
    input = document.getElementById("ordem_campo_form");
      console.log(parseInt(input.value));
    if (input.value === "") {
      input.className = "textInputWrong";
      input.placeholder = "Ordem do campo no formulário é obrigatório.";
      event.preventDefault();
    } else if (parseInt(input.value) <= 0 || isNaN(parseInt(input.value))) {
      input.className = "textInputWrong";
      input.value="";
      input.placeholder = "O número tem que ser superior a 0.";
      event.preventDefault();
    }
  });
