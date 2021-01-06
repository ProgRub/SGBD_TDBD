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
            echo "<div class='caixaFormulario'>";
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
            if (!(count($dateList) == 3 && strlen($dateList[0]) == 4 && strlen($dateList[1]) == 2 && strlen($dateList[2]) == 2 && checkdate($dateList[1], $dateList[2], $dateList[0]))) {
                echo "<p class='warning textoLabels'>Data tem que estar no formato AAAA-MM-DD e ser válida!</p>";
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
                echo "<strong><span class='information'>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão corretos e pretende submeter os mesmos?</span><br><br>";
                echo "<ol>
						  <li><p class='textoValidar'>Nome completo da criança:</p></li>
						  <ul><li>$child_name</li></ul>
						  <li><p class='textoValidar'>Data de nascimento:</p></li>
						  <ul><li>$birth_date</li></ul>
						  <li><p class='textoValidar'>Nome do encarregado de educação:</p></li>
						  <ul><li>$tutor_name</li></ul>
						  <li><p class='textoValidar'>Telefone do encarregado de educação:</p></li>
						  <ul><li>$tutor_phone</li></ul>
						  <li><p class='textoValidar'>Endereço de e-mail do tutor:</p></li>
						  <ul><li>$tutor_email</strong></li></ul>
						</ol>
						<form method='post'>
						<p hidden>
						<input type='hidden' value='inserir' name='estado'> 
						<input type='hidden' value='$child_name' name='child_name'>
						<input type='hidden' value='$birth_date' name='birth_date'>
						<input type='hidden' value='$tutor_name' name='tutor_name'>
						<input type='hidden' value='$tutor_phone' name='tutor_phone'>
						<input type='hidden' value='$tutor_email' name='tutor_email'>
						</p>
						<input type='submit' value='submeter' class='submitButton'>
						</form>";
						//voltarAtras();		
            }
            echo "</div>";
        } elseif ($_POST["estado"] == "inserir") {
            echo "<div class='caixaSubTitulo'><h3>Dados de registo - inserção</h3></div>";
            echo "<div class='caixaFormulario'>";
            $child_name = testarInput($_POST['child_name']);
            $birth_date = testarInput($_POST['birth_date']);
            $tutor_name = testarInput($_POST['tutor_name']);
            $tutor_phone = testarInput($_POST['tutor_phone']);
            $tutor_email = testarInput($_POST['tutor_email']);
            $insertChildQuery = "INSERT INTO child (id,name,birth_date,tutor_name,tutor_phone,tutor_email) VALUES (NULL,'$child_name','$birth_date','$tutor_name','$tutor_phone', '$tutor_email');";
            if (!mysqli_query($mySQL, $insertChildQuery)) {
                echo "<span class='warning'>Erro: " . $insertChildQuery . "<br>" . mysqli_error($mySQL)."</span>";
            } else {
                echo "<span class='information'>Inseriu os dados de registo com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.</span><br><br>";
                echo "<a href='gestao-de-registos'><button class='continuarButton textoLabels'>Continuar</button></a>";
            }
            echo "</div>";
        } else {
            echo "<div class='caixaSubTitulo'><h3 >Dados de registo - introdução</h3></div>";
            echo "<div class='caixaFormulario'>";
            echo "<span class='information'><strong>Introduza os dados pessoais básicos da criança:</strong></span><br>
			<span class='warning'>* Campos obrigatórios</span><br>";
            echo "<form method='post'>
					<strong class='textoLabels'> Nome completo:</strong><span class='warning textoLabels'> * </span><br>
					<input type='text' class='textInput' name='child_name'><br>
					<strong class='textoLabels'>Data de nascimento:<span class='warning textoLabels'> * </span><br>
					<input type='text' class='textInput' name='birth_date' placeholder='AAAA-MM-DD'><br>
					<strong class='textoLabels'>Nome completo do encarregado de educação:</strong><span class='warning textoLabels'> * </span><br>
					<input type='text' class='textInput' name='tutor_name'><br>
					<strong class='textoLabels'>Telefone do encarregado de educação:</strong><span class='warning textoLabels'> * </span><br>
					<input type='text' class='textInput' name='tutor_phone'><br>
					<strong class='textoLabels'>Endereço de e-mail do tutor: </strong><br>
					<input type='text' class='textInput' name='tutor_email' placeholder='email@example.com'><br><br>
					<input type='hidden' value='validar' name='estado'>
					<input type='submit' value='submeter' class='submitButton textoLabels'>
					</form></div>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}

?>