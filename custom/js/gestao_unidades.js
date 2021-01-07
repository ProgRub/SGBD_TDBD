document
  .getElementsByTagName("form")[1]
  .addEventListener("submit", function (event) {
    let input = document.getElementById("nome_unidade");
    if (input.value === "") {
	//   console.log("VAZIO");
	input.className="textInputWrong";
      event.preventDefault();
    }
  });
