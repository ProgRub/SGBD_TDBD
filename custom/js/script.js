// function validarGestaoUnidades() {
//   // document.getElementsByTagName("text");
//   var input = document.getElementById("nome_unidade");
//   if (input.value === "") {
//     console.log("VAZIO");
//   } else {
//     console.log("PREENCHIDO");
//   }
// }
// console.log(document.getElementsByTagName('form'));
document.getElementsByTagName('form')[1].addEventListener('submit', function (event) {
  if (document.getElementById("nome_unidade").value === "") {
    console.log("VAZIO");
    event.preventDefault();
  }
})