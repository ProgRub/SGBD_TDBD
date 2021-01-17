let formArray = [];
let allInputs = document.getElementsByTagName("input");
// console.log(allInputs);
for (let index = 0; index < allInputs.length; index++) {
  // console.log(allInputs[index].id);
  if (allInputs[index].id.length <= 3 && allInputs[index].id.length >= 1) {
    formArray.push(allInputs[index]);
  }
}
// console.log(document.getElementById("0"));
allInputs = document.getElementsByTagName("textarea");
// console.log(allInputs);
for (let index = 0; index < allInputs.length; index++) {
  // console.log(allInputs[index].id);
  if (allInputs[index].id.length <= 3 && allInputs[index].id.length >= 1) {
    formArray.push(allInputs[index]);
  }
}
allInputs = document.getElementsByTagName("select");
// console.log(allInputs);
for (let index = 0; index < allInputs.length; index++) {
  // console.log(allInputs[index].id);
  if (allInputs[index].id.length <= 3 && allInputs[index].id.length >= 1) {
    formArray.push(allInputs[index]);
  }
}

// console.log(formArray);

formArray.forEach((input) => {
  if (input.getAttribute("type") == "text") {
    input.addEventListener("click", function () {
      this.className = "textInput";
      this.placeholder = "";
    });
  } else if (input.tagName == "TEXTAREA") {
    input.addEventListener("click", function () {
      this.className = "textArea";
      this.placeholder = "";
    });
  }
});

document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    for (let index = 0; index < formArray.length; index++) {
      let input = formArray[index];
      console.log(index);
      if (input.getAttribute("type") == "text") {
        if (input.value === "") {
          input.className = "textInputWrong";
          input.placeholder = "Este campo é obrigatório.";
          event.preventDefault();
        }
      } else if (input.tagName == "TEXTAREA") {
        input.className = "textAreaWrong";
        input.placeholder = "Este campo é obrigatório.";
        event.preventDefault();
      } else if (input.tagName == "SELECT") {
        if (input.value === "empty") {
          event.preventDefault();
        }
      } else {
        let indexSecond = index;
        let inputType = input.getAttribute("type");
        let inputsSameType = [];
        let whileCondition = true;
        while (indexSecond < formArray.length && whileCondition) {
          if (formArray[indexSecond].getAttribute("type") == inputType) {
            inputsSameType.push(formArray[indexSecond]);
          } else {
            index = indexSecond - 1;
            whileCondition = false;
          }
          indexSecond++;
          index++;
        }
        let checked = false;
        // console.log(inputsSameType);
        inputsSameType.forEach((button) => {
          if (button.checked) {
            checked = true;
          }
        });
        if (!checked) {
          event.preventDefault();
        }
      }
    }
  });
