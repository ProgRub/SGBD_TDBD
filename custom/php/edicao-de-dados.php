<?php
require_once("custom/php/common.php");
echo "MUDOU3";
if (verificaCapability("manage_items")) {

    $mySQL = ligacaoBD();

    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());

    } else {
        if ($_REQUEST["estado"] == "editar") {
            if (!empty($_REQUEST["idItem"])) { //edição de um item
                echo "<div class='caixaSubTitulo'><h3><strong>Edição de Dados - Editar Item</strong></h3></div>";
                $queryItem = "SELECT item.name as itemName, item.id, item.item_type_id, item_type.name as typeName, item.state  
                FROM item, item_type WHERE item.id ='" . $_REQUEST["idItem"] . "' AND item.item_type_id='" . $_REQUEST["tipoItem"] . "' 
                AND item.item_type_id=item_type.id"; //atributos do item que se deseja editar e do tipo desse item
                $tabelaItem = mysqli_query($mySQL, $queryItem);

                while ($linhaItem = mysqli_fetch_assoc(($tabelaItem))) {
                    echo "<div class='caixaFormulario'><form method='post' >
                    <strong>Nome: </strong><br>
                    <input type='text' class='textInput' name='nome_item' id='nome_item' value='" . $linhaItem["itemName"] . "' ><br><br>";

                    echo "<br><strong>Tipo: </strong></br>";
                    $queryTipos = "SELECT * FROM item_type"; //todos os tipos de itens
                    $tabelaTipos = mysqli_query($mySQL, $queryTipos);
                    while ($linhaTipos = mysqli_fetch_assoc(($tabelaTipos))) {
                        if ($linhaTipos["id"] == $_REQUEST["tipoItem"]) { //se for o tipo do item que se deseja editar (fica checked)
                            echo '<input  type="radio" name="tipo_item"  checked value=' . $linhaTipos["id"] . '><span class="textoLabels" >' . $linhaItem["typeName"] . '</span><br>';
                        } else {
                            echo '<input  type="radio" name="tipo_item" value=' . $linhaTipos["id"] . '><span class="textoLabels" >' . $linhaTipos["name"] . '</span><br>';
                        }
                    }
                    echo "<br><strong>Estado:</strong>
                    <input type='radio' id='at' value='active' name='estado_item' " . ($linhaItem["state"] == 'active' ? 'checked' : '') . "><span class='textoLabels' for='at'>ativo</span><br>
                    
                    <input type='radio' id='inat' value='inactive' name='estado_item' " . ($linhaItem["state"] == 'inactive' ? 'checked' : '') . "><span for='inat' class='textoLabels'>inativo</span><br>
                    
                    <input type='hidden' value='itemEditado' name='estado'>
                    <input class='submitButton textoLabels' type='submit' value='Editar Item' name='submit'>
                    </form></div>";
                }
            } else if (!empty($_REQUEST["idValorPerm"])) { //edição de um valor permitido

                echo "<div class='caixaSubTitulo'><h3><strong>Edição de Dados - Editar Valor Permitido</strong></h3></div>";
                $queryValorPermitido = "SELECT subitem_allowed_value.value as valorName, subitem_allowed_value.id, subitem_allowed_value.state, subitem.name as subitemName 
                FROM subitem_allowed_value, subitem 
                WHERE subitem_allowed_value.id ='" . $_REQUEST["idValorPerm"] . "' AND subitem_allowed_value.subitem_id = subitem.id 
                AND subitem.id='" . $_REQUEST["idSubitemValor"] . "'"; //todos os atributos daquele item

                $tabelaValorPermitido = mysqli_query($mySQL, $queryValorPermitido);

                while ($linhaValorPermitido = mysqli_fetch_assoc(($tabelaValorPermitido))) {
                    echo "<div class='caixaFormulario'><form method='post' >
                    <strong>Nome: </strong><br>
                    <input type='text' class='textInput' name='valor' id='valor' value='" . $linhaValorPermitido["valorName"] . "' ><br><br>";

                    echo "<br><strong>Subitem: </strong></br>";
                    $querySubitens = "SELECT * FROM subitem"; //todos os tipos de itens
                    $tabelaSubitens = mysqli_query($mySQL, $querySubitens);
                    while ($linhaSubitens = mysqli_fetch_assoc(($tabelaSubitens))) {
                        if ($linhaSubitens["id"] == $_REQUEST["idSubitemValor"]) { //se for o tipo do item que se deseja editar (fica checked)
                            echo '<input  type="radio" name="subitem_valor"  checked value=' . $linhaSubitens["id"] . '><span class="textoLabels" >' . $linhaValorPermitido["subitemName"] . '</span><br>';
                        } else {
                            echo '<input  type="radio" name="subitem_valor" value=' . $linhaSubitens["id"] . '><span class="textoLabels" >' . $linhaSubitens["name"] . '</span><br>';
                        }
                    }
                    echo "<br><strong>Estado:</strong>
                    <input type='radio' id='at' value='active' name='estado_valorper' " . ($linhaValorPermitido["state"] == 'active' ? 'checked' : '') . "><span class='textoLabels' for='at'>ativo</span><br>
                    <input type='radio' id='inat' value='inactive' name='estado_valorper' " . ($linhaValorPermitido["state"] == 'inactive' ? 'checked' : '') . "><span for='inat' class='textoLabels'>inativo</span><br>
                    <input type='hidden' value='valorPermEditado' name='estado'>
                    <input class='submitButton textoLabels' type='submit' value='Editar Valor Permitido' name='submit'>
                    </form></div>";
                }
            } else if (!empty($_REQUEST["idSubitem"])) { //edição de um subitem
                echo "<div class='caixaSubTitulo'><h3><strong>Edição de Dados - Editar Subitem</strong></h3></div>";
                $querySubitem = "SELECT subitem.id, subitem.name as subitemName, subitem.value_type, subitem.form_field_name, subitem.form_field_type, 
                subitem.unit_type_id, subitem.form_field_order, subitem.mandatory, subitem.state, item.id, item.name as itemName
                FROM subitem, item 
                WHERE subitem.id ='" . $_REQUEST["idSubitem"] . "' AND subitem.item_id = item.id AND item.id='" . $_REQUEST["idItemSubitem"] . "'";

                $tabelaSubitem = mysqli_query($mySQL, $querySubitem);

                while ($linhaSubitem = mysqli_fetch_assoc(($tabelaSubitem))) {
                    $tipo_valores = get_enum_values("subitem", "value_type");
                    $tipo_camp_form = get_enum_values("subitem", "form_field_type");

                    echo "<div class='caixaFormulario'><form method='post' >
                    <strong>Nome: </strong><br>
                    <input type='text' class='textInput' name='nome_subitem' id='nome_subitem' value='" . $linhaSubitem["subitemName"] . "' ><br><br>";
                    echo "<strong>Tipo de valor: </strong><br>";
                    foreach ($tipo_valores as $val_tip) {
                        if ($linhaSubitem["value_type"] == $val_tip) {
                            echo "<input type='radio' id='tipo_valor' checked value='" . $val_tip . "' name='tipo_valor'><span class='textoLabels'>" . $val_tip . "</span><br>";
                        } else {
                            echo "<input type='radio' id='tipo_valor' value='" . $val_tip . "' name='tipo_valor'><span class='textoLabels'>" . $val_tip . "</span><br>";
                        }
                    }

                    echo "<br><strong>Item: </strong></br>";
                    $queryItens = "SELECT * FROM item"; //todos os itens
                    $tabelaItens = mysqli_query($mySQL, $queryItens);
                    echo "<select name='item_subitem' id='item_subitem'  class='textInput textoLabels'>";
                    while ($linhaItens = mysqli_fetch_assoc(($tabelaItens))) {
                        if ($linhaItens["id"] == $_REQUEST["idItemSubitem"]) { //se for o item do subitem que se deseja editar (fica checked)
                            echo "<option selected value='" . $linhaItens["id"] . "'>" . $linhaSubitem["itemName"] . "</option>";
                        } else {
                            echo "<option value='" . $linhaItens["id"] . "'>" . $linhaItens["name"] . "</option>";
                        }
                    }
                    echo '</select><br>';

                    echo "<strong>Nome do campo no formulário: </strong><br>
                    <input type='text' class='textInput' name='nome_campo' id='nome_campo' value='" . $linhaSubitem["form_field_name"] . "' ><br><br>";

                    echo "<strong>Tipo do campo no formulário: </strong><br>";
                    foreach ($tipo_camp_form as $camp_form_tip) {
                        if ($linhaSubitem["form_field_type"] == $camp_form_tip) {
                            echo "<input type='radio' id='tipo_campo' checked value='" . $camp_form_tip . "' name='tipo_campo'><span class='textoLabels'>" . $camp_form_tip . "</span><br>";
                        } else {
                            echo "<input type='radio' id='tipo_campo' value='" . $camp_form_tip . "' name='tipo_campo'><span class='textoLabels'>" . $camp_form_tip . "</span><br>";
                        }
                    }

                    echo "<br><strong>Tipo de unidade: </strong></br>";
                    $queryTiposUnidades = "SELECT * FROM subitem_unit_type"; //todos os itens
                    $tabelaTiposUnidades = mysqli_query($mySQL, $queryTiposUnidades);
                    echo "<select name='tipo_unidade' id='tipo_unidade'  class='textInput textoLabels'>";
                    echo '<option value=""></option>';
                    while ($linhaTipoUnidade = mysqli_fetch_assoc(($tabelaTiposUnidades))) {
                        if ($linhaTipoUnidade["id"] == $linhaSubitem["unit_type_id"]) {
                            echo "<option selected value='" . $linhaTipoUnidade["id"] . "'>" . $linhaTipoUnidade["name"] . "</option>";
                        } else {
                            echo "<option value='" . $linhaTipoUnidade["id"] . "'>" . $linhaTipoUnidade["name"] . "</option>";
                        }
                    }
                    echo '</select><br>';

                    echo "<strong>Ordem do campo no formulário: </strong><br>
                    <input type='text' class='textInput' name='ordem_campo' id='ordem_campo' value='" . $linhaSubitem["form_field_order"] . "' ><br><br>";

                    echo "<br><strong>Obrigatório:</strong>
                    <input type='radio' id='obrig' value='1' name='obrigatorio' " . ($linhaSubitem["mandatory"] == '1' ? 'checked' : '') . "><span class='textoLabels' for='obrig'>sim</span><br>
                    <input type='radio' id='n_obrig' value='0' name='obrigatorio' " . ($linhaSubitem["mandatory"] == '0' ? 'checked' : '') . "><span for='n_obrig' class='textoLabels'>não</span><br>";


                    echo "<br><strong>Estado:</strong>
                    <input type='radio' id='at' value='active' name='estado_subitem' " . ($linhaSubitem["state"] == 'active' ? 'checked' : '') . "><span class='textoLabels' for='at'>ativo</span><br>
                    <input type='radio' id='inat' value='inactive' name='estado_subitem' " . ($linhaSubitem["state"] == 'inactive' ? 'checked' : '') . "><span for='inat' class='textoLabels'>inativo</span><br>
                    <input type='hidden' value='subitemEditado' name='estado'>
                    <input class='submitButton textoLabels' type='submit' value='Editar Subitem' name='submit'>
                    </form></div>";

                }
            }
        } else if ($_REQUEST["estado"] == "ativar") { //se escolheu ativar
            if (!empty($_REQUEST["idItem"])) { //edição de um item
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Ativar Item</h3></div>";
                echo "<div class='caixaFormulario'>";
                $insertQuery = "UPDATE item SET state='active' WHERE item.id ='" . $_REQUEST["idItem"] . "'";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                } else {
                    echo "<span class='information'>O item foi ativado com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                    echo "<a href='gestao-de-itens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }
                voltarAtras();
                echo "</div>";

            }
            else if (!empty($_REQUEST["idValorPerm"])) { //edição de um valor permitido
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Ativar Valor Permitido</h3></div>";
                echo "<div class='caixaFormulario'>";
                echo "<div class='caixaFormulario'><form method='post' >
                    <strong>Deseja ativar o valor permitido escolhido?</strong><br>
                    <input type='checkbox' name='ativar' id='ativar' value='ativar' ><label for='ativar'>Sim</label></label><br><br>
                    <input href='#' class='submitButton textoLabels' type='submit' value='Confirmar' name='submit'>
                    </form></div>";
                if($_REQUEST["ativar"] == 'ativar'){
                    $insertQuery = "UPDATE subitem_allowed_value SET state='active' WHERE subitem_allowed_value.id ='" . $_REQUEST["idValorPerm"] . "'";
                    if (!mysqli_query($mySQL, $insertQuery)) {
                        echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                    } else {
                        echo "<span class='information'>O valor permitido foi ativado com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                        echo "<a href='gestao-de-valores-permitidos'><button class='continuarButton textoLabels'>Continuar</button></a>";
                    }
                }else{
                    echo "<a href='gestao-de-valores-permitidos'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }
                echo "</div>";

            }
            else if (!empty($_REQUEST["idSubitem"])) { //edição de um subitem
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Ativar Valor Permitido</h3></div>";
                echo "<div class='caixaFormulario'>";
                $insertQuery = "UPDATE subitem SET state='active' WHERE subitem.id ='" . $_REQUEST["idSubitem"] . "'";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                } else {
                    echo "<span class='information'>O subitem foi ativado com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                    echo "<a href='gestao-de-subitens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }
                voltarAtras();
                echo "</div>";
            }

        } else if ($_REQUEST["estado"] == "desativar") { //se escolheu desativar
            if (!empty($_REQUEST["idItem"])) { //edição de um item
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Desativar Item</h3></div>";
                echo "<div class='caixaFormulario'>";
                $insertQuery = "UPDATE item SET state='inactive' WHERE item.id ='" . $_REQUEST["idItem"] . "'";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                } else {
                    echo "<span class='information'>O item foi desativado com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                    echo "<a href='gestao-de-itens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }
                voltarAtras();
                echo "</div>";

            }
            else if (!empty($_REQUEST["idValorPerm"])) { //edição de um valor permitido
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Desativar Valor Permitido</h3></div>";
                echo "<div class='caixaFormulario'>";
                $insertQuery = "UPDATE subitem_allowed_value SET state='inactive' WHERE subitem_allowed_value.id ='" . $_REQUEST["idValorPerm"] . "'";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                } else {
                    echo "<span class='information'>O valor permitido foi desativado com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                    echo "<a href='gestao-de-valores-permitidos'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }
                voltarAtras();
                echo "</div>";
            }
            else if (!empty($_REQUEST["idSubitem"])) { //edição de um subitem
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Desativar Valor Permitido</h3></div>";
                echo "<div class='caixaFormulario'>";
                $insertQuery = "UPDATE subitem SET state='inactive' WHERE subitem.id ='" . $_REQUEST["idSubitem"] . "'";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                } else {
                    echo "<span class='information'>O subitem foi desativado com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                    echo "<a href='gestao-de-subitens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }
                voltarAtras();
                echo "</div>";
            }
        } else {
            if ($_REQUEST["estado"] == "itemEditado") {
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Editar Item</h3></div>";
                echo "<div class='caixaFormulario'>";
                $faltaDado = false;
                $campos = "";
                if (empty($_REQUEST["nome_valor"])) { //não escreveu nome
                    $campos .= "<li><br><strong>Nome</strong></li>";
                    $faltaDado = true;
                }
                if (!$faltaDado) { //não falta preencher nenhum campo obrigatório
                    $insertQuery = "UPDATE item SET name='" . testarInput($_REQUEST["nome_item"]) . "', item_type_id='" . $_REQUEST["tipo_item"] . "', state='" . $_REQUEST["estado_item"] . "' WHERE item.id ='" . $_REQUEST["idItem"] . "'";
                    if (!mysqli_query($mySQL, $insertQuery)) {
                        echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                    } else {
                        echo "<span class='information'>Alterou os dados do item com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                        echo "<a href='gestao-de-itens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                    }
                } else {
                    echo "<span class='warning'>Os seguintes campos são <strong>obrigatórios</strong></span>:<ul>" . $campos . "</ul>";
                    voltarAtras();
                }
                echo "</div>";
            } else if ($_REQUEST["estado"] == "valorPermEditado") {
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Editar Valor Permitido</h3></div>";
                echo "<div class='caixaFormulario'>";
                $faltaDado = false;
                $campos = "";
                if (empty($_REQUEST["valor"])) { //não escreveu nome
                    $campos .= "<li><br><strong>Valor</strong></li>";
                    $faltaDado = true;
                }
                if (!$faltaDado) { //não falta preencher nenhum campo obrigatório
                    $insertQuery = "UPDATE subitem_allowed_value SET value='" . testarInput($_REQUEST["valor"]) . "', subitem_id='" . $_REQUEST["subitem_valor"] . "', state='" . $_REQUEST["estado_valorper"] . "' WHERE id ='" . $_REQUEST["idValorPerm"] . "'";
                    if (!mysqli_query($mySQL, $insertQuery)) {
                        echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                    } else {
                        echo "<span class='information'>Alterou os dados do valor permitido com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                        echo "<a href='gestao-de-valores-permitidos'><button class='continuarButton textoLabels'>Continuar</button></a>";
                    }
                } else {
                    echo "<span class='warning'>Os seguintes campos são <strong>obrigatórios</strong></span>:<ul>" . $campos . "</ul>";
                    voltarAtras();
                }
                echo "</div>";
            } else if ($_REQUEST["estado"] == "subitemEditado") {
                $nome_subitem = testarInput($_REQUEST["nome_subitem"]);
                $tipo_valor = testarInput($_REQUEST["tipo_valor"]);
                $tipo_camp_form = testarInput($_REQUEST["tipo_campo"]);
                $tipo_unidade = testarInput($_REQUEST["tipo_unidade"]);
                $nome_campo = testarInput($_REQUEST["nome_campo"]);
                $ordem_campo_form = testarInput($_REQUEST["ordem_campo"]);
                $obrigatorio = testarInput($_REQUEST["obrigatorio"]);

                if (empty($nome_subitem) || empty($tipo_valor) || empty($tipo_camp_form) || $ordem_campo_form == "") {
                    echo "<span class='warning textoLabels'>Não preencheu todos os campos obrigatórios!<br></span>";
                    $houveErros = True;
                }
                if (1 === preg_match('~[0-9]~', $nome_subitem)) {
                    echo "<span class='warning textoLabels'>O nome do subitem não pode conter números!<br></span>";
                    $houveErros = True;
                }
                if (preg_match('/[^\x20-\x7e]/', $nome_campo)) {
                    echo "<span class='warning textoLabels'>O nome do campo no formulário é um código ASCII!<br></span>";
                    $houveErros = True;
                }
                if (!is_numeric($ordem_campo_form) || $ordem_campo_form <= 0) {
                    echo "<span class='warning textoLabels'>A ordem do campo no formulário tem que ser um número superior a 0!<br></span>";
                    $houveErros = True;
                }

                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Editar Subitem</h3></div>";
                echo "<div class='caixaFormulario'>";
                if ($houveErros) {
                    voltarAtras();
                } else {
                    $insertQuery = "UPDATE subitem SET name='" . $nome_subitem . "', value_type='" . $tipo_valor . "', item_id='" . $_REQUEST["item_subitem"] . "', 
                    form_field_name='" . $nome_campo . "', form_field_type='" . $tipo_camp_form . "', unit_type_id=";
                    if ($tipo_unidade == '') { //se escolheu opção vazia, insere valor "NULL"
                        $insertQuery .= "NULL, form_field_order='" . $ordem_campo_form . "', mandatory='" . $obrigatorio . "', state='" . $_REQUEST["estado_subitem"] . "' 
                        WHERE id ='" . $_REQUEST["idSubitem"] . "'";
                    } else {
                        $insertQuery .= "'" . $tipo_unidade . "', form_field_order='" . $ordem_campo_form . "', mandatory='" . $obrigatorio . "', state='" . $_REQUEST["estado_subitem"] . "' 
                     WHERE id ='" . $_REQUEST["idSubitem"] . "'";
                    }
                    if (!mysqli_query($mySQL, $insertQuery)) {
                        echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                    } else {
                        echo "<span class='information'>Alterou os dados do subitem com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                        echo "<a href='gestao-de-subitens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                    }
                }
                echo "</div>";
            }
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>
