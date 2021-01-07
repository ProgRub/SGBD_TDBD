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
      if (
        listDate.length != 3 ||
        listDate[0].length != 4 ||
        listDate[1].length != 2 ||
        listDate[2].length != 2
      ) {
        console.log("DATA INVALIDA");
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
          console.log("DATA INVALIDA");
        }
      }
    }
    input = document.getElementById("tutor_name");
    if (input.value === "" || /\d/.test(input.value)) {
      console.log("VAZIO2");
      event.preventDefault();
    }
    input = document.getElementById("tutor_phone");
    if (
      input.value === "" ||
      input.value.length !== 9 ||
      isNaN(input.value) ||
      isNaN(parseFloat(input.value))
    ) {
      console.log("VAZIO3");
      event.preventDefault();
    }
    input = document.getElementById("tutor_email");
    if (input.value === "" || !/\S+@\S+\.\S+/.test(input.value)) {
      console.log("VAZIO4");
      event.preventDefault();
    }
  });
