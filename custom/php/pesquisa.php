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
					echo "<tr class='row'><td class='textoTabela cell'>" . $rowSubitem["name"] . "</td>";
					$linha = $rowSubitem["name"];
					$option = str_replace(" ", "_", $linha);
					echo "<td class='textoTabela cell'><input type='checkbox' name='subitens_obter[]' value=" . $option . "></td>
					<td class='textoTabela cell'><input type='checkbox' name='subitens_filtro[]' value=" . $option . "></td>
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
				$aplicarFiltro = false;
				echo "<tr class='row'><td class='textoTabela cell'><li>$valor</li></td>";
				foreach($_REQUEST['atributos_filtro'] as $chave1=>$valor1){
					if($valor1 == $valor){
						$aplicarFiltro = true;
					}	
				}
				if($aplicarFiltro==true){
					if ($valor == id){
						echo '<td class="textoTabela cell"><select name="oper">
						<option value="selecione_tipo_op">Selecione um dos operadores:</option>
						<option value="maior"> > </option>
						<option value="maiorOuIgual"> >= </option>
						<option value="igual"> = </option>
						<option value="menor"> < </option>
						<option value="menorOuIgual"> <= </option>
						<option value="diferente"> != </option>
						<option value="like">LIKE</option>
						</select></td>';
					}
					else{
						echo '<td class="textoTabela cell"><select name="oper">
						<option value="selecione_tipo_op">Selecione um dos operadores:</option>
						<option value="igual">=</option>
						<option value="diferente">!=</option>
						<option value="like">LIKE</option>
						</select></td>';
					}
					echo "<td class='textoTabela cell'><span class='textoLabels'><strong>$valor</strong></span><span class='warning'>*</span><br>
					<input type='text' class='textInput2' id=".$valor." name=".$valor."></td></tr>";

				}
				else{
					echo "<td class='textoTabela cell'></td><td class='textoTabela cell'></td></tr>";
				}

				$aux++;
				$_SESSION["atributos_obter" . $aux] = $valor;  
			}
			$_SESSION["n_atributos_obter"] = $aux;  
			echo "</ul></table>
			<span class='information'><strong>e do item: * " . $_SESSION["item_name"] . " * uma listagem dos valores dos subitens:</strong></span>";
			$aux = 0;
			echo "<table class='tabela'><ul>";
			foreach($_REQUEST['subitens_obter'] as $chave=>$valor){
				$aplicarFiltro=false;
				echo "<tr class='row'><td class='textoTabela cell'><li>$valor</li></td>";
				foreach($_REQUEST['subitens_filtro'] as $chave1=>$valor1){
					if($valor1 == $valor){
						$aplicarFiltro = true;
					}	
				}
				if($aplicarFiltro==true){
					$valor = str_replace("_", " ", $valor);
					$querySubitens = "SELECT * FROM subitem WHERE name= '$valor'";
					$tabelaSubitens = mysqli_query($mySQL, $querySubitens);
					$id = 0;
					while ($rowSubitem = mysqli_fetch_assoc($tabelaSubitens)){
						$nomeFormulario = $rowSubitem["form_field_name"];
						$inputFields = "<span class='textoLabels'><strong>$nomeFormulario</strong></span><span class='warning'>*</span><br>";
						$inputFields .= "<input name='$nomeFormulario'";
						switch ($rowSubitem["value_type"]) {
							case "text":
								echo '<td class="textoTabela cell"><select name="oper">
								<option value="selecione_tipo_op">Selecione um dos operadores:</option>
								<option value="igual">=</option>
								<option value="diferente">!=</option>
								<option value="like">LIKE</option>
								</select></td>';
								$inputFields .= " type='" . $rowSubitem["form_field_type"] . "' class='textInput2' id='$id'>";
								echo "<td class='textoTabela cell'> $inputFields </td></tr>";
								$id++;
								break;
							case "bool":
								echo '<td class="textoTabela cell"><select name="oper">
								<option value="selecione_tipo_op">Selecione um dos operadores:</option>
								<option value="igual">=</option>
								<option value="diferente">!=</option>
								<option value="like">LIKE</option>
								</select></td>';
								$inputFields .= " type='radio'>";
								echo "<td class='textoTabela cell'> $inputFields </td></tr>";
								break;
							case "double":
							case "int":
								echo '<td class="textoTabela cell"><select name="oper">
								<option value="selecione_tipo_op">Selecione um dos operadores:</option>
								<option value="maior"> > </option>
								<option value="maiorOuIgual"> >= </option>
								<option value="igual"> = </option>
								<option value="menor"> < </option>
								<option value="menorOuIgual"> <= </option>
								<option value="diferente"> != </option>
								<option value="like">LIKE</option>
								</select></td>';
								$inputFields .= " type='text' class='textInput2' id='$id'>";
								echo "<td class='textoTabela cell'> $inputFields </td></tr>";
								$id++;
								break;
							case "enum":
								echo '<td class="textoTabela cell"><select name="oper">
								<option value="selecione_tipo_op">Selecione um dos operadores:</option>
								<option value="igual">=</option>
								<option value="diferente">!=</option>
								</select></td>';
								$query = "SELECT value from subitem_allowed_value WHERE subitem_id=" . $rowSubitem["id"];
								$result2 = mysqli_query($mySQL, $query);
								if ($rowSubitem["form_field_type"] == "radio") {
									$inputFields .= " checked ";
								}
								$index = 0;
								while ($val = mysqli_fetch_assoc($result2)) {
									$inputFields .= " type='" . $rowSubitem["form_field_type"] . "' value='" . $val["value"] . "'><span for='$id' class='textoLabels'>" . $val["value"] . "</span><br>";
									$index++;
									if ($index < mysqli_num_rows($result2)) {
										$inputFields .= "<input name='$nomeFormulario'";
									}
								}
								echo "<td class='textoTabela cell'> $inputFields </td></tr>";
								break;
						}
					}
				}
				else{
					echo "<td class='textoTabela cell'></td><td class='textoTabela cell'></td></tr>";
				}
				
				$aux++;
				$_SESSION["subitens_obter" . $aux] = $valor;  
			}
			$_SESSION["n_subitens_obter"] = $aux; 
			echo "</ul></table>
			<p hidden><input type='hidden' value='execucao' name='estado'></p>
			<input type='submit' value='Escolher filtros' class='submitButton textoLabels'>
			</form>";
		}
		
		elseif ($_REQUEST["estado"] == "execucao") {

			
			
			
			
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