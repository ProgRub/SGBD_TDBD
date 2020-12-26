<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_subitems")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    } 
	else {
		if ($_POST["estado"] == "inserir") {
			$houveErros = False;
			echo "<div class='caixaSubTitulo'><h3><strong>Gestão de subitens - inserção</strong></h3></div>"; 
			$nome_subitem = testarInput($_POST["nome_subitem"]);
			$tipo_valor = testarInput($_POST["tipo_valor"]);
			$it = testarInput($_POST["it"]);
			$tipo_camp_form = testarInput($_POST["tipo_camp_form"]);
			$tipo_unidade = testarInput($_POST["tipo_unidade"]);
			$ordem_campo_form = testarInput($_POST["ordem_campo_form"]);
			$obrigatorio = testarInput($_POST["obrigatorio"]);
			
			if (empty($nome_subitem) || empty($tipo_valor) || ($it=="selecione_um_item") || empty($tipo_camp_form)|| $ordem_campo_form == "" || empty($obrigatorio)) {
                echo "<p class='warning textoLabels'>Não preencheu todos os campos obrigatórios!</p>";
                $houveErros = True;
            }
			if (1 === preg_match('~[0-9]~', $nome_subitem)) {
                echo "<p class='warning textoLabels'>O nome do subitem não pode conter números!</p>";
                $houveErros = True;
			}
			if (!is_numeric($ordem_campo_form) || $ordem_campo_form <= 0) {
				echo "<p class='warning textoLabels'>A ordem do campo no formulário tem que ser um número superior a 0!</p>";
				$houveErros = True;
			}
			if ($houveErros) {
                voltarAtras();
            } else {
				// ******* Nome do campo no formulário ********
				// (Alterar depois maneira de conseguir o id do subitem)
				$querySubitemMaxId = "SELECT name FROM subitem";				
                $tabelaSubitemMaxId = mysqli_query($mySQL, $querySubitemMaxId);	
				$maxId = mysqli_num_rows($tabelaSubitemMaxId);	
				$newId = $maxId + 1;
				$tirarAcento = Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', Transliterator::FORWARD);
				$itemSemAcento = $tirarAcento->transliterate($it);
				$tresPrimeirasLetrasItem = substr($itemSemAcento, 0, 3);
				$subitem_ascii = preg_replace('/[^a-z0-9_ ]/i', '', $nome_subitem);
				$subitemSemCaracteresVazios = str_replace(" ", "_", $subitem_ascii);
				$nome_campo_form = $tresPrimeirasLetrasItem . "-" . $newId . "-" . $subitemSemCaracteresVazios;
				echo "<strong>O nome do campo no formulário será: </strong>" . $nome_campo_form;

				//-----------------------------------
 				echo "\n\n*** FALTA FAZER INSERÇÃO DOS DADOS NA TABELA SUBITEM ***";
				// ***
			}	
		}
		else{
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
								$queryUnidade = "SELECT name FROM subitem_unit_type WHERE id = " . $rowSubitem["unit_type_id"] ."   ";		
								$tabelaUnidade = mysqli_query($mySQL, $queryUnidade);			
								$rowUnidade = mysqli_fetch_assoc($tabelaUnidade);								
                                echo "<td  class='textoTabela cell'>" . $rowSubitem["id"] . "</td><td class='textoTabela cell'>" . $rowSubitem["name"] . "<td  class='textoTabela cell'>" . $rowSubitem["value_type"] . "<td  class='textoTabela cell'>" . $rowSubitem["form_field_name"] . "<td  class='textoTabela cell'>" . $rowSubitem["form_field_type"] . "<td  class='textoTabela cell'>" . $rowUnidade["name"] . "<td  class='textoTabela cell'>" . $rowSubitem["form_field_order"] . "<td  class='textoTabela cell'>" . ($rowSubitem["mandatory"] == '1' ? 'sim' : 'não') . "</td><td class='textoTabela cell'>" . ($rowSubitem["state"] == 'active' ? 'ativo' : 'inativo') . "</td><td class='textoTabela cell'>[editar] [desativar]</td>"; 
                                echo "</tr>";								
                            }
                        }
                    }
                    echo "</table>";
                }
            } 						
			else {
                echo "Não há subitems especificados.";
            }
			$tipo_valores = get_enum_values("subitem", "value_type");
			$tipo_camp_form = get_enum_values("subitem", "form_field_type");
			$queryItens = "SELECT name FROM item"; 
            $tabelaItens2 = mysqli_query($mySQL, $queryItens);
			$queryTiposUnid = "SELECT name FROM subitem_unit_type"; 
            $tabelaTiposUnid = mysqli_query($mySQL, $queryTiposUnid);
			
			echo "<div class='caixaSubTitulo'><h3><strong>Gestão de subitens - introdução</strong></h3></div>
            <div class='caixaFormulario'><body><form method='post' >
			<p class='warning'>* Campos obrigatórios</p><br>
			<strong>Nome do subitem: </strong><span class='warning textoLabels'> * </span><br><input type='text' name='nome_subitem' ><br><br>
			<br><strong>Tipo de valor: </strong><span class='warning textoLabels'> * </span></br>";
			foreach($tipo_valores as $val_tip)
			{
				echo '<input  type="radio" name="tipo_valor" value=' . $val_tip . '><span class="textoLabels" >' . $val_tip . '</span><br>';
			}
			echo "<br><strong>Item: </strong><span class='warning textoLabels'> * </span></br>";
            if (mysqli_num_rows($tabelaItens2) > 0) {
				echo '<select name="it" required>
				<option value="selecione_um_item">Selecione um item:</option>';
                while ($linhaItem = mysqli_fetch_assoc(($tabelaItens2))) {
					echo '<option value=' . $linhaItem["name"] . '>' . $linhaItem["name"] . '</option>';
                }
				echo '</select><br>';
            } else {
                echo "Não há nenhum item.<br>";
            }
			echo "<br><strong>Tipo do campo do formulário: </strong><span class='warning textoLabels'> * </span></br>";
			foreach($tipo_camp_form as $camp_form_tip)
			{
				echo '<input  type="radio" name="tipo_camp_form" value=' . $camp_form_tip . '><span class="textoLabels" >' . $camp_form_tip . '</span><br>';
			}
			echo "<br><strong>Tipo de unidade: </strong></br>";
            if (mysqli_num_rows($tabelaTiposUnid) > 0) {
				echo '<select name="tipo_unidade">
				<option value="selecione_tipo_unid">Selecione um tipo de unidade:</option>';
                while ($linhaUnid = mysqli_fetch_assoc(($tabelaTiposUnid))) {
					echo '<option value=' . $linhaUnid["name"] . '>' . $linhaUnid["name"] . '</option>';
                }
				echo '</select><br>';
            } else {
                echo "Não há nenhum tipo de unidade.<br>";
            }
			echo "<br><strong>Ordem do campo no formulário: </strong><span class='warning textoLabels'> * </span><br><input type='text' name='ordem_campo_form' ><br><br>
			<br><strong>Obrigatório: </strong><span class='warning textoLabels'> * </span><br>
			<input  type='radio' name='obrigatorio' value=sim><span class='textoLabels' >Sim</span><br>
			<input  type='radio' name='obrigatorio' value=nao><span class='textoLabels' >Não</span><br>
			<input type='hidden' value='inserir' name='estado'>
			<input type='submit' value='Inserir subitem' class='submitButton textoLabels'>
			</form></body>";
		}   
	}
} 
else {
    echo "Não tem autorização para aceder a esta página";
}
?>
