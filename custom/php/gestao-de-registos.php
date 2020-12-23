<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_records")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        if ($_POST["estado"] == "validar") {
            $houveErros = False;
            echo "<div class='caixaSubTitulo'><h3>Dados de registo - validação</h3></div>";
            $child_name = testarInput($_POST["child_name"]);
            $birth_date = testarInput($_POST["birth_date"]);
            $tutor_name = testarInput($_POST["tutor_name"]);
            $tutor_phone = testarInput($_POST["tutor_phone"]);
            $tutor_email = testarInput($_POST["tutor_email"]);
            if (empty($child_name) || empty($birth_date) || empty($tutor_name) || empty($tutor_phone)) {
                echo "<p class='warning textoLabels'>Não preencheu todos os campos obrigatórios!</p>";
                $houveErros = True;
            }
            if (!preg_match('/^\d{9}$/', $tutor_phone)) {
                echo "<p class='warning textoLabels'>Numero de telefone do tutor tem que ter 9 algarismos!</p>";
                $houveErros = True;
            }
            $dateList = explode("-", $birth_date);
            if (!(count($dateList) == 3&& strlen($dateList[0])==4 && strlen($dateList[1])==2 && strlen($dateList[2])==2 && checkdate($dateList[1], $dateList[2], $dateList[0]))) {
                echo "<p class='warning textoLabels'>Data tem que estar no formato AAAA-MM-DD!</p>";
                $houveErros = True;
            }
            if (!empty($tutor_email) && !filter_var($tutor_email, FILTER_VALIDATE_EMAIL)) {
                echo "<p class='warning textoLabels'>Endereço de email inválido!</p>";
                $houveErros = True;
            }
            if (1 === preg_match('~[0-9]~', $child_name) || 1 === preg_match('~[0-9]~', $tutor_name)) {
                echo "<p class='warning textoLabels'>Nomes não podem conter números!</p>";
                $houveErros = True;
            }
            if ($houveErros) {
                voltarAtras();
            } else {
                echo "<strong><p class='information'>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão corretos e pretende submeter os mesmos?\n\n";
                echo "<body>
						<ol>
						  <li><p class='textoValidar textoLabels'>Nome completo da criança:</p></li>
						  <ul><li>$child_name</li></ul>
						  <li><p class='textoValidar textoLabels'>Data de nascimento:</p></li>
						  <ul><li>$birth_date</li></ul>
						  <li><p class='textoValidar textoLabels'>Nome do encarregado de educação:</p></li>
						  <ul><li>$tutor_name</li></ul>
						  <li><p class='textoValidar textoLabels'>Telefone do encarregado de educação:</p></li>
						  <ul><li>$tutor_phone</li></ul>
						  <li><p class='textoValidar textoLabels'>Endereço de e-mail do tutor:</p></li>
						  <ul><li>$tutor_email</strong></li></ul>
						</ol>
						<form method='post'>
						<input type='hidden' value='inserir' name='estado'>
						" . voltarAtras() . " <input type='submit' value='submeter'>
						<input type='hidden' value='$child_name' name='child_name'>
						<input type='hidden' value='$birth_date' name='birth_date'>
						<input type='hidden' value='$tutor_name' name='tutor_name'>
						<input type='hidden' value='$tutor_phone' name='tutor_phone'>
						<input type='hidden' value='$tutor_email' name='tutor_email'>
						</form>
					 </body>";
            }
        } elseif ($_POST["estado"] == "inserir") {
            echo "<h3>Dados de registo - inserção</h3>";
            $child_name = testarInput($_POST['child_name']);
            $birth_date = testarInput($_POST['birth_date']);
            $tutor_name = testarInput($_POST['tutor_name']);
            $tutor_phone = testarInput($_POST['tutor_phone']);
            $tutor_email = testarInput($_POST['tutor_email']);
            $insertChildQuery = "INSERT INTO child (id,name,birth_date,tutor_name,tutor_phone,tutor_email) VALUES (NULL,'$child_name','$birth_date','$tutor_name','$tutor_phone', '$tutor_email');";
            if (!mysqli_query($mySQL, $insertChildQuery)) {
                echo "Erro: " . $insertChildQuery . "<br>" . mysqli_error($mySQL);
            } else {
                echo "Inseriu os dados de registo com sucesso.\nClique em Continuar para avançar.";
                echo "<br><a href='gestao-de-registos'>Continuar</a>";
            }
        } else {
            echo "<div class='caixaSubTitulo'><h3 >Dados de registo - introdução</h3></div>";
            echo "<div class='caixaFormulario'><strong><p class='information'>Introduza os dados pessoais básicos da criança:</strong>";
            echo "<body>
					<form method='post'>
					<p class='warning'>* Campos obrigatórios</p><br>
					<strong> Nome completo:</strong><span class='warning textoLabels'> * </span><br><input type='text' name='child_name'><br>
					<strong>Data de nascimento:<span class='warning textoLabels'> * </span><br><input type='text' name='birth_date' placeholder='AAAA-MM-DD'><br>
					<strong>Nome completo do encarregado de educação:</strong><span class='warning textoLabels'> * </span><br><input type='text' name='tutor_name'><br>
					<strong>Telefone do encarregado de educação:</strong><span class='warning textoLabels'> * </span><br><input type='text' name='tutor_phone'><br>
					<strong>Endereço de e-mail do tutor: </strong><br><input type='text' name='tutor_email' placeholder='email@example.com'><br><br>
					<input type='hidden' value='validar' name='estado'>
					<input type='submit' value='submeter' class='submitButton textoLabels'>
					</form>
				</body>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>

