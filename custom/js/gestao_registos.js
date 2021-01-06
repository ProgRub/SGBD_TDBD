document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    let input = document.getElementById("child_name");
    if (input.value === "" || /\d/.test(input.value)) {
      console.log("VAZIO");
      event.preventDefault();
    }
    input = document.getElementById("birth_date");
    if (input.value === "") {
      console.log("VAZIO1");
      event.preventDefault();
    } else {
        let listDate = input.value.split("-");
        if (listDate.length != 3||listDate[0].length!=4||listDate[1].length!=2||listDate[2].length!=2) {
            console.log("DATA INVALIDA");
        } else {
            Date.prototype.isValid = function () {
            return this.getTime() === this.getTime();
        };
            if (parseInt(listDate[1], 10) === 2) {
                
            }
            else if (parseInt(listDate[1], 10) === 11 && parseInt(listDate[1], 10) === 4 && parseInt(listDate[1], 10) === 6 && parseInt(listDate[1], 10) === 9) {
                
            }
            else {
                
            }
        }
      }
    input = document.getElementById("tutor_name");
    if (input.value === "") {
      console.log("VAZIO2");
      event.preventDefault();
    }
    input = document.getElementById("tutor_phone");
    if (input.value === "") {
      console.log("VAZIO3");
      event.preventDefault();
    }
    input = document.getElementById("tutor_email");
    if (input.value === "") {
      console.log("VAZIO4");
      event.preventDefault();
    }
  });
