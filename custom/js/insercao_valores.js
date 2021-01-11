let formArray = [];
let allInputs = document.getElementsByTagName("input");
for (let index = 0; index < allInputs.length; index++) {
  console.log(allInputs[index].id);
  if (allInputs[index].id.length == 1) {
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
    for (let index = 0; index < formArray.length; index++) {
      const input = formArray[index];
      if (input.getAttribute("type") == "text" && input.value === "") {
        input.className = "textInputWrong";
        input.placeholder = "Este campo é obrigatório.";
        event.preventDefault();
      } else if (input.getAttribute("type") != "text") {
        let indexSecond=index;
        let inputType=input.getAttribute("type") ;
        let inputsSameType=[];
        while (indexSecond<formArray.length) {
          if (formArray[indexSecond].getAttribute("type") == inputType) {
            inputsSameType.push(formArray[indexSecond]);
          }
          else{
            break;
          }
          indexSecond++;
        }
        index=indexSecond;
        let checked=false;
        inputsSameType.forEach((button)=>{
          if (button.checked){
            checked=true;
            break;
          }
        });
        if (!checked) {
          event.preventDefault();
        }
      }
    }
  });
