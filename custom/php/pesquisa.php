<?php
require_once("custom/php/common.php");
if (verificaCapability("search")) {
	$mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
		if ($_REQUEST["estado"] == "escolha") {

			$_SESSION["item_id"] = $_REQUEST["item"];
            $queryNomeItem = "SELECT name from item WHERE id=" . $_SESSION["item_id"];
            $tabelaNomeItem = mysqli_query($mySQL, $queryNomeItem);
            $nomeItem = mysqli_fetch_assoc($tabelaNomeItem);
            $_SESSION["item_name"] = $nomeItem["name"];
			
			echo "<form method='post'><table class='tabela'>";
			echo "<tr class='row'><th class='textoTabela cell'>Atributo</th><th class='textoTabela cell'>Obter</th><th class='textoTabela cell'>Filtro</th> </tr>";
			$atributos = array("id", "name", "birth_date", "tutor_name", "tutor_phone", "tutor_email"); 		
			for ($i = 0; $i < 6; $i++) {
				echo "<tr class='row'><td class='textoTabela cell'>" . $atributos[$i] . "</td>
				<td class='textoTabela cell'><input type='checkbox' name='atributos_obter[]' value=". $atributos[$i] ."></td>
				<td class='textoTabela cell'><input type='checkbox' name='atributos_filtro[]' value=" . $atributos[$i] . "></td>
				</tr>";
			}
			echo "</table><br><br>";

			echo "<table class='tabela'>";
			echo "<tr class='row'><th class='textoTabela cell'>Subitem</th><th class='textoTabela cell'>Obter</th><th class='textoTabela cell'>Filtro</th> </tr>";
			$querySubitens = "SELECT * FROM subitem WHERE item_id=" . $_SESSION["item_id"];
            $tabelaSubitens = mysqli_query($mySQL, $querySubitens);
            if (mysqli_num_rows($tabelaSubitens) > 0) {
				while ($rowSubitem = mysqli_fetch_assoc($tabelaSubitens)) {				
					echo "<tr class='row'><td class='textoTabela cell'>" . $rowSubitem["name"] . "</td>
					<td class='textoTabela cell'><input type='checkbox' name='subitens_obter[]' value=" . $rowSubitem["name"] . "></td>
					<td class='textoTabela cell'><input type='checkbox' name='subitens_filtro[]' value=" . $rowSubitem["name"] . "></td>
					</tr>";
				}
			}
			echo "</table>
			<input type='submit' value='Escolher' class='submitButton textoLabels'>
			<input type='hidden' value='escolher_filtros' name='estado'>
			</form>";

		}
		elseif ($_REQUEST["estado"] == "escolher_filtros") {
			
			$aux = 0;
			echo "<div class='caixaFormulario'><form method='post'>
			<span class='information'><strong>Irá ser realizada uma pesquisa que irá obter, como resultado, uma listagem de, para cada criança, dos seguintes dados pessoais escolhidos:</strong></span>
			<form method='post'><table class='tabela'><ul>";
			foreach($_REQUEST['atributos_obter'] as $chave=>$valor){
				echo "<tr><td><li>$valor</li></td>";	
				
				echo '<td><select name="oper">
				<option value="selecione_tipo_op">Selecione um dos operadores:</option>
				<option value="=">=</option>
				<option value="!=">!=</option>
				<option value="like">LIKE</option>
                </select></td>';
				
				echo "<td><input type='text' class='textInput' id=".$valor." name=".$valor."></td></tr>";
				$aux++;
				$_SESSION["atributos_obter" . $aux] = $valor;  
			}
			echo "</ul></table>
			<span class='information'><strong>e do item: * " . $_SESSION["item_name"] . " * uma listagem dos valores dos subitens:</strong></span>
			<ul>";
			$aux = 0;
			foreach($_REQUEST['subitens_obter'] as $chave=>$valor){
				echo "<li>$valor</li>";	
				$aux++;
				$_SESSION["subitens_obter" . $aux] = $valor;  
			}
			echo "</ul></form>";
		}
		else{
			
			echo "<div class='caixaSubTitulo'><h3>Pesquisa - escolher item</h3></div>";
			echo "<div class='caixaFormulario'>";
			echo "<ul>";
            $queryTipoItem = "SELECT name,id FROM item_type ORDER BY id";
            $tabelaTipoItem = mysqli_query($mySQL, $queryTipoItem);
            while ($tipoItem = mysqli_fetch_assoc($tabelaTipoItem)) {
                echo "<li>" . $tipoItem["name"] . "</li><ul>";
                $queryItem = "SELECT name,id FROM item WHERE item_type_id=" . $tipoItem["id"];
                $tabelaItem = mysqli_query($mySQL, $queryItem);
                while ($item = mysqli_fetch_assoc($tabelaItem)) {
                    echo "<li><a href='pesquisa?estado=escolha&item=" . $item["id"] . "'>[" . $item["name"] . "]</a></li>";
                }
                echo "</ul>";
            }
            echo "</ul>";
            echo "</div>";
			
		}	
	}		
}
else{
	echo "Não tem autorização para aceder a esta página";
}
?>