<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_subitems")) { //Verifica se o utilizador está autenticado e tem a capability "manage_subitems"
    $mySQL = ligacaoBD(); //Efetua a ligação com a base de dados
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) { //Se não for possível selecionar a base de dados "bitnami_wordpress" é apresentado o erro ocorrido
        die("Connection failed: " . mysqli_connect_error());
    }
	else {
        if ($_REQUEST["estado"] == "inserir") {
            $houveErros = false;
            echo "<div class='caixaSubTitulo'><h3><strong>Gestão de subitens - inserção</strong></h3></div>";
            echo "<div class='caixaFormulario'>";
            $nome_subitem = testarInput($_REQUEST["nome_subitem"]);
            $tipo_valor = testarInput($_REQUEST["tipo_valor"]);
            $nome_item = testarInput($_REQUEST["it"]);
            $tipo_camp_form = testarInput($_REQUEST["tipo_camp_form"]);
            $tipo_unidade = testarInput($_REQUEST["tipo_unidade"]);
            $ordem_campo_form = testarInput($_REQUEST["ordem_campo_form"]);
            $obrigatorio = testarInput($_REQUEST["obrigatorio"]);

			//Validação de todos campos do formulário:
            if (empty($nome_subitem) || empty($tipo_valor) || ($nome_item == "selecione_um_item") || empty($tipo_camp_form) || $ordem_campo_form == "" || empty($obrigatorio)) { //Se algum dos campos obrigatórios estiver vazio é apresentada a mensagem de erro e a variável $houveErros é colocada a True
                echo "<p class='warning textoLabels'>Não preencheu todos os campos obrigatórios!</p>";
                $houveErros = True;
            }
            if (1 === preg_match('~[0-9]~', $nome_subitem)) {//Se o nome do subitem conter números é apresentada a mensagem de erro e a variável $houveErros é colocada a True
                echo "<p class='warning textoLabels'>O nome do subitem não pode conter números!</p>";
                $houveErros = True;
            }
            if (!is_numeric($ordem_campo_form) || $ordem_campo_form <= 0) { //Se a ordem do campo do formulário não for um número ou for um número inferior ou igual a 0, é apresentada a mensagem de erro e a variável $houveErros é colocada a True
                echo "<p class='warning textoLabels'>A ordem do campo no formulário tem que ser um número superior a 0!</p>";
                $houveErros = True;
            }
            if ($houveErros) { //Se algum campo do formulário não foi preenchido corretamente é apresentado um botão para voltar para a página anterior
                voltarAtras();
            }
			else {// Se os campos do formulário foram todos preenchidos corretamente:

				$nome_item = str_replace("_", " ", $nome_item); //Voltar a colocar o nome do item com espaços (em vez de underscores)
				$tipo_unidade = str_replace("_", " ", $tipo_unidade); //Voltar a colocar o tipo de unidade com espaços (em vez de underscores)

                $queryItem = "SELECT id FROM item WHERE name='$nome_item'"; //Query para descobrir o id do item selecionado
                $result = mysqli_query($mySQL, $queryItem);
                $item = mysqli_fetch_assoc($result);
                $queryItem = "SELECT id FROM subitem_unit_type WHERE name='$tipo_unidade'"; //Query para descobrir o id do tipo de unidade selecionado
                $result = mysqli_query($mySQL, $queryItem);
                $unidade = mysqli_fetch_assoc($result);

                $query = "START TRANSACTION;\n";
                $ocorreuErro = false;
                if (!mysqli_query($mySQL, $query)) {
                    echo "<span class='warning'>Erro: " . $query . "<br>" . mysqli_error($mySQL) . "</span>";
                    $ocorreuErro = true;
                }
                $insertQuery = "INSERT INTO subitem (id, name, item_id, value_type, form_field_name, form_field_type, unit_type_id, form_field_order, mandatory, state) VALUES (NULL,'$nome_subitem'," . $item["id"] . ",'$tipo_valor','','$tipo_camp_form'," . ($unidade==null?'NULL':$unidade["id"]) . ",$ordem_campo_form," . ($obrigatorio == 'sim' ? 1 : 0) . ",'active');";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: $insertQuery<br>mysqli_error($mySQL)</span>";
                    $ocorreuErro = true;
                }
                if (!$ocorreuErro) {
                    $idItem=mysqli_insert_id($mySQL);
                    $tirarAcento = Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', Transliterator::FORWARD);
                    $itemSemAcento = $tirarAcento->transliterate($nome_item);
                    $tresPrimeirasLetrasItem = substr($itemSemAcento, 0, 3);
                    $subitem_ascii = preg_replace('/[^a-z0-9_ ]/i', '', $nome_subitem);
                    $subitemSemCaracteresVazios = str_replace(" ", "_", $subitem_ascii);
                    $nome_campo_form = $tresPrimeirasLetrasItem . "-" . $idItem . "-" . $subitemSemCaracteresVazios;
                    $updateQuery="UPDATE subitem SET form_field_name='$nome_campo_form' WHERE id=$idItem";
                    if (!mysqli_query($mySQL, $updateQuery)) {
                        echo "<span class='warning'>Erro: $updateQuery<br>mysqli_error($mySQL)</span>";
                        $ocorreuErro = true;
                    }
                }
                if (!$ocorreuErro) {
                    $query = "COMMIT;";
                    if (!mysqli_query($mySQL, $query)) {
                        $ocorreuErro = true;
                    }
                } else {
                    $query = "ROLLBACK;";
                    if (!mysqli_query($mySQL, $query)) {
                        $ocorreuErro = true;
                    }
                }
				if(!$ocorreuErro) {//Se não houver nenhum erro ao executar a query os dados são inseridos na tabela "subitem" e é apresentado um botão "Continuar" com uma ligação para esta mesma página
                    echo "<span class='information'>Inseriu os dados de novo subitem com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.</span><br>";
                    echo "<a href='gestao-de-subitens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }else{
				    voltarAtras();
                }
                echo "</div>";
            }
        }
		else { //Estado inicial:
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_subitens.js', array('jquery'), 1.1, true);
            }
            if (mysqli_num_rows(mysqli_query($mySQL, "SELECT * FROM subitem")) > 0) { //Se existirem tuplos na tabela "subitem"
                $queryItem = "SELECT * FROM item ORDER BY name"; //Query para obter todos os itens ordenados alfabeticamente
                $tabelaItens = mysqli_query($mySQL, $queryItem); //Tabela com todos os itens ordenados alfabeticamente
                if (mysqli_num_rows($tabelaItens) > 0) { //Se existirem tuplos na tabela com os itens
					//--------------------------------------------------------------------------------------------------------------
					//                                    TABELA COM TODOS OS SUBITENS
					//--------------------------------------------------------------------------------------------------------------
                    echo "<table class='tabela'>";
					//Cabeçalho da tabela:
                    echo "<tr class='row'>
					<th class='textoTabela cell'>item</th>
					<th class='textoTabela cell'>id</th><th class='textoTabela cell'>subitem</th>
					<th class='textoTabela cell'>tipo de valor</th>
					<th class='textoTabela cell'>nome do campo no formulário</th>
					<th class='textoTabela cell'>tipo do campo no formulário</th>
					<th class='textoTabela cell'>tipo de unidade</th>
					<th class='textoTabela cell'>ordem do campo no formulário</th>
					<th class='textoTabela cell'>obrigatório</th>
					<th class='textoTabela cell'>estado</th>
					<th class='textoTabela cell'>ação</th>
					</tr>";
					//-----
                    while ($rowItem = mysqli_fetch_assoc($tabelaItens)) { //Enquanto existirem tuplos na tabela com os itens
                        $querySubitens = "SELECT * FROM subitem WHERE item_id = " . $rowItem["id"] . " ORDER BY name"; //Query para encontrar todos os subitens associados ao item da linha atual
                        $tabelaSubitens = mysqli_query($mySQL, $querySubitens); //Tabela com todos os subitens associados ao item da linha atual
						if (mysqli_num_rows($tabelaSubitens) > 0) { //Se existirem subitens associados ao item da linha atual
                            $newSubitem = true;
                            $numeroSubitens = mysqli_num_rows($tabelaSubitens); //Número de subitens associados ao item da linha atual

                            while ($rowSubitem = mysqli_fetch_assoc($tabelaSubitens)) { //Enquanto existirem tuplos na tabela dos subitens associados ao item da linha atual
                                if ($newSubitem) { //Para o primeiro subitem associado ao item da linha atual:
									//Rowspan para definir o número de linhas que a célula "item" deve abranger (para incluir todos os subitens associados)
									//E escrever o nome do item nessa célula/linha
                                    echo "<tr class='row'>
									<td class='textoTabela cell' rowspan='$numeroSubitens'>" . $rowItem["name"] . "</td>";
                                    $newSubitem = false;
                                }
								else {
									//Caso haja mais do que um subitem associado ao item, o nome do item já não é necessário escrever nessas linhas
                                    echo "<tr class='row'>";
                                }
                                $queryUnidade = "SELECT name FROM subitem_unit_type WHERE id = " . $rowSubitem["unit_type_id"] . ""; //Query para descobrir qual é o nome do tipo de unidade com o mesmo id do atributo "unit_type_id" da tabela "subitem"
                                $tabelaUnidade = mysqli_query($mySQL, $queryUnidade); //Tabela com o nome do tipo de unidade com o mesmo id do atributo "unit_type_id" da tabela "subitem"
                                $rowUnidade = mysqli_fetch_assoc($tabelaUnidade); //Resultado da query
								//Escrita dos valores das restantes colunas da tabela:
                                echo "<td  class='textoTabela cell'>" . $rowSubitem["id"] . "</td>
                                <td class='textoTabela cell'>" . $rowSubitem["name"] . "</td>
                                <td class='textoTabela cell'>" . $rowSubitem["value_type"] . "</td>
                                <td class='textoTabela cell'>" . $rowSubitem["form_field_name"] . "</td>
                                <td class='textoTabela cell'>" . $rowSubitem["form_field_type"] . "</td>
                                <td class='textoTabela cell'>" . $rowUnidade["name"] . "</td>
                                <td class='textoTabela cell'>" . $rowSubitem["form_field_order"] . "</td>
                                <td class='textoTabela cell'>" . ($rowSubitem["mandatory"] == '1' ? 'sim' : 'não') . "</td>
                                <td class='textoTabela cell'>" . ($rowSubitem["state"] == 'active' ? 'ativo' : 'inativo') . "</td>
                                <td class='textoTabela cell'>
                                <a href='edicao-de-dados?estado=editar&id=".$rowSubitem["id"]."&tipo=subitem'>[editar] </a><a href='edicao-de-dados?estado=".($rowSubitem["state"] == 'active' ? 'desativar' : 'ativar')."&id=".$rowSubitem["id"]."&tipo=subitem'>".($rowSubitem["state"] == 'active' ? '[desativar]' : '[ativar]')."</a></td>";
                            }
                        }
						//Se não existirem subitens associados ao item da linha atual, passar para o próximo item
                    }
                    echo "</table>";
					//--------------------------------------------------------------------------------------------------------------
                }
            }
			else { //Se não houverem tuplos na tabela subitem
                echo "<span class='information'>Não há subitems especificados.</span>";
            }


            $tipo_valores = get_enum_values("subitem", "value_type"); //Para obter todos os tipos de valores presentes no atributo value_type
            $tipo_camp_form = get_enum_values("subitem", "form_field_type"); //Para obter todos os tipos de campo de formulário presentes no atributo form_field_type
            $queryItens = "SELECT name FROM item"; //Para obter o nome de todos os itens 
            $tabelaItens2 = mysqli_query($mySQL, $queryItens); //Tabela com o nome de todos os itens 
            $queryTiposUnid = "SELECT name FROM subitem_unit_type"; //Para obter o nome de todos os tipos de unidades
            $tabelaTiposUnid = mysqli_query($mySQL, $queryTiposUnid); //Tabela com o nome de todos os tipos de unidades

			//Formulário para inserir os dados do subitem:
            echo "<div class='caixaSubTitulo'><h3><strong>Gestão de subitens - introdução</strong></h3></div>
            <div class='caixaFormulario'>
			<form method='post'>
			<span class='warning'>* Campos obrigatórios</span><br><br>
			<strong>Nome do subitem: </strong><span class='warning textoLabels'> * </span><br>
			<input type='text' class='textInput' id='nome_subitem' name='nome_subitem' ><br><br>
			<br><strong>Tipo de valor: </strong><span class='warning textoLabels'> * </span></br>";
            $primeiro = true;
            foreach ($tipo_valores as $val_tip) { //Escrever cada tipo de valor em um "radio" diferente
                $input = "<input";
                if ($primeiro) {
                    $input .= " checked"; //Para o primeiro tipo de valor ficar selecionado
                    $primeiro = false;
                }
                $input .= " type='radio' name='tipo_valor' value=$val_tip><span class='textoLabels'>$val_tip</span><br>";
                echo $input;
            }

            echo "<br><strong>Item: </strong><span class='warning textoLabels'> * </span></br>";
            if (mysqli_num_rows($tabelaItens2) > 0) { //Se existirem itens, apresenta uma selectbox com todos os itens para o utilizador escolher um
                echo '<select name="it" id="item"  class="textInput textoLabels">
				<option value="selecione_um_item">Selecione um item:</option>';
                while ($linhaItem = mysqli_fetch_assoc(($tabelaItens2))) { //Enquanto existirem itens
					$linha = $linhaItem["name"]; //Nome do item
					$option = str_replace(" ", "_", $linha); //Se o nome do item conter espaços, esse espaço é substituído por um underscore
					//Esta substituição é feita porque o valor da opção não pode conter espaços (caso contrário, só é passado como valor a 1º palavra do nome do item)
                    echo '<option value= ' . $option . ' >' . $linhaItem["name"] . '</option>';
                }
                echo '</select><br>';
            }
			else { //Caso não existam itens
                echo "Não há nenhum item.<br>";
            }
            echo "<br><strong>Tipo do campo do formulário: </strong><span class='warning textoLabels'> * </span></br>";
            $primeiro = true;
            foreach ($tipo_camp_form as $camp_form_tip) { //Escrever cada tipo de campo de formulário em um "radio" diferente
                $input = "<input";
                if ($primeiro) {
                    $input .= " checked"; //Para o primeiro tipo de campo de formulário ficar selecionado
                    $primeiro = false;
                }
                $input .= " type='radio' name='tipo_camp_form' value=$camp_form_tip><span class='textoLabels'>$camp_form_tip</span><br>";
                echo $input;
            }
            echo "<br><strong>Tipo de unidade: </strong></br>";
            if (mysqli_num_rows($tabelaTiposUnid) > 0) { //Se existirem tipos de unidades, apresenta uma selectbox com todos os tipos de unidades para o utilizador escolher um
                echo '<select name="tipo_unidade" id="tipo_unidade"  class="textInput textoLabels">
				<option value="selecione_tipo_unid">Selecione um tipo de unidade:</option>';
                while ($linhaUnid = mysqli_fetch_assoc(($tabelaTiposUnid))) {
					$linha = $linhaUnid["name"]; //Nome do tipo de unidade
					$option = str_replace(" ", "_", $linha);//Se o nome do tipo de unidade conter espaços, esse espaço é substituído por um underscore
                    echo '<option value=' . $option . '>' . $linhaUnid["name"] . '</option>';
                }
                echo '</select><br>';
            }
			else { //Caso não haja tipos de unidades
                echo "<span class='information'>Não há nenhum tipo de unidade.</span><br>";
            }
            echo "<br>
			<strong>Ordem do campo no formulário: </strong><span class='warning textoLabels'> * </span><br>
			<input type='text' class='textInput' id='ordem_campo_form' name='ordem_campo_form' ><br><br>
			
			<br><strong>Obrigatório: </strong><span class='warning textoLabels'> * </span><br>
			<input  type='radio' name='obrigatorio' checked value=sim><span class='textoLabels' >Sim</span><br>
			<input  type='radio' name='obrigatorio' value=nao><span class='textoLabels' >Não</span><br>
			
			<p hidden><input type='hidden' value='inserir' name='estado'><p>
			<input type='submit' value='Inserir subitem' class='submitButton textoLabels'>
			</form></div>";
        }
    }
}
else { //Se o utilizador não está autenticado ou não tem a capability "manage_subitems" não pode aceder à página
    echo "Não tem autorização para aceder a esta página";
}
?>
