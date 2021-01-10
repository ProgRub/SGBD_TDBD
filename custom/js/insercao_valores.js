let formArray = [];
let allInputs = document.getElementsByTagName("input");
for(let index=0;index<allInputs.length;index++){
  console.log(allInputs[index].id);
  if (allInputs[index].id.length==1) {
    formArray.push(allInputs[index]);
  }
}

console.log(formArray);

formArray.forEach((input) => {
  if (input.getAttribute("type") == "text") {
    input.addEventListener("click", function () {
      this.className = "textInput";
      this.placeholder = "";
    });
  }
});

document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    formArray.forEach((input) => {
      if (input.getAttribute("type") == "text" && input.value === "") {
        input.className = "textInputWrong";
        input.placeholder = "Este campo é obrigatório.";
        event.preventDefault();
      } else if (input.getAttribute("type") != "text") {
        event.preventDefault();
      }
    });
  });
