<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_subitems")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
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

            if (empty($nome_subitem) || empty($tipo_valor) || ($nome_item == "selecione_um_item") || empty($tipo_camp_form) || $ordem_campo_form == "" || empty($obrigatorio)) {
                echo "<span class='warning textoLabels'>Não preencheu todos os campos obrigatórios!<br></span>";
                $houveErros = True;
            }
            if (1 === preg_match('~[0-9]~', $nome_subitem)) {
                echo "<span class='warning textoLabels'>O nome do subitem não pode conter números!<br></span>";
                $houveErros = True;
            }
            if (!is_numeric($ordem_campo_form) || $ordem_campo_form <= 0) {
                echo "<span class='warning textoLabels'>A ordem do campo no formulário tem que ser um número superior a 0!<br></span>";
                $houveErros = True;
            }
            if ($houveErros) {
                voltarAtras();
            } else {
				
				$nome_item = str_replace("_", " ", $nome_item);
				$tipo_unidade = str_replace("_", " ", $tipo_unidade);
				
				// ******* Nome do campo no formulário ********   
                //id do subitem 
                $querySubitemMaxId = "SELECT name FROM subitem";
                $tabelaSubitemMaxId = mysqli_query($mySQL, $querySubitemMaxId);
                $maxId = mysqli_num_rows($tabelaSubitemMaxId);
                $newId = $maxId + 1;
				//------
                $tirarAcento = Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', Transliterator::FORWARD);
                $itemSemAcento = $tirarAcento->transliterate($nome_item);
                $tresPrimeirasLetrasItem = substr($itemSemAcento, 0, 3);
                $subitem_ascii = preg_replace('/[^a-z0-9_ ]/i', '', $nome_subitem);
                $subitemSemCaracteresVazios = str_replace(" ", "_", $subitem_ascii);
                $nome_campo_form = $tresPrimeirasLetrasItem . "-" . $newId . "-" . $subitemSemCaracteresVazios;
                //echo "<span class='information'>O nome do campo no formulário será: </span>$nome_campo_form<br>";
				
				//************************************************
								
                $queryItem = "SELECT id FROM item WHERE name='$nome_item'";
                $result = mysqli_query($mySQL, $queryItem);
                $item = mysqli_fetch_assoc($result);
                $queryItem = "SELECT id FROM subitem_unit_type WHERE name='$tipo_unidade'";
                $result = mysqli_query($mySQL, $queryItem);
                $unidade = mysqli_fetch_assoc($result);
				
                $insertQuery = "INSERT INTO subitem (id, name, item_id, value_type, form_field_name, form_field_type, unit_type_id, form_field_order, mandatory, state) VALUES (NULL,'$nome_subitem'," . $item["id"] . ",'$tipo_valor','$nome_campo_form','$tipo_camp_form'," . ($unidade==null?'NULL':$unidade["id"]) . ",$ordem_campo_form," . ($obrigatorio == 'sim' ? 1 : 0) . ",'active');";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: $insertQuery<br>mysqli_error($mySQL)</span>";
                } else {
                    echo "<span class='information'>Inseriu os dados de novo subitem com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.</span><br>";
                    echo "<a href='gestao-de-subitens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }
                echo "</div>";
            }
        } else {
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_subitens.js', array('jquery'), 1.1, true);
            }
            if (mysqli_num_rows(mysqli_query($mySQL, "SELECT * FROM subitem")) > 0) {
                $queryItem = "SELECT * FROM item ORDER BY name";
                $tabelaItens = mysqli_query($mySQL, $queryItem);
                if (mysqli_num_rows($tabelaItens) > 0) {
                    echo "<table class='tabela'>";
                    echo "<tr class='row'><th class='textoTabela cell'>item</th><th class='textoTabela cell'>id</th><th class='textoTabela cell'>subitem</th><th class='textoTabela cell'>tipo de valor</th><th class='textoTabela cell'>nome do campo no formulário</th><th class='textoTabela cell'>tipo do campo no formulário</th><th class='textoTabela cell'>tipo de unidade</th><th class='textoTabela cell'>ordem do campo no formulário</th><th class='textoTabela cell'>obrigatório</th><th class='textoTabela cell'>estado</th><th class='textoTabela cell'>ação</th></tr>";
                    while ($rowItem = mysqli_fetch_assoc($tabelaItens)) {
                        $querySubitens = "SELECT * FROM subitem WHERE item_id = " . $rowItem["id"] . " ORDER BY name";
                        $tabelaSubitens = mysqli_query($mySQL, $querySubitens);
                        if (mysqli_num_rows($tabelaSubitens) > 0) {
                            $newSubitem = true;
                            $numeroSubitens = mysqli_num_rows($tabelaSubitens);
                            while ($rowSubitem = mysqli_fetch_assoc($tabelaSubitens)) {
                                if ($newSubitem) {
                                    echo "<tr class='row'><td class='textoTabela cell' rowspan='$numeroSubitens'>" . $rowItem["name"] . "</td>";
                                    $newSubitem = false;
                                } else {
                                    echo "<tr class='row'>";
                                }
                                $queryUnidade = "SELECT name FROM subitem_unit_type WHERE id = " . $rowSubitem["unit_type_id"] . "   ";
                                $tabelaUnidade = mysqli_query($mySQL, $queryUnidade);
                                $rowUnidade = mysqli_fetch_assoc($tabelaUnidade);
                                echo "<td  class='textoTabela cell'>" . $rowSubitem["id"] . "</td><td class='textoTabela cell'>" . $rowSubitem["name"] . "<td  class='textoTabela cell'>" . $rowSubitem["value_type"] . "<td  class='textoTabela cell'>" . $rowSubitem["form_field_name"] . "<td  class='textoTabela cell'>" . $rowSubitem["form_field_type"] . "<td  class='textoTabela cell'>" . $rowUnidade["name"] . "<td  class='textoTabela cell'>" . $rowSubitem["form_field_order"] . "<td  class='textoTabela cell'>" . ($rowSubitem["mandatory"] == '1' ? 'sim' : 'não') . "</td><td class='textoTabela cell'>" . ($rowSubitem["state"] == 'active' ? 'ativo' : 'inativo') . "</td><td class='textoTabela cell'>[editar] [desativar]</td>";
                                echo "</tr>";
                            }
                        }
                    }
                    echo "</table>";
                }
            } else {
                echo "<span class='information'>Não há subitems especificados.</span>";
            }
            $tipo_valores = get_enum_values("subitem", "value_type");
            $tipo_camp_form = get_enum_values("subitem", "form_field_type");
            $queryItens = "SELECT name FROM item";
            $tabelaItens2 = mysqli_query($mySQL, $queryItens);
            $queryTiposUnid = "SELECT name FROM subitem_unit_type";
            $tabelaTiposUnid = mysqli_query($mySQL, $queryTiposUnid);

            echo "<div class='caixaSubTitulo'><h3><strong>Gestão de subitens - introdução</strong></h3></div>
            <div class='caixaFormulario'><form method='post'>
			<span class='warning'>* Campos obrigatórios</span><br><br>
			<strong>Nome do subitem: </strong><span class='warning textoLabels'> * </span><br><input type='text' class='textInput' id='nome_subitem' name='nome_subitem' ><br><br>
			<br><strong>Tipo de valor: </strong><span class='warning textoLabels'> * </span></br>";
            $primeiro = true;
            foreach ($tipo_valores as $val_tip) {
                $input = "<input";
                if ($primeiro) {
                    $input .= " checked";
                    $primeiro = false;
                }
                $input .= " type='radio' name='tipo_valor' value=$val_tip><span class='textoLabels'>$val_tip</span><br>";
                echo $input;
            }
            echo "<br><strong>Item: </strong><span class='warning textoLabels'> * </span></br>";
            if (mysqli_num_rows($tabelaItens2) > 0) {
                echo '<select name="it" id="item"  class="textInput textoLabels">
				<option value="selecione_um_item">Selecione um item:</option>';
                while ($linhaItem = mysqli_fetch_assoc(($tabelaItens2))) {
					$linha = $linhaItem["name"];
					$option = str_replace(" ", "_", $linha);
                    echo '<option value= ' . $option . ' >' . $linhaItem["name"] . '</option>';
                }
                echo '</select><br>';
            } else {
                echo "Não há nenhum item.<br>";
            }
            echo "<br><strong>Tipo do campo do formulário: </strong><span class='warning textoLabels'> * </span></br>";
            $primeiro = true;
            foreach ($tipo_camp_form as $camp_form_tip) {
                $input = "<input";
                if ($primeiro) {
                    $input .= " checked";
                    $primeiro = false;
                }
                $input .= " type='radio' name='tipo_camp_form' value=$camp_form_tip><span class='textoLabels'>$camp_form_tip</span><br>";
                echo $input;
//				echo '<input  type="radio" name="tipo_camp_form" value=' . $camp_form_tip . '><span class="textoLabels" >' . $camp_form_tip . '</span><br>';
            }
            echo "<br><strong>Tipo de unidade: </strong></br>";
            if (mysqli_num_rows($tabelaTiposUnid) > 0) {
                echo '<select name="tipo_unidade" id="tipo_unidade"  class="textInput textoLabels">
				<option value="selecione_tipo_unid">Selecione um tipo de unidade:</option>';
                while ($linhaUnid = mysqli_fetch_assoc(($tabelaTiposUnid))) {
					$linha = $linhaUnid["name"];
					$option = str_replace(" ", "_", $linha);
                    echo '<option value=' . $option . '>' . $linhaUnid["name"] . '</option>';
                }
                echo '</select><br>';
            } else {
                echo "<span class='information'>Não há nenhum tipo de unidade.</span><br>";
            }
            echo "<br><strong>Ordem do campo no formulário: </strong><span class='warning textoLabels'> * </span><br><input type='text' class='textInput' id='ordem_campo_form' name='ordem_campo_form' ><br><br>
			<br><strong>Obrigatório: </strong><span class='warning textoLabels'> * </span><br>
			<input  type='radio' name='obrigatorio' checked value=sim><span class='textoLabels' >Sim</span><br>
			<input  type='radio' name='obrigatorio' value=nao><span class='textoLabels' >Não</span><br>
			<input type='hidden' value='inserir' name='estado'>
			<input type='submit' value='Inserir subitem' class='submitButton textoLabels'>
			</form></div>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>
