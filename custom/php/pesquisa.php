<?php
require_once("custom/php/common.php");
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
if (verificaCapability("search")) { //Verifica se o utilizador está autenticado e tem a capability "search"
    $mySQL = ligacaoBD(); //Efetua a ligação com a base de dados
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) { //Se não for possível selecionar a base de dados "bitnami_wordpress" é apresentado o erro ocorrido
        die("Connection failed: " . mysqli_connect_error());
    } else {
        if ($_REQUEST["estado"] == "escolha") { //Escolher quais atributos/subitens para obter e/ou filtrar
            $_SESSION["item_id"] = $_REQUEST["item"]; //Guarda na variável de sessão o id do item escolhido
            $queryNomeItem = "SELECT name from item WHERE id=" . $_SESSION["item_id"]; //Query para obter o nome do item escolhido
            $tabelaNomeItem = mysqli_query($mySQL, $queryNomeItem);
            $nomeItem = mysqli_fetch_assoc($tabelaNomeItem);
            $_SESSION["item_name"] = $nomeItem["name"]; //Guarda na variável de sessão o nome do item escolhido

            //        *************** FORMULÁRIO: *****************
            $action = get_site_url() . '/' . $current_page;
            echo "<form method='post' action='$action'>";
            //-----------------------------------------------------------
            //                        1º TABELA
            //-----------------------------------------------------------
            //Cabeçalho da tabela para obter/filtrar os atributos da tabela "child":
            echo "<table class='tabela'>
			<tr class='row'>
			<th class='textoTabela cell'>Atributo</th>
			<th class='textoTabela cell'>Obter</th>
			<th class='textoTabela cell'>Filtro</th>
			</tr>";
            $atributos = array("id", "name", "birth_date", "tutor_name", "tutor_phone", "tutor_email"); //atributos da tabela "child"

            //Lista dos atributos da tabela child com a 1ª coluna mostrando o nome de cada atributo,
            //seguido por uma checkbox em cada uma das duas colunas seguintes:
            for ($i = 0; $i < 6; $i++) {
                echo "<tr class='row'><td class='textoTabela cell'>" . $atributos[$i] . "</td>
				<td class='textoTabela cell'><input type='checkbox' name='atributos_obter[]' value=" . $atributos[$i] . "></td>
				<td class='textoTabela cell'><input type='checkbox' name='atributos_filtro[]' value=" . $atributos[$i] . "></td>
				</tr>";
            }
            echo "</table><br><br>";
            //-----------------------------------------------------------
            //                       2º TABELA
            //-----------------------------------------------------------
            //Cabeçalho da tabela para obter/filtrar os subitens associados ao item ecolhido:
            echo "<table class='tabela'>
            <tr class='row'>
			<th class='textoTabela cell'>Subitem</th>
			<th class='textoTabela cell'>Obter</th>
			<th class='textoTabela cell'>Filtro</th> 
			</tr>";

            $querySubitens = "SELECT * FROM subitem WHERE item_id=" . $_SESSION["item_id"]; //Query para obter os subitens associados ao item ecolhido
            $tabelaSubitens = mysqli_query($mySQL, $querySubitens);

            //lista dos subitens do item escolhido com a 1ª coluna mostrando o nome de cada subitem,
            //seguido por uma checkbox em cada uma das duas colunas seguintes:
            if (mysqli_num_rows($tabelaSubitens) > 0) {
                while ($rowSubitem = mysqli_fetch_assoc($tabelaSubitens)) {
                    echo "<tr class='row'>
					<td class='textoTabela cell'>" . $rowSubitem["name"] . "</td>";
                    $linha = $rowSubitem["name"];
                    //Substituir espaços do nome do subitem por underscore para o valor não ficar apenas com a 1º palavra do nome do subitem
                    $option = $rowSubitem["id"] . "." . str_replace(" ", "_", $linha); //Juntar id do subitem com o nome do subitem (separados por um ponto)
                    echo "<td class='textoTabela cell'><input type='checkbox' name='subitens_obter[]' value=" . $option . "></td>
					<td class='textoTabela cell'><input type='checkbox' name='subitens_filtro[]' value=" . $option . "></td>
					</tr>";
                }
            }
            echo "</table>";
            //-----------------------------------------------------------

            echo "<input type='submit' value='Escolher' class='submitButton textoLabels'>
			<input type='hidden' value='escolher_filtros' name='estado'>
			</form>";

        } elseif ($_REQUEST["estado"] == "escolher_filtros") { //Listar os atributos/subitens para obter e apresentar um formulário para os atributos/subitens para filtrar
            //--------------------------------------------------------------------------------------------------------------------------
            //		Guardar em variáveis de sessão os ids e nomes dos atributos e subitens escolhidos no estado anterior:
            //--------------------------------------------------------------------------------------------------------------------------
            $_SESSION["atrib_obter"] = $_REQUEST['atributos_obter'];
            $_SESSION["atrib_filtro"] = $_REQUEST['atributos_filtro'];

            //Colocar no array $array_subitens_obter o id e nome dos subitens para obter, onde:
            //A chave corresponde ao id do subitem e o valor corresponde ao nome do subitem
            $array_subitens_obter = array();
            foreach ($_REQUEST['subitens_obter'] as $chave => $valor) {
                $separar_id_nome = explode(".", $valor); //Separar o id e o nome do subitem
                $separar_id_nome[1] = str_replace("_", " ", $separar_id_nome[1]); //Voltar a colocar o nome do subitem com espaços
                $array_subitens_obter[$separar_id_nome[0]] = $separar_id_nome[1];
            }

            $_SESSION["sub_obter"] = $array_subitens_obter;

            //Colocar no array $array_subitens_filtrar o id e nome dos subitens para filtrar, onde:
            //A chave corresponde ao id do subitem e o valor corresponde ao nome do subitem
            $array_subitens_filtrar = array();
            foreach ($_REQUEST['subitens_filtro'] as $chave => $valor) {
                $separar_id_nome = explode(".", $valor); //Separar o id e o nome do subitem
                $separar_id_nome[1] = str_replace("_", " ", $separar_id_nome[1]); //Voltar a colocar o nome do subitem com espaços
                $array_subitens_filtrar[$separar_id_nome[0]] = $separar_id_nome[1];
            }

            $_SESSION["sub_filtro"] = $array_subitens_filtrar;
            //--------------------------------------------------------------------------------------------------------------------------

            echo "<div class='caixaSubTitulo'><h3 >Pesquisa - Escolher filtros</h3></div>";
            echo "<div class='caixaFormulario'>
			<span class='warning'>* Campos obrigatórios</span><br>
			<span class='information'><strong>
			Irá ser realizada uma pesquisa que irá obter, como resultado, uma listagem de, para cada criança, dos seguintes dados pessoais escolhidos:
			</strong></span>";

            //        *************** FORMULÁRIO: *****************
            //-----------------------------------------------------------
            //                        ATRIBUTOS
            //-----------------------------------------------------------
            $action = get_site_url() . '/' . $current_page;
            echo "<form method='post' action='$action'>
			<table>
			<ul>";

            // Listar e apresentar formulário primeiro para os atributos para filtrar:

            foreach ($_SESSION["atrib_filtro"] as $chave => $valor) {
                echo "<tr>
				<td class='cell2'><li>$valor</li></td>";

                if ($valor == "id" || $valor == "birth_date" || $valor == "tutor_phone") {
                    echo '<td class="cell2">
					<span class="textoLabels"><strong>Operador</strong></span><span class="warning">*</span><br>
					<select name="oper_atrib[]">
					<option value="selecione_tipo_op">Selecione um dos operadores:</option>
					<option value="maior"> > </option>
					<option value="maiorOuIgual"> >= </option>
					<option value="igual"> = </option>
					<option value="menor"> < </option>
					<option value="menorOuIgual"> <= </option>
					<option value="diferente"> != </option>
					</select></td>';
                } else {
                    echo '<td class="cell2">
					<span class="textoLabels"><strong>Operador</strong></span><span class="warning">*</span><br>
					<select name="oper_atrib[]">
					<option value="selecione_tipo_op">Selecione um dos operadores:</option>
					<option value="igual"> = </option>
					<option value="diferente"> != </option>
					<option value="like"> LIKE </option>
					</select></td>';
                }
                echo "<td class='cell2'>
				<span class='textoLabels'><strong>$valor</strong></span><span class='warning'>*</span><br>";

                if ($valor == "birth_date") {
                    echo "<input type='text' class='textInput2' id=" . $valor . " name=val_atrib_filtrar[] placeholder='AAAA-MM-DD'>";
                } elseif ($valor == "tutor_email") {
                    echo "<input type='text' class='textInput2' id=" . $valor . " name=val_atrib_filtrar[] placeholder='email@example.com'>";
                } else {
                    echo "<input type='text' class='textInput2' id=" . $valor . " name=val_atrib_filtrar[]>";

                }
                echo "</td></tr>";
            }

            // Depois listar os atributos para obter (exceto aqueles que também serão para filtrar):

            foreach ($_SESSION["atrib_obter"] as $chave => $valor) {
                $atributo_ja_listado = false;
                foreach ($_SESSION["atrib_filtro"] as $chave2 => $valor2) {
                    if ($valor == $valor2) {
                        $atributo_ja_listado = true;
                    }
                }
                if ($atributo_ja_listado == false) {
                    echo "<tr>
					<td class='cell2'><li>$valor</li></td>
					<td class='cell2'></td>
					<td class='cell2'></td>
					</tr>";
                }
            }
            echo "</ul></table>
	
			<span class='information'><strong>e do item: * " . $_SESSION["item_name"] . " * uma listagem dos valores dos subitens:</strong></span>";

            //-----------------------------------------------------------
            //                        SUBITENS
            //-----------------------------------------------------------
            echo "<table><ul>";

            $n_chave = 0;

            // Listar e apresentar formulário primeiro para os subitens para filtrar:

            foreach ($_SESSION["sub_filtro"] as $chave => $valor) {
                echo "<tr>
				<td class='cell2'><li>$valor</li></td>";

                $querySubitens = "SELECT * FROM subitem WHERE id='$chave'"; //Query para obter os valores do subitem 
                $tabelaSubitens = mysqli_query($mySQL, $querySubitens);

                while ($rowSubitem = mysqli_fetch_assoc($tabelaSubitens)) { //Para esse subitem (este while apenas será executado uma vez)           
                    $inputFields = "<span class='textoLabels'><strong>$valor</strong></span><span class='warning'>*</span><br>";
                    $inputFields .= "<input name=val_sub_filtrar[]";
                    switch ($rowSubitem["value_type"]) { //Dependendo do tipo de valor do subitem, será apresentada uma selectbox e um tipo de campo de formulário adequado

                        case "text": //tipo de campo de formulário pode ser: text ou textbox
                            echo '<td class="cell2">
							<span class="textoLabels"><strong>Operador</strong></span><span class="warning">*</span><br>
							<select name="oper_sub[]">
							<option value="selecione_tipo_op">Selecione um dos operadores:</option>
							<option value="igual"> = </option>
							<option value="diferente"> != </option>
							<option value="like"> LIKE </option>
							</select></td>';

                            $inputFields .= " type='" . $rowSubitem["form_field_type"] . "' class='textInput2' '>";
                            echo "<td class='cell2'> $inputFields </td></tr>";
                            $n_chave++;
                            break;

                        case "bool": //tipo de campo de formulário é radio (verdadeiro ou falso)
                            echo '<td class="cell2">
							<span class="textoLabels"><strong>Operador</strong></span><span class="warning">*</span><br>
							<select name="oper_sub[]">
							<option value="selecione_tipo_op">Selecione um dos operadores:</option>
							<option value="igual"> = </option>
							<option value="diferente"> != </option>
							</select></td>';

                            $inputFields .= " type='radio' value='verdadeiro'>verdadeiro<br>
							<input name=val_sub_filtrar[] type='radio' value='falso'>falso";
                            echo "<td class='cell2'> $inputFields </td></tr>";
                            $n_chave++;
                            break;

                        case "double": //tipo de campo de formulário é text
                        case "int":
                            echo '<td class="cell2">
							<span class="textoLabels"><strong>Operador</strong></span><span class="warning">*</span><br>
							<select name="oper_sub[]">
							<option value="selecione_tipo_op">Selecione um dos operadores:</option>
							<option value="maior"> > </option>
							<option value="maiorOuIgual"> >= </option>
							<option value="igual"> = </option>
							<option value="menor"> < </option>
							<option value="menorOuIgual"> <= </option>
							<option value="diferente"> != </option>
							</select></td>';

                            $inputFields .= " type='text' class='textInput2'>";
                            echo "<td class='cell2'> $inputFields </td></tr>";
                            $n_chave++;
                            break;

                        case "enum": //tipo de campo de formulário pode ser: selectbox, radio ou checkbox
                            echo '<td class="cell2">
							<span class="textoLabels"><strong>Operador</strong></span><span class="warning">*</span><br>
							<select name="oper_sub[]">
							<option value="selecione_tipo_op">Selecione um dos operadores:</option>
							<option value="igual">=</option>
							<option value="diferente">!=</option>
							</select></td>';

                            $isSelectBox = $rowSubitem["form_field_type"] == "selectbox";

                            if ($isSelectBox) {
                                $inputFields = "<span class='textoLabels'><strong>$valor</strong></span><span class='warning'>*</span><br>";
                                $inputFields .= "<select name=val_sub_filtrar[]>";
                                $inputFields .= "<option value='empty'>Selecione um valor</option>";
                            } else {
                                $inputFields = "<span class='textoLabels'><strong>$valor</strong></span><span class='warning'>*</span><br>";
                            }
                            $query = "SELECT value from subitem_allowed_value WHERE subitem_id=" . $rowSubitem["id"]; //Query para obter os valores permitidos do subitem
                            $result2 = mysqli_query($mySQL, $query);

                            while ($val = mysqli_fetch_assoc($result2)) { //Enquanto houver valores permitidos
                                if ($isSelectBox) {
                                    $inputFields .= "<option value='" . $val["value"] . "'>" . $val["value"] . "</option>";
                                }
                                if ($rowSubitem["form_field_type"] == "checkbox") {
                                    $inputFields .= "<input name=val_sub_filtrar[" . $n_chave . "][]";
                                    $inputFields .= " type=checkbox value=" . $val["value"] . "><span class='textoLabels'>" . $val["value"] . "</span><br>";
                                }
                                if ($rowSubitem["form_field_type"] == "radio") {
                                    $inputFields .= "<input name=val_sub_filtrar[] type='radio' value=" . $val["value"] . "><span class='textoLabels'>" . $val["value"] . "</span><br>";
                                }
                            }
                            if ($isSelectBox) {
                                $inputFields .= "</select>";
                            }

                            echo "<td class='cell2'> $inputFields </td></tr>";
                            $n_chave++;
                            break;
                    }
                }
            }

            // Depois listar os subitens para obter (exceto aqueles que também serão para filtrar):

            foreach ($_SESSION["sub_obter"] as $chave => $valor) {
                $atributo_ja_listado = false;
                foreach ($_SESSION["sub_filtro"] as $chave2 => $valor2) {
                    if ($valor == $valor2) {
                        $atributo_ja_listado = true;
                    }
                }
                if ($atributo_ja_listado == false) {
                    echo "<tr><td class='cell2'><li>$valor</li></td><td class='cell2'></td><td class='cell2'></td></tr>";
                }
            }
            echo "</ul></table>
			
			<p hidden><input type='hidden' value='execucao' name='estado'></p>
			<input type='submit' value='Escolher filtros' class='submitButton textoLabels'>
			</form>";
        } //-----------------------------------------------------------

        elseif ($_REQUEST["estado"] == "execucao") { //Construir dinamicamente a query SQL que irá executar a pesquisa configurada nos estados anteriores e apresentar o resultado

            $oper_atrib = $_REQUEST['oper_atrib'];
            $val_atrib_filtrar = $_REQUEST['val_atrib_filtrar'];
            $oper_sub = $_REQUEST['oper_sub'];
            $val_sub_filtrar = $_REQUEST['val_sub_filtrar'];

            //Verificação se o utilizador preencheu todos os campos:

            $houveErros = false;

            foreach ($oper_atrib as $chave => $valor) {
                if ($valor == "selecione_tipo_op") {
                    $houveErros = true;
                }
            }
            foreach ($oper_sub as $chave => $valor) {
                if ($valor == "selecione_tipo_op") {
                    $houveErros = true;
                }
            }
            foreach ($val_atrib_filtrar as $chave => $valor) {
                if (empty($valor)) {
                    $houveErros = true;
                }
            }
            $total_sub_preenchidos = 0;
            foreach ($val_sub_filtrar as $chave => $valor) {
                if (empty($valor) || $valor == "empty") {
                    $houveErros = true;
                }
                $total_sub_preenchidos++;
            }
            $total_sub_filtro = count($_SESSION["sub_filtro"]);
            if ($total_sub_preenchidos < $total_sub_filtro) {
                $houveErros = true;
            }

            //-----------


            if ($houveErros) { //Se os campos não foram todos preenchidos, é apresentada uma mensagem de erro e um botão para voltar atrás
                echo "<span class='warning'>Não preencheu todos os campos do formulário!</span><br>";
                voltarAtras();
            } else { //Se os campos foram todos preenchidos

                // ******** Construção dinâmica da query SQL que irá executar a pesquisa configurada nos estados anteriores **********

                //	$query -> query para executar
                //  $queryApresentada -> query para apresentar antes da tabela com o resultado da query

                //-----------------------------------------------------------
                //              INÍCIO DA QUERY: SELECT ... FROM ...
                //-----------------------------------------------------------

                if (count($_SESSION["atrib_obter"]) != 0) {  //Se existirem atributos para obter
                    $primeiro = true;
                    foreach ($_SESSION["atrib_obter"] as $chave => $valor) {
                        if ($valor == "name") {
                            $valor = 'child.name AS "Nome da criança"';
                        }
                        if ($valor == "id") {
                            $valor = 'child.id AS "ID da criança"';
                        }
                        if ($valor == "birth_date") {
                            $valor = 'child.birth_date AS "Data de nascimento da criança"';
                        }
                        if ($valor == "tutor_name") {
                            $valor = 'child.tutor_name AS "Nome do Encarregado de Educação"';
                        }
                        if ($valor == "tutor_phone") {
                            $valor = 'child.tutor_phone AS "Telefone do Encarregado de Educação"';
                        }
                        if ($valor == "tutor_email") {
                            $valor = 'child.tutor_email AS "E-mail do Encarregado de Educação"';
                        }
                        if ($primeiro == true) {
                            $query = 'SELECT ' . $valor;
                            $queryApresentada = 'SELECT ' . $valor;
                            $primeiro = false;
                        } else {
                            $query .= ', ' . $valor;
                            $queryApresentada .= ', ' . $valor;
                        }
                    }
                }

                if (count($_SESSION["sub_obter"]) != 0) { //Se existirem subitens para obter
                    if (count($_SESSION["atrib_obter"]) == 0) { //Se existirem subitens para obter e não existitem atributos para obter
                        $query = 'SELECT subitem.name AS "Nome do subitem", value AS "Valor" FROM child, subitem, value ';
                        $queryApresentada = 'SELECT subitem.name AS "Nome do subitem", value AS "Valor" <br>FROM child, subitem, value ';
                    } else {//Se existirem subitens para obter e existitem atributos para obter
                        $query .= ', subitem.name AS "Nome do subitem", value AS "Valor" FROM child, subitem, value ';
                        $queryApresentada .= ', subitem.name AS "Nome do subitem", value AS "Valor" <br>FROM child, subitem, value ';

                    }
                } else { //Se não existirem subitens para obter
                    if (count($_SESSION["atrib_obter"]) != 0) { //Se não existirem subitens para obter e existitem atributos para obter
                        $query .= ' FROM child ';
                        $queryApresentada .= '<br>FROM child ';
                    }
                }

                //-----------------------------------------------------------
                //                         WHERE
                //-----------------------------------------------------------

                if (count($_SESSION["atrib_obter"]) + count($_SESSION["sub_obter"]) != 0) { //Se não foi obtido nenhum atributo/subitem, não é apresentada nenhuma query

                    // ********** Filtrar atributos: ***********

                    if (count($_SESSION["atrib_filtro"]) > 0) {
                        $query .= 'WHERE ';
                        $queryApresentada .= '<br>WHERE ';

                        $auxx = 0;
                        foreach ($_SESSION["atrib_filtro"] as $chave => $valor) {
                            if ($valor == "name") {
                                $valor = "child.name";
                            }
                            if ($valor == "id") {
                                $valor = "child.id";
                            }

                            $query .= '' . $valor . ' ';
                            $queryApresentada .= '' . $valor . ' ';

                            switch ($oper_atrib[$auxx]) {
                                case "maior":
                                    $query .= '> ';
                                    $queryApresentada .= '> ';
                                    break;
                                case "maiorOuIgual":
                                    $query .= '>= ';
                                    $queryApresentada .= '>= ';
                                    break;
                                case "igual":
                                    $query .= '= ';
                                    $queryApresentada .= '= ';
                                    break;
                                case "menor":
                                    $query .= '< ';
                                    $queryApresentada .= '< ';
                                    break;
                                case "menorOuIgual":
                                    $query .= '<= ';
                                    $queryApresentada .= '<= ';
                                    break;
                                case "diferente":
                                    $query .= '!= ';
                                    $queryApresentada .= '!= ';
                                    break;
                                case "like":
                                    $query .= 'LIKE ';
                                    $queryApresentada .= 'LIKE ';
                                    break;
                            }

                            if (is_numeric($val_atrib_filtrar[$auxx])) {
                                $query .= '' . $val_atrib_filtrar[$auxx] . ' ';
                                $queryApresentada .= '' . $val_atrib_filtrar[$auxx] . ' ';
                            } else {
                                if ($oper_atrib[$auxx] == "like") {
                                    $query .= '"%' . $val_atrib_filtrar[$auxx] . '%" ';
                                    $queryApresentada .= '"%' . $val_atrib_filtrar[$auxx] . '%" ';
                                } else {
                                    $query .= '"' . $val_atrib_filtrar[$auxx] . '" ';
                                    $queryApresentada .= '"' . $val_atrib_filtrar[$auxx] . '" ';
                                }
                            }

                            if (($auxx + 1) < count($_SESSION["atrib_filtro"])) {
                                $query .= 'AND ';
                                $queryApresentada .= '<br>AND ';

                            }
                            $auxx++;
                        }
                    }

                    //Continuação da query consoante:
                    //o número de atributos a filtrar
                    //o número de subitens a filtrar
                    //o número de subitens a obter

                    if (count($_SESSION["atrib_filtro"]) == 0 && count($_SESSION["sub_filtro"]) != 0 && count($_SESSION["sub_obter"]) != 0) {
                        $query .= 'WHERE subitem.id = subitem_id AND child.id = child_id ';
                        $queryApresentada .= '<br>WHERE subitem.id = subitem_id <br>AND child.id = child_id ';
                    }
                    if (count($_SESSION["atrib_filtro"]) == 0 && count($_SESSION["sub_filtro"]) != 0 && count($_SESSION["sub_obter"]) == 0) {
                        $query .= 'WHERE ';
                        $queryApresentada .= '<br>WHERE ';
                    }
                    if (count($_SESSION["atrib_filtro"]) == 0 && count($_SESSION["sub_filtro"]) == 0 && count($_SESSION["sub_obter"]) != 0) {
                        $query .= 'WHERE subitem.id = subitem_id AND child.id = child_id ';
                        $queryApresentada .= '<br>WHERE subitem.id = subitem_id <br>AND child.id = child_id ';
                    }
                    if (count($_SESSION["atrib_filtro"]) > 0 && count($_SESSION["sub_filtro"]) != 0 && count($_SESSION["sub_obter"]) != 0) {
                        $query .= 'AND subitem.id = subitem_id AND child.id = child_id ';
                        $queryApresentada .= '<br>AND subitem.id = subitem_id <br>AND child.id = child_id ';
                    }
                    if (count($_SESSION["atrib_filtro"]) > 0 && count($_SESSION["sub_filtro"]) == 0 && count($_SESSION["sub_obter"]) != 0) {
                        $query .= 'AND subitem.id = subitem_id AND child.id = child_id ';
                        $queryApresentada .= '<br>AND subitem.id = subitem_id <br>AND child.id = child_id ';
                    }


                    $auxx = 0;
                    if (count($_SESSION["sub_obter"]) > 0) { //Se existirem subitens para obter

                        // ******** Obter no resultado o/s valor/es desse subitem: **********

                        $query .= 'AND ( ';
                        $queryApresentada .= '<br>AND ( ';
                        foreach ($_SESSION["sub_obter"] as $chave => $valor) {
                            $query .= 'subitem.id = ';
                            $queryApresentada .= 'subitem.id = ';
                            $query .= '' . $chave . ' ';
                            $queryApresentada .= '' . $chave . ' ';

                            if (($auxx + 1) < count($_SESSION["sub_obter"])) {
                                $query .= 'OR ';
                                $queryApresentada .= 'OR ';
                            } else {
                                $query .= ') ';
                                $queryApresentada .= ') ';
                            }
                            $auxx++;
                        }
                    }

                    $auxx = 0;
                    $primeiro = true;

                    if (count($_SESSION["sub_filtro"]) > 0) { //Se existirem subitens para filtrar

                        // ********** Filtrar subitens: ***********

                        foreach ($_SESSION["sub_filtro"] as $chave => $valor) {
                            if (is_array($val_sub_filtrar[$auxx])) { //Para o caso do subitem a filtrar ter como campo de formulário uma checkbox
                                if (count($_SESSION["atrib_filtro"]) == 0 && count($_SESSION["sub_obter"]) == 0 && $primeiro == true) { //Se existirem atributos para filtrar e subitens para obter e ser o 1º valor a ser filtrado
                                    $query .= '( child.id IN (SELECT child_id FROM value WHERE subitem_id = ' . $chave . ' AND value ';
                                    $queryApresentada .= '( child.id IN (SELECT child_id FROM value WHERE subitem_id = ' . $chave . ' AND value ';
                                    $primeiro = false;
                                } else {
                                    $query .= 'AND ( child.id IN (SELECT child_id FROM value WHERE subitem_id = ' . $chave . ' AND value ';
                                    $queryApresentada .= '<br>AND (child.id IN (SELECT child_id FROM value WHERE subitem_id = ' . $chave . ' AND value ';
                                }
                            } else { //Quando o input só tem um valor
                                if (count($_SESSION["atrib_filtro"]) == 0 && count($_SESSION["sub_obter"]) == 0 && $primeiro == true) { //Se existirem atributos para filtrar e subitens para obter e ser o 1º valor a ser filtrado
                                    $query .= 'child.id IN (SELECT child_id FROM value WHERE subitem_id = ' . $chave . ' AND value ';
                                    $queryApresentada .= 'child.id IN (SELECT child_id FROM value WHERE subitem_id = ' . $chave . ' AND value ';
                                    $primeiro = false;
                                } else {
                                    $query .= 'AND child.id IN (SELECT child_id FROM value WHERE subitem_id = ' . $chave . ' AND value ';
                                    $queryApresentada .= '<br>AND child.id IN (SELECT child_id FROM value WHERE subitem_id = ' . $chave . ' AND value ';
                                }
                            }

                            switch ($oper_sub[$auxx]) {
                                case "maior":
                                    $query .= '> ';
                                    $queryApresentada .= '> ';
                                    $aux_op_checkbox = '> ';
                                    break;
                                case "maiorOuIgual":
                                    $query .= '>= ';
                                    $queryApresentada .= '>= ';
                                    $aux_op_checkbox = '>= ';
                                    break;
                                case "igual":
                                    $query .= '= ';
                                    $queryApresentada .= '= ';
                                    $aux_op_checkbox = '= ';
                                    break;
                                case "menor":
                                    $query .= '< ';
                                    $queryApresentada .= '< ';
                                    $aux_op_checkbox = '< ';
                                    break;
                                case "menorOuIgual":
                                    $query .= '<= ';
                                    $queryApresentada .= '<= ';
                                    $aux_op_checkbox = '<= ';
                                    break;
                                case "diferente":
                                    $query .= '!= ';
                                    $queryApresentada .= '!= ';
                                    $aux_op_checkbox = '!= ';
                                    break;
                                case "like":
                                    $query .= 'LIKE ';
                                    $queryApresentada .= 'LIKE ';
                                    $aux_op_checkbox = 'LIKE ';
                                    break;
                            }

                            if (is_array($val_sub_filtrar[$auxx])) { //Para o caso do subitem a filtrar ter como campo de formulário uma checkbox
                                $primeiroValor = true;
                                foreach ($val_sub_filtrar[$auxx] as $chave2 => $valor2) {
                                    if ($primeiroValor == true) {
                                        if (is_numeric($valor2)) {
                                            $query .= '' . $valor2 . ') ';
                                            $queryApresentada .= '' . $valor2 . ') ';
                                        } else {
                                            if ($oper_atrib[$auxx] == "like") {
                                                $query .= '"%' . $valor2 . '%") ';
                                                $queryApresentada .= '"%' . $valor2 . '%") ';
                                            } else {
                                                $query .= '"' . $valor2 . '") ';
                                                $queryApresentada .= '"' . $valor2 . '") ';
                                            }
                                        }
                                        $primeiroValor = false;
                                    } else {

                                        $query .= 'OR child.id IN (SELECT child_id FROM value WHERE subitem_id = ' . $chave . ' AND value ' . $aux_op_checkbox . '';
                                        $queryApresentada .= 'OR child.id IN (SELECT child_id FROM value WHERE subitem_id = ' . $chave . ' AND value ' . $aux_op_checkbox . '';

                                        if (is_numeric($valor2)) {
                                            $query .= '' . $valor2 . ') ';
                                            $queryApresentada .= '' . $valor2 . ') ';
                                        } else {
                                            if ($oper_atrib[$auxx] == "like") {
                                                $query .= '"%' . $valor2 . '%") ';
                                                $queryApresentada .= '"%' . $valor2 . '%") ';
                                            } else {
                                                $query .= '"' . $valor2 . '") ';
                                                $queryApresentada .= '"' . $valor2 . '") ';
                                            }
                                        }
                                    }
                                }
                                $query .= ' ) ';
                                $queryApresentada .= ' ) ';
                            } else { //Quando o input só tem um valor
                                if (is_numeric($val_sub_filtrar[$auxx])) {
                                    $query .= '' . $val_sub_filtrar[$auxx] . ') ';
                                    $queryApresentada .= '' . $val_sub_filtrar[$auxx] . ') ';
                                } else {
                                    if ($oper_sub[$auxx] == "like") {
                                        $query .= '"%' . $val_sub_filtrar[$auxx] . '%") ';
                                        $queryApresentada .= '"%' . $val_sub_filtrar[$auxx] . '%") ';
                                    } else {
                                        $query .= '"' . $val_sub_filtrar[$auxx] . '") ';
                                        $queryApresentada .= '"' . $val_sub_filtrar[$auxx] . '") ';
                                    }
                                }
                            }

                            $auxx++;
                        }
                    }
                }

                //          ********* Query **********

                echo "<strong><span class='textoValidar'>QUERY:</span></strong><br>";
                echo $queryApresentada;

                //******** Tabela com o resultado da query ******

                $resultado = mysqli_query($mySQL, $query);
                $tabelaGerada=mysqli_fetch_all($resultado);
                $resultado = mysqli_query($mySQL, $query);
                $keys=array();
                if ($resultado->num_rows > 0) {
                    echo "<table class='tabela'>
						<tr class='row'>";
                    $campo = $resultado->fetch_fields(); //Obter informações da definição das colunas do resultado como objetos
                    $campos = array();
                    $aux = 0;
                    foreach ($campo as $coluna) {
                        echo "<th class='textoTabela cell'>" . $coluna->name . "</th>";
                        array_push($keys, $coluna->name);
                        array_push($campos, array($aux++, $coluna->name)); //Coloca em cada posição do array $campos um array contendo na posição 0 o número da coluna e na posição 1 o nome da coluna
                    }
                    echo "</tr>";

                    while ($linha = $resultado->fetch_array()) { //Obter as linhas do resultado
                        echo "<tr class='row'>";
                        for ($i = 0; $i < sizeof($campos); $i++) { //Para cada coluna, escrever o respetivo valor
                            $campo_nome = $campos[$i][1]; //Nome da coluna
                            $campo_valor = $linha[$campo_nome];
                            echo "<td class='textoTabela cell'>" . $campo_valor . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                }

                //---------------------------------------------------

                //Botão para exportar ficheiro XLSX (ligação para fazer download do ficheiro)
//                echo '<pre>'; print_r($tabelaGerada); echo '</pre>';
//                echo "BREAK<br>";
//                echo '<pre>'; print_r($keys); echo '</pre>';

                $filename = "resultado.xlsx";
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // $keys are for the header row.  If they are supplied we start writing at row 2
                if ($keys) {
                    $offset = substr_count($queryApresentada,"<br>")+2;
                } else {
                    $offset = substr_count($queryApresentada,"<br>")+1;
                }
                echo $offset."<br>";

                // write the rows
                $i = 0;
                foreach($tabelaGerada as $row) {
                    $spreadsheet->getActiveSheet()->fromArray($row, null, 'A' . ($i++ + $offset));
                }

                // write the header row from the $keys
                if ($keys) {
                    $spreadsheet->setActiveSheetIndex(0);
                    $spreadsheet->getActiveSheet()->fromArray($keys, null, 'A'.($offset-1));
                }

                // get last row and column for formatting
                $last_column = $spreadsheet->getActiveSheet()->getHighestColumn();
                $last_row = $spreadsheet->getActiveSheet()->getHighestRow();

                // autosize all columns to content width
                for ($i = 'A'; $i <= $last_column; $i++) {
                    $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
                }

                // if $keys, freeze the header row and make it bold
                if ($keys) {
                    $spreadsheet->getActiveSheet()->freezePane('A2');
                    $spreadsheet->getActiveSheet()->getStyle('A2:' . $last_column . '1')->getFont()->setBold(true);
                }

                // format all columns as text
                $spreadsheet->getActiveSheet()->getStyle('A2:' . $last_column . $last_row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $spreadsheet->getActiveSheet()->mergeCells("A1:".$last_column."1");
                $spreadsheet->getActiveSheet()->setCellValue("A1",str_replace("<br>","\n",$queryApresentada));
                $spreadsheet->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
                $writer = new Xlsx($spreadsheet);
                $writer->save($filename);
                if (file_exists(realpath($filename))) {
                    echo "<br><a href='".get_site_url() ."/".  $filename . "' download='resultado.xlsx'><button class='continuarButton textoLabels'>Exportar1</button></a>";
                }
            }
        } else { //Estado inicial
            echo "<div class='caixaSubTitulo'><h3>Pesquisa - escolher item</h3></div>";
            echo "<div class='caixaFormulario'>";
            //Lista dos tipos de itens e itens existentes na BD:
            echo "<ul>";
            $query = "SELECT name,id FROM item_type ORDER BY id"; //Query para obter todos os tipos de itens ordenados por id
            $tipoItens = mysqli_query($mySQL, $query);
            while ($tipoItem = mysqli_fetch_assoc($tipoItens)) { //Enquanto houver tipos de itens
                $query = "SELECT name,id FROM item WHERE item_type_id=" . $tipoItem["id"]; //Query para obter todos os itens associados ao tipo de item
                $itens = mysqli_query($mySQL, $query);
                //Condição para garantir que o tipo de item apenas é listado se possui itens associados e itens com subitens ativos:
                if (mysqli_num_rows($itens) > 0 && mysqli_num_rows(mysqli_query($mySQL, "SELECT id FROM subitem WHERE state='active' AND item_id IN (SELECT id FROM item WHERE item_type_id=" . $tipoItem["id"] . ")")) > 0) {
                    echo "<li>" . $tipoItem["name"] . "</li><ul>";
                    while ($item = mysqli_fetch_assoc($itens)) { //Enquanto houver itens associados ao tipo de item
                        //Condição para garantir que um item é listado apenas se possui pelo menos um subitem ativo:
                        if (mysqli_num_rows(mysqli_query($mySQL, "SELECT id FROM subitem WHERE state='active' AND item_id=" . $item["id"])) > 0) {
                            //Nome do item é um link para a próxima página (escolha), e também é passado o id do item: 
                            echo "<li>
							<a href='pesquisa?estado=escolha&item=" . $item["id"] . "'>[" . $item["name"] . "]</a>
							</li>";
                        }
                    }
                    echo "</ul>";
                }
            }
            echo "</ul>";
            echo "</div>";
        }
    }
} else { //Se o utilizador não está autenticado ou não tem a capability "manage_subitems" não pode aceder à página
    echo "<span class='warning'>Não tem autorização para aceder a esta página</span>";
}