let formArray = [];
for (let index = 0; index < 1000; index++) {
  let input = document.getElementById(index.toString());
  if (input != null) {
    formArray.push(input);
  } else {
    break;
  }
}

console.log(formArray);

formArray.forEach((input) => {
  input.addEventListener("click", function () {
    this.className = "textInput";
    this.placeholder = "";
  });
});

document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    formArray.forEach((input) => {
      if (input.value === "") {
        input.className = "textInputWrong";
        input.placeholder = "Este campo é obrigatório.";
        event.preventDefault();
      }
    });
  });
