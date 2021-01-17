<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_records")) { //Verifica se o utilizador está autenticado e tem a capability "manage_records"
    $mySQL = ligacaoBD(); //Efetua a ligação com a base de dados
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) { //Se não for possível selecionar a base de dados "bitnami_wordpress" é apresentado o erro ocorrido
        die("Connection failed: " . mysqli_connect_error());
    } 
	else {
        if ($_REQUEST["estado"] == "validar") { //Validar dados recebidos
            $houveErros = false;
            $dataNoFormatoCorreto = true;
            echo "<div class='caixaSubTitulo'><h3>Dados de registo - validação</h3></div>";
            echo "<div class='caixaFormulario'>";
            $child_name = testarInput($_REQUEST["child_name"]);
            $birth_date = testarInput($_REQUEST["birth_date"]);
            $tutor_name = testarInput($_REQUEST["tutor_name"]);
            $tutor_phone = testarInput($_REQUEST["tutor_phone"]);
            $tutor_email = testarInput($_REQUEST["tutor_email"]);
            $dateList = explode("-", $birth_date); //Para dividir o dia, mês e ano da data de nascimento em variáveis separadas.
            //JUNTA OS NOMES DE TODOS OS CAMPOS EM FALTA PARA DEPOIS LISTA-LOS:
            $campos = "";

            if (empty($child_name)) {
                $campos .= "<li><strong>Nome completo</strong></li>";
                $houveErros = true;
            } 
			else if (1 === preg_match('~[0-9]~', $child_name) || 1 === preg_match('~[0-9]~', $tutor_name)) {//Se o nome da criança ou do tutor conter números é apresentada a mensagem de erro e a variável $houveErros é colocada a true
                $campos .= "<li><strong>Nomes não podem conter números!</strong></li>";
                $houveErros = true;
            }
            if (empty($birth_date)) {
                $campos .= "<li><strong>Data de nascimento</strong></li>";
                $houveErros = true;
            } 
			else if (!preg_match('/^\d{4}$/', $dateList[0]) || !preg_match('/^\d{2}$/', $dateList[1]) || !preg_match('/^\d{2}$/', $dateList[2]) || !checkdate($dateList[1], $dateList[2], $dateList[0])) {//Verifica-se se algum campo da data não tem caracteres não numéricos, e o comprimento do dia e mês é 2 caracteres e do ano 4 caracteres, e verifica a data usando a função checkdate( int $month , int $day , int $year):
                $campos .=  "<li><strong>Data tem que estar no formato AAAA-MM-DD e ser válida!</strong></li>";
                $houveErros = true;
                $dataNoFormatoCorreto = false; 
            }
			else if ($dataNoFormatoCorreto) { //Apenas se a data inserida estiver no formato correto e ser válida é que será verificada se a data inserida é maior que a data atual
                $data_atual = date("Y-m-d");
                if ($birth_date > $data_atual) {
                    $campos .= "<li><strong>A data inserida é maior que a data atual!</strong></li>";
                    $houveErros = true;
                }
            }
            if (empty($tutor_name)) {
                $campos .= "<li><strong>Nome completo do encarregado de educação</strong></li>";
                $houveErros = true;
            }
            if (empty($tutor_phone)) {
                $campos .= "<li><strong>Telefone do encarregado de educação</strong></li>";
                $houveErros = true;
            } 
			else if (!preg_match('/^\d{9}$/', $tutor_phone)) {//Se o nº de telefone não tem 9 caracteres numéricos é apresentada a mensagem de erro e a variável $houveErros é colocada a true
                $campos .=  "<li><strong>Número de telefone do tutor tem que ter 9 algarismos!</strong></li>";
                $houveErros = true;
            }
            if (!empty($tutor_email) && !filter_var($tutor_email, FILTER_VALIDATE_EMAIL)) {//Se o email do tutor for preenchido e o email inserido não é válido é apresentada a mensagem de erro e a variável $houveErros é colocada a true
                $campos .=  "<li><strong>Endereço de email inválido!</strong></li>";
                $houveErros = true;
            }

            if ($houveErros) { //Se algum campo do formulário não foi preenchido corretamente é apresentado um botão para voltar para a página anterior
                //LISTA OS NOMES DOS CAMPOS EM FALTA
                echo "<span class='warning'>Os seguintes campos são <strong>obrigatórios</strong> e/ou estão no <strong>formato errado</strong></span>:<ul>" . $campos . "</ul>";
                //BOTÃO PARA VOLTAR ATRÁS
                voltarAtras();
            } 
			else { // Se os campos do formulário foram todos preenchidos corretamente:
                echo "<strong><span class='information'>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão corretos e pretende submeter os mesmos?</span><br><br>";
                //Lista dos dados inseridos pelo utilizador: 
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
				</ol>";
                //Output de formulário com todos os campos preenchidos como inputs hidden (para poder aceder a esses valores na página seguinte):
                $action = get_site_url() . '/' . $current_page;
                echo "<form method='post' action='$action'><p hidden>
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
        } 
		elseif ($_REQUEST["estado"] == "inserir") { //Inserção dos dados na tabela "child"
            echo "<div class='caixaSubTitulo'><h3>Dados de registo - inserção</h3></div>";
            echo "<div class='caixaFormulario'>";
            $child_name = testarInput($_REQUEST['child_name']);
            $birth_date = testarInput($_REQUEST['birth_date']);
            $tutor_name = testarInput($_REQUEST['tutor_name']);
            $tutor_phone = testarInput($_REQUEST['tutor_phone']);
            $tutor_email = testarInput($_REQUEST['tutor_email']);
            $insertChildQuery = "INSERT INTO child (id,name,birth_date,tutor_name,tutor_phone,tutor_email) VALUES (NULL,'$child_name','$birth_date','$tutor_name','$tutor_phone', '$tutor_email');"; //Query para inserir os dados na tabela "child"
            if (!mysqli_query($mySQL, $insertChildQuery)) { //Se houver algum erro ao executar a query é apresentada a mensagem de erro
                echo "<span class='warning'>Erro: " . $insertChildQuery . "<br>" . mysqli_error($mySQL) . "</span>";
            } else { //Se não houver nenhum erro ao executar a query os dados são inseridos na tabela "child" e é apresentado um botão "Continuar" com uma ligação para esta mesma página
                echo "<span class='information'>Inseriu os dados de registo com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.</span><br><br>";
                echo "<a href='gestao-de-registos'><button class='continuarButton textoLabels'>Continuar</button></a>";
            }
            echo "</div>";

        } 
		else {//Estado inicial:
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_registos.js', array('jquery'), 1.1, true);
            }
            echo "<div class='caixaSubTitulo'><h3 >Dados de registo - introdução</h3></div>";
            //Apresentação do formulário para inserir os dados da criança:
            echo "<div class='caixaFormulario'>";
            echo "<span class='information'><strong>Introduza os dados pessoais básicos da criança:</strong></span><br>
			<span class='warning'>* Campos obrigatórios</span><br>";
            $action = get_site_url() . '/' . $current_page;
            echo "<form method='post' action='$action'>
				<strong class='textoLabels'> Nome completo:</strong><span class='warning textoLabels'> * </span><br>
				<input type='text' class='textInput' id='child_name' name='child_name'><br>
				<strong class='textoLabels'>Data de nascimento:<span class='warning textoLabels'> * </span><br>
				<input type='text' class='textInput' id='birth_date' name='birth_date' placeholder='AAAA-MM-DD'><br>
				<strong class='textoLabels'>Nome completo do encarregado de educação:</strong><span class='warning textoLabels'> * </span><br>
				<input type='text' class='textInput' id='tutor_name' name='tutor_name'><br>
				<strong class='textoLabels'>Telefone do encarregado de educação:</strong><span class='warning textoLabels'> * </span><br>
				<input type='text' class='textInput' id='tutor_phone' name='tutor_phone'><br>
				<strong class='textoLabels'>Endereço de e-mail do tutor: </strong><br>
				<input type='text' class='textInput' id='tutor_email' name='tutor_email' placeholder='email@example.com'><br><br>
				<p hidden><input type='hidden' value='validar' name='estado'></p>
				<input type='submit' value='submeter' class='submitButton textoLabels'>
				</form></div>";
        }
    }
} 
else { //Se o utilizador não está autenticado ou não tem a capability "manage_records" não pode aceder à página
    echo "<span class='warning'>Não tem autorização para aceder a esta página</span>";
}
?>