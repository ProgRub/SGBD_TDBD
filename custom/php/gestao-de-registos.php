<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_records")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
		echo "Erro na ligação com a base de dados";
    } 
	else {
		echo "<h3>Dados de registo - introdução</h3>"; 
		echo "Introduza os dados pessoais básicos da criança:";
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>


<!DOCTYPE HTML>
<html>
<body>

<form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
* Campos obrigatórios
<p></p>
Nome completo* <input type="text" name="crianca_nome"><br>
Data de nascimento* <input type="text" name="data_de_nascimento"><br>
Nome completo do encarregado de educação* <input type="text" name="ee_nome"><br>
Telefone do encarregado de educação* <input type="text" name="ee_telefone"><br>
Endereço de e-mail do tutor <input type="text" name="tutor_email"><br>
<p></p>
<input type="submit">
</form>

</body>
</html>

