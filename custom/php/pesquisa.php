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
					$option = $rowSubitem["id"] . "." . str_replace(" ", "_", $linha);
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
			
			//Guardar em variáveis de sessão os ids e nomes dos atributos e subitens escolhidos no estado anterior:
			$_SESSION["atrib_obter"] = $_REQUEST['atributos_obter'];
			$_SESSION["atrib_filtro"] = $_REQUEST['atributos_filtro'];
			$array_subitens_obter = array();
			foreach($_REQUEST['subitens_obter'] as $chave=>$valor){
				$separar_id_nome = explode(".", $valor);
				$separar_id_nome[1] = str_replace("_", " ", $separar_id_nome[1]);
				$array_subitens_obter[$separar_id_nome[0]] = $separar_id_nome[1];
			}
			$_SESSION["sub_obter"] = $array_subitens_obter;
			$array_subitens_filtrar = array();
			foreach($_REQUEST['subitens_filtro'] as $chave=>$valor){
				$separar_id_nome = explode(".", $valor);
				$separar_id_nome[1] = str_replace("_", " ", $separar_id_nome[1]);
				$array_subitens_filtrar[$separar_id_nome[0]] = $separar_id_nome[1];
			}
			$_SESSION["sub_filtro"] = $array_subitens_filtrar; 
			//------------------------------------
			
			$aux = 0;
			echo "<div class='caixaFormulario'><form method='post'>
			<span class='information'><strong>Irá ser realizada uma pesquisa que irá obter, como resultado, uma listagem de, para cada criança, dos seguintes dados pessoais escolhidos:</strong></span>
			<form method='post'><table><ul>";
			
			foreach($_SESSION["atrib_filtro"] as $chave=>$valor){
				echo "<tr><td class='cell2'><li>$valor</li></td>";

				if ($valor == id){
					echo '<td class="cell2"><select name="oper_atrib[]">
					<option value="selecione_tipo_op">Selecione um dos operadores:</option>
					<option value="maior"> > </option>
					<option value="maiorOuIgual"> >= </option>
					<option value="igual"> = </option>
					<option value="menor"> < </option>
					<option value="menorOuIgual"> <= </option>
					<option value="diferente"> != </option>
					</select></td>';
				}
				else{
					echo '<td class="cell2"><select name="oper_atrib[]">
					<option value="selecione_tipo_op">Selecione um dos operadores:</option>
					<option value="igual"> = </option>
					<option value="diferente"> != </option>
					<option value="like"> LIKE </option>
					</select></td>';
				}
				echo "<td class='cell2'><input type='text' class='textInput2' id=".$valor." name=val_atrib_filtrar[] placeholder=".$valor."*></td></tr>";
			}
		 
			foreach($_SESSION["atrib_obter"] as $chave=>$valor){
				$atributo_ja_listado = false;
				foreach($_SESSION["atrib_filtro"] as $chave2=>$valor2){
					if($valor==$valor2){
						$atributo_ja_listado = true;	
					}
				}
				if($atributo_ja_listado == false){
					echo "<tr><td class='cell2'><li>$valor</li></td><td class='cell2'></td><td class='cell2'></td></tr>";
				}
			}
			echo "</ul></table>
	
			<span class='information'><strong>e do item: * " . $_SESSION["item_name"] . " * uma listagem dos valores dos subitens:</strong></span>";
			

			echo "<table><ul>";
						
			foreach($_SESSION["sub_filtro"] as $chave=>$valor){
				echo "<tr><td class='cell2'><li>$valor</li></td>";

				$querySubitens = "SELECT * FROM subitem WHERE name= '$valor'";
				$tabelaSubitens = mysqli_query($mySQL, $querySubitens);
				$id = 0;
				while ($rowSubitem = mysqli_fetch_assoc($tabelaSubitens)){
					$nomeFormulario = $rowSubitem["form_field_name"];
					$inputFields = "<span class='textoLabels'><strong>$nomeFormulario</strong></span><span class='warning'>*</span><br>";
					$inputFields .= "<input name=val_sub_filtrar[]";
					switch ($rowSubitem["value_type"]) {
						case "text":
							echo '<td class="cell2"><select name="oper_sub[]">
							<option value="selecione_tipo_op">Selecione um dos operadores:</option>
							<option value="igual"> = </option>
							<option value="diferente"> != </option>
							<option value="like"> LIKE </option>
							</select></td>';
							$inputFields .= " type='" . $rowSubitem["form_field_type"] . "' class='textInput2' id='$id'>";
							echo "<td class='cell2'> $inputFields </td></tr>";
							$id++;
							break;
						case "bool":
							echo '<td class="cell2"><select name="oper_sub[]">
							<option value="selecione_tipo_op">Selecione um dos operadores:</option>
							<option value="igual"> = </option>
							<option value="diferente"> != </option>
							</select></td>';
							$inputFields .= " type='radio' value='True'>True<br>
							<input name=val_sub_filtrar[] type='radio' value='False'>False";
							echo "<td class='cell2'> $inputFields </td></tr>";
							break;
						case "double":
						case "int":
							echo '<td class="cell2"><select name="oper_sub[]">
							<option value="selecione_tipo_op">Selecione um dos operadores:</option>
							<option value="maior"> > </option>
							<option value="maiorOuIgual"> >= </option>
							<option value="igual"> = </option>
							<option value="menor"> < </option>
							<option value="menorOuIgual"> <= </option>
							<option value="diferente"> != </option>
							</select></td>';
							$inputFields .= " type='text' class='textInput2' id='$id'>";
							echo "<td class='cell2'> $inputFields </td></tr>";
							$id++;
							break;
						case "enum":
							echo '<td class="cell2"><select name="oper_sub[]">
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
							$inputFields = "<span class='textoLabels'><strong>$nomeFormulario</strong></span><span class='warning'>*</span><br>";
							$inputFields .= "<input name=val_sub_filtrar[x][]";
							while ($val = mysqli_fetch_assoc($result2)) {
								$inputFields .= " type='" . $rowSubitem["form_field_type"] . "' value='" . $val["value"] . "'><span for='$id' class='textoLabels'>" . $val["value"] . "</span><br>";
								$index++;
								if ($index < mysqli_num_rows($result2)) {
									$inputFields .= "<input name=val_sub_filtrar[x][]";
								}
							}
							echo "<td class='cell2'> $inputFields </td></tr>";
							break;
					}
				}				
			}
			
			foreach($_SESSION["sub_obter"] as $chave=>$valor){
				$atributo_ja_listado = false;
				foreach($_SESSION["sub_filtro"] as $chave2=>$valor2){
					if($valor==$valor2){
						$atributo_ja_listado = true;	
					}
				}
				if($atributo_ja_listado == false){
					echo "<tr><td class='cell2'><li>$valor</li></td><td class='cell2'></td><td class='cell2'></td></tr>";
				}
			}
			echo "</ul></table>
			
			<p hidden><input type='hidden' value='execucao' name='estado'></p>
			<input type='submit' value='Escolher filtros' class='submitButton textoLabels'>
			</form>";
		}
		
		elseif ($_REQUEST["estado"] == "execucao") {
			
			$oper_atrib = $_REQUEST['oper_atrib'];
			$val_atrib_filtrar = $_REQUEST['val_atrib_filtrar'];
			$oper_sub = $_REQUEST['oper_sub'];
			$val_sub_filtrar = $_REQUEST['val_sub_filtrar'];
			
			$primeiro = true;
			foreach($_SESSION["atrib_obter"] as $chave=>$valor){
				if($valor == "name"){$valor = "child.name";}
				if($primeiro==true){
					$query = "SELECT " . $valor;
					$primeiro = false;	
				}
				else{
					$query.= ", " . $valor;
				}
			}

			if (count($_SESSION["sub_obter"])!=0){
				if(count($_SESSION["atrib_obter"])==0){
					$query = "SELECT name, value FROM subitem, value ";
				}
				else{
					$query .= ", subitem.name, value FROM child, subitem, value ";
					
				}
			}
			else{
				if(count($_SESSION["atrib_obter"])!=0){
					$query .= " FROM child ";
				}
			}
			
			/*if(count($oper_atrib)!=0){
				$query .= "WHERE ";
				$aux=0;
				foreach($_SESSION["atrib_filtro"] as $chave=>$valor){
					$query .= 
					
				}

				
			}*/
			
			echo "<strong>QUERY:</strong><br>";
			echo $query;
			
			/*$tabela = mysqli_query($mySQL, $query);
				
			while ($rowQuery = mysqli_fetch_assoc($tabela)){
					
			}*/

			/*foreach($val_sub_filtrar as $chave=>$valor){
				echo $valor;
				echo "<br>";
				
			}*/

			/*echo "<table class='tabela'><tr class='row'>";
			foreach($_SESSION["atrib_obter"] as $chave=>$valor){
				echo "<th class='textoTabela cell'>".$valor."</th>";	
			}
			foreach($_SESSION["sub_obter"] as $chave=>$valor){
				echo "<th class='textoTabela cell'>".$valor."</th>";	
			}
			echo "</tr>";*/

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