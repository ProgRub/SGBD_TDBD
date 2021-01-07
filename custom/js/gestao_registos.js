document.getElementById("child_name").addEventListener("click", function () {
  this.className = "textInput";
      this.placeholder = "";
});

document.getElementById("birth_date").addEventListener("click", function () {
  this.className = "textInput";
      this.placeholder = "";
});

document.getElementById("tutor_name").addEventListener("click", function () {
  this.className = "textInput";
      this.placeholder = "";
});

document.getElementById("tutor_phone").addEventListener("click", function () {
  this.className = "textInput";
      this.placeholder = "";
});

document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    let input = document.getElementById("child_name");
    if (input.value === "" || /\d/.test(input.value)) {
		  input.value = "";
      input.className = "textInputWrong";
      input.placeholder =
        "Nome da criança é obrigatório e não deve ter dígitos.";
      event.preventDefault();
    }
    input = document.getElementById("birth_date");
    if (input.value === "") {
        input.className = "textInputWrong";
        input.placeholder = "Data é obrigatória, formato: AAAA-MM-DD.";
      event.preventDefault();
    } else {
      let listDate = input.value.split("-");
      if (
        listDate.length !== 3 ||
        listDate[0].length !== 4 ||
        listDate[1].length !== 2 ||
        listDate[2].length !== 2
      ) {
		  input.value = "";
        input.className = "textInputWrong";
        input.placeholder = "Formato: AAAA-MM-DD.";
      } else {
        let maximoMes = -1;
        if (parseInt(listDate[1], 10) === 2) {
          let year = parseInt(listDate[0], 10);
          if (year % 400 === 0) {
            //ano bissexto
            maximoMes = 29;
          } else if (year % 100 === 0) {
            //ano normal
            maximoMes = 28;
          } else if (year % 4 === 0) {
            //ano bissexto
            maximoMes = 29;
          }
        } else if (
          parseInt(listDate[1], 10) === 11 &&
          parseInt(listDate[1], 10) === 4 &&
          parseInt(listDate[1], 10) === 6 &&
          parseInt(listDate[1], 10) === 9
        ) {
          maximoMes = 30;
        } else if (
          parseInt(listDate[1], 10) <= 12 &&
          parseInt(listDate[1], 10) >= 1
        ) {
          maximoMes = 31;
        }
        if (parseInt(listDate[2], 10) > maximoMes) {
		  input.value = "";
          input.className = "textInputWrong";
          input.placeholder = "Data tem de ser válida, formato: AAAA-MM-DD.";
        }
      }
    }
    input = document.getElementById("tutor_name");
    if (input.value === "" || /\d/.test(input.value)) {
		  input.value = "";
      input.className = "textInputWrong";
      input.placeholder = "Nome do tutor é obrigatório e não deve ter dígitos.";
      event.preventDefault();
    }
    input = document.getElementById("tutor_phone");
    if (
      input.value === ""
      ) {
      input.className = "textInputWrong";
      input.placeholder = "Telefone do tutor é obrigatório.";
      event.preventDefault();
	}
	else if(input.value.length !== 9 ||
      isNaN(input.value) ||
      isNaN(parseFloat(input.value))){
		  input.value="";
      input.className = "textInputWrong";
      input.placeholder = "Telefone do tutor deve ter 9 dígitos.";
      event.preventDefault();
	  }
  });
