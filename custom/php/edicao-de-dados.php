<?php
require_once("custom/php/common.php");
//echo "MUDOU3";
if (verificaCapability("manage_items")) { //SEM CAPABILITY?

    //ESTEBELECE LIGAÇÃO COM A BASE DE DADOS:
    $mySQL = ligacaoBD();

    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());

    } else {
        //SE O ESTADO DE EXECUÇÃO FOR "EDITAR":
        if ($_REQUEST["estado"] == "editar") {

            //SE ESCOLHEU EDITAR UM ITEM:
            if (!empty($_REQUEST["idItem"])) {

                //SUB-TITULO DA PAGINA:
                echo "<div class='caixaSubTitulo'><h3><strong>Edição de Dados - Editar Item</strong></h3></div>";
                //QUERY PARA ENCONTRAR VALORES DO ITEM ESCOLHIDO E DO SEU TIPO DE ITEM:
                $queryItem = "SELECT item.name as itemName, item.id, item.item_type_id, item_type.name as typeName, item.state  
                FROM item, item_type WHERE item.id ='" . $_REQUEST["idItem"] . "' AND item.item_type_id='" . $_REQUEST["tipoItem"] . "' 
                AND item.item_type_id=item_type.id";

                //RESULTADO DA QUERY:
                $tabelaItem = mysqli_query($mySQL, $queryItem);

                //PERCORRE A TABELA DO RESULTADO DA QUERY:
                while ($linhaItem = mysqli_fetch_assoc(($tabelaItem))) { //APENAS 1 RESULTADO (REFERENTE AO ITEM ESCOLHIDO)
                    //FORMULÁRIO PARA A ESCOLHA DOS NOVOS VALORES:
                    echo "<div class='caixaFormulario'><form method='post' >";

                    //TEXTBOX COM O NOME ANTERIOR DO ITEM ESCOLHIDO:
                    echo "<strong>Nome: </strong><br>
                    <input type='text' class='textInput' name='nome_item' id='nome_item' value='" . $linhaItem["itemName"] . "' ><br><br>";

                    //ESCOLHA DO TIPO DE ITEM:
                    echo "<br><strong>Tipo: </strong></br>";
                    //QUERY DE TODOS OS TIPOS DE ITENS:
                    $queryTipos = "SELECT * FROM item_type";
                    //RESULTADO DA QUERY:
                    $tabelaTipos = mysqli_query($mySQL, $queryTipos);

                    //PERCORRE A TABELA RESULTADO DA QUERY:
                    while ($linhaTipos = mysqli_fetch_assoc(($tabelaTipos))) {
                        //SE FOR O TIPO DE ITEM DO ITEM ESCOLHIDO (FICA CHECKED):
                        if ($linhaTipos["id"] == $_REQUEST["tipoItem"]) {
                            echo '<input  type="radio" name="tipo_item"  checked value=' . $linhaTipos["id"] . '><span class="textoLabels" >' . $linhaItem["typeName"] . '</span><br>';
                        } else { //OS RESTANTES TIPOS DE ITENS SÃO APRESENTADOS PARA ESCOLHA:
                            echo '<input  type="radio" name="tipo_item" value=' . $linhaTipos["id"] . '><span class="textoLabels" >' . $linhaTipos["name"] . '</span><br>';
                        }
                    }

                    //ESCOLHA DO ESTADO DO ITEM:
                    echo "<br><strong>Estado:</strong>";
                    //SE O VALOR DO ESTADO DO ITEM ERA ATIVO, FICA CHECKED:
                    echo "<input type='radio' id='at' value='active' name='estado_item' " . ($linhaItem["state"] == 'active' ? 'checked' : '') . "><span class='textoLabels' for='at'>ativo</span><br>";
                    //SE O VALOR DO ESTADO DO ITEM ERA INATIVO, FICA CHECKED:
                    echo "<input type='radio' id='inat' value='inactive' name='estado_item' " . ($linhaItem["state"] == 'inactive' ? 'checked' : '') . "><span for='inat' class='textoLabels'>inativo</span><br>
                    <input type='hidden' value='itemEditado' name='estado'>
                    <input class='submitButton textoLabels' type='submit' value='Editar Item' name='submit'>
                    </form></div>";
                    //AO CLICAR NO BOTÃO, O ESTADO DE EXECUÇÃO MUDA PARA "itemEditado"

                }
            //SE ESCOLHEU EDITAR UM VALOR PERMITIDO:
            } else if (!empty($_REQUEST["idValorPerm"])) {
                //SUBTITULO DA PAGINA:
                echo "<div class='caixaSubTitulo'><h3><strong>Edição de Dados - Editar Valor Permitido</strong></h3></div>";
                //QUERY PARA ENCONTRAR OS VALORES DO VALOR PERMITIDO E DO SEU SUBITEM
                $queryValorPermitido = "SELECT subitem_allowed_value.value as valorName, subitem_allowed_value.id, subitem_allowed_value.state, subitem.name as subitemName 
                FROM subitem_allowed_value, subitem 
                WHERE subitem_allowed_value.id ='" . $_REQUEST["idValorPerm"] . "' AND subitem_allowed_value.subitem_id = subitem.id 
                AND subitem.id='" . $_REQUEST["idSubitemValor"] . "'";

                //RESULTADO DA QUERY:
                $tabelaValorPermitido = mysqli_query($mySQL, $queryValorPermitido);

                //PERCORRE TABELA RESULTADO DA QUERY:
                while ($linhaValorPermitido = mysqli_fetch_assoc(($tabelaValorPermitido))) {
                    //FORMULARIO PARA ESCOLHA DOS NOVOS VALORES:
                    //TEXTBOX COM O NOME ANTERIOR DO VALOR PERMITIDO ESCOLHIDO:
                    echo "<div class='caixaFormulario'><form method='post' >
                    <strong>Nome: </strong><br>
                    <input type='text' class='textInput' name='valor' id='valor' value='" . $linhaValorPermitido["valorName"] . "' ><br><br>";


                    echo "<br><strong>Subitem: </strong></br>";
                    //QUERY DE TODOS OS SUBITENS:
                    $querySubitens = "SELECT * FROM subitem";
                    //RESULTADO DA QUERY:
                    $tabelaSubitens = mysqli_query($mySQL, $querySubitens);
                    //PERCORRE A TABELA RESULTADO DA QUERY:
                    while ($linhaSubitens = mysqli_fetch_assoc(($tabelaSubitens))) {
                        //SE FOR O SUBITEM DO VALOR PERMITIDO ESCOLHIDO, FICA CHECKED:
                        if ($linhaSubitens["id"] == $_REQUEST["idSubitemValor"]) {
                            echo '<input  type="radio" name="subitem_valor"  checked value=' . $linhaSubitens["id"] . '><span class="textoLabels" >' . $linhaValorPermitido["subitemName"] . '</span><br>';
                        } else { //COLOCA COMO OPÇÃO OS RESTANTES SUBITENS:
                            echo '<input  type="radio" name="subitem_valor" value=' . $linhaSubitens["id"] . '><span class="textoLabels" >' . $linhaSubitens["name"] . '</span><br>';
                        }
                    }
                    //ALTERAR O ESTADO DO VALOR PERMITIDO (O VALOR ANTERIOR FICA CHECKED):
                    echo "<br><strong>Estado:</strong>
                    <input type='radio' id='at' value='active' name='estado_valorper' " . ($linhaValorPermitido["state"] == 'active' ? 'checked' : '') . "><span class='textoLabels' for='at'>ativo</span><br>
                    <input type='radio' id='inat' value='inactive' name='estado_valorper' " . ($linhaValorPermitido["state"] == 'inactive' ? 'checked' : '') . "><span for='inat' class='textoLabels'>inativo</span><br>
                    <input type='hidden' value='valorPermEditado' name='estado'>
                    <input class='submitButton textoLabels' type='submit' value='Editar Valor Permitido' name='submit'>
                    </form></div>";
                    //AO CLICAR NO BOTÃO, O ESTADO DE EXECUÇÃO MUDA PARA "valorPermEditado"
                }
            //SE FOR ESCOLHIDO EDITAR UM SUBITEM:
            } else if (!empty($_REQUEST["idSubitem"])) {
                //SUBTITULO DA PAGINA:
                echo "<div class='caixaSubTitulo'><h3><strong>Edição de Dados - Editar Subitem</strong></h3></div>";
                //QUERY DOS VALORES DO SUBITEM ESCOLHIDO E DO SEU ITEM:
                $querySubitem = "SELECT subitem.id, subitem.name as subitemName, subitem.value_type, subitem.form_field_name, subitem.form_field_type, 
                subitem.unit_type_id, subitem.form_field_order, subitem.mandatory, subitem.state, item.id, item.name as itemName
                FROM subitem, item 
                WHERE subitem.id ='" . $_REQUEST["idSubitem"] . "' AND subitem.item_id = item.id AND item.id='" . $_REQUEST["idItemSubitem"] . "'";

                //RESULTADO DA QUERY:
                $tabelaSubitem = mysqli_query($mySQL, $querySubitem);

                //PERCORRE A TABELA RESULTADO DA QUERY:
                while ($linhaSubitem = mysqli_fetch_assoc(($tabelaSubitem))) {
                    //OBTEM VALORES ENUM DAS TABELAS SUBITEM (CAMPOS VALUE_TYPE E FORM_FIELD_TYPE):
                    $tipo_valores = get_enum_values("subitem", "value_type");
                    $tipo_camp_form = get_enum_values("subitem", "form_field_type");

                    //FORMULARIO PARA EDIÇÃO DOS VALORES:
                    //TEXTBOX COM O NOME ANTERIOR DO SUBITEM ESCOLHIDO:
                    echo "<div class='caixaFormulario'><form method='post' >
                    <strong>Nome: </strong><br>
                    <input type='text' class='textInput' name='nome_subitem' id='nome_subitem' value='" . $linhaSubitem["subitemName"] . "' ><br><br>";

                    //ESCOLHA DO TIPO DE VALOR:
                    echo "<strong>Tipo de valor: </strong><br>";
                    //PERCORRE CADA TIPO:
                    foreach ($tipo_valores as $val_tip) {
                        //SE FOR O TIPO DE VALOR DO SUBITEM ESCOLHIDO, FICA CHECKED:
                        if ($linhaSubitem["value_type"] == $val_tip) {
                            echo "<input type='radio' id='tipo_valor' checked value='" . $val_tip . "' name='tipo_valor'><span class='textoLabels'>" . $val_tip . "</span><br>";
                        } else { //COLOCA COMO OPÇÃO OS RESTANTES TIPOS DE VALOR:
                            echo "<input type='radio' id='tipo_valor' value='" . $val_tip . "' name='tipo_valor'><span class='textoLabels'>" . $val_tip . "</span><br>";
                        }
                    }

                    //ESCOLHA DO TIPO DE ITEM:
                    echo "<br><strong>Item: </strong></br>";
                    //QUERY DE TODOS OS ITENS:
                    $queryItens = "SELECT * FROM item";
                    //RESULTADO DA QUERY:
                    $tabelaItens = mysqli_query($mySQL, $queryItens);

                    //SELECT PARA ESCOLHA DO TIPO DE SUBITEM:
                    echo "<select name='item_subitem' id='item_subitem'  class='textInput textoLabels'>";
                    //PERCORRE A TABELA RESULTADO DA QUERY:
                    while ($linhaItens = mysqli_fetch_assoc(($tabelaItens))) {
                        //SE FOR O ITEM DO SUBITEM ESCOLHIDO, FICA SELECTED
                        if ($linhaItens["id"] == $_REQUEST["idItemSubitem"]) {
                            echo "<option selected value='" . $linhaItens["id"] . "'>" . $linhaSubitem["itemName"] . "</option>";
                        } else { //COLOCA OS RESTANTES ITENS COMO OPÇÃO:
                            echo "<option value='" . $linhaItens["id"] . "'>" . $linhaItens["name"] . "</option>";
                        }
                    }
                    echo '</select><br>';

                    //TEXTBOX COM O NOME DO CAMPO NO FORMULARIO ANTERIOR DO SUBITEM ESCOLHIDO:
                    echo "<strong>Nome do campo no formulário: </strong><br>
                    <input type='text' class='textInput' name='nome_campo' id='nome_campo' value='" . $linhaSubitem["form_field_name"] . "' ><br><br>";

                    //ESCOLHA DO TIPO DO CAMPO NO FORMULARIO:
                    echo "<strong>Tipo do campo no formulário: </strong><br>";
                    //PERCORRE TODOS OS TIPOS DE CAMPO NO FORMULARIO:
                    foreach ($tipo_camp_form as $camp_form_tip) {
                        //SE FOR O VALOR DO TIPO DE CAMPO NO FORMULARIO DO SUBITEM ESCOLHIDO, FICA CHECKED:
                        if ($linhaSubitem["form_field_type"] == $camp_form_tip) {
                            echo "<input type='radio' id='tipo_campo' checked value='" . $camp_form_tip . "' name='tipo_campo'><span class='textoLabels'>" . $camp_form_tip . "</span><br>";
                        } else { //COLOCA OS RESTANTES TIPOS COMO OPÇÃO:
                            echo "<input type='radio' id='tipo_campo' value='" . $camp_form_tip . "' name='tipo_campo'><span class='textoLabels'>" . $camp_form_tip . "</span><br>";
                        }
                    }

                    //ESCOLHA DO TIPO DE UNIDADE:
                    echo "<br><strong>Tipo de unidade: </strong></br>";
                    //QUERY DE TODOS OS TIPOS DE UNIDADES:
                    $queryTiposUnidades = "SELECT * FROM subitem_unit_type";
                    //RESULTADO DA QUERY:
                    $tabelaTiposUnidades = mysqli_query($mySQL, $queryTiposUnidades);

                    //SELECT PARA ESCOLHA DO TIPO DE UNIDADE:
                    echo "<select name='tipo_unidade' id='tipo_unidade'  class='textInput textoLabels'>";
                    //OPÇÃO VAZIO:
                    echo '<option value=""></option>';
                    //PERCORRE OS TIPOS DE UNIDADE:
                    while ($linhaTipoUnidade = mysqli_fetch_assoc(($tabelaTiposUnidades))) {
                        //SE O TIPO DE UNIDADE FOR O DO SUBITEM ESCOLHIDO, FICA SELECTED:
                        if ($linhaTipoUnidade["id"] == $linhaSubitem["unit_type_id"]) {
                            echo "<option selected value='" . $linhaTipoUnidade["id"] . "'>" . $linhaTipoUnidade["name"] . "</option>";
                        } else { //OS RESTANTES TIPOS FICAM COMO OPÇÃO:
                            echo "<option value='" . $linhaTipoUnidade["id"] . "'>" . $linhaTipoUnidade["name"] . "</option>";
                        }
                    }
                    echo '</select><br>';

                    //TEXTBOX COM A ORDEM DO CAMPO NO FORMULARIO ANTERIOR PREENCHIDO:
                    echo "<strong>Ordem do campo no formulário: </strong><br>
                    <input type='text' class='textInput' name='ordem_campo' id='ordem_campo' value='" . $linhaSubitem["form_field_order"] . "' ><br><br>";

                    //ESCOLHA DO VALOR DO MANDATORY (FICA CHECKED O VALOR ATUAL):
                    echo "<br><strong>Obrigatório:</strong>
                    <input type='radio' id='obrig' value='1' name='obrigatorio' " . ($linhaSubitem["mandatory"] == '1' ? 'checked' : '') . "><span class='textoLabels' for='obrig'>sim</span><br>
                    <input type='radio' id='n_obrig' value='0' name='obrigatorio' " . ($linhaSubitem["mandatory"] == '0' ? 'checked' : '') . "><span for='n_obrig' class='textoLabels'>não</span><br>";

                    //ESCOLHA DO ESTADO DO SUBITEM (FICA CHECKED O VALOR ATUAL):
                    echo "<br><strong>Estado:</strong>
                    <input type='radio' id='at' value='active' name='estado_subitem' " . ($linhaSubitem["state"] == 'active' ? 'checked' : '') . "><span class='textoLabels' for='at'>ativo</span><br>
                    <input type='radio' id='inat' value='inactive' name='estado_subitem' " . ($linhaSubitem["state"] == 'inactive' ? 'checked' : '') . "><span for='inat' class='textoLabels'>inativo</span><br>
                    <input type='hidden' value='subitemEditado' name='estado'>
                    <input class='submitButton textoLabels' type='submit' value='Editar Subitem' name='submit'>
                    </form></div>";
                    //APÓS CLICAR NO BOTÃO, O ESTADO DE EXECUÇÃO MUDA PARA "subitemEditado"

                }
            }
        } else if ($_REQUEST["estado"] == "ativar" || $_REQUEST["estado"] == "desativar") { //caso tenha escolhido ativar ou desativar um elemento
            if (!empty($_REQUEST["idItem"])) { //EDIÇÃO DE UM ITEM
                $elemento = "Item";
            }else if(!empty($_REQUEST["idValorPerm"])){ //EDIÇÃO DE UM VALOR PERMITIDO
                $elemento = "Valor Permitido";
            }else if(!empty($_REQUEST["idSubitem"])){ //EDIÇÃO DE UM SUBITEM
                $elemento = "Subitem";
            }
            $acao = ($_REQUEST["estado"] == "ativar" ? 'ativar' : 'desativar'); //VALOR DA AÇÃO A REALIZAR (ATIVAR OU DESATIVAR)

            //SUB-TITULO DA PAGINA:
            echo "<div class='caixaSubTitulo'><h3>Edição de Dados - ".($_REQUEST["estado"] == "ativar" ? 'Ativar ' : 'Desativar ')." " . $elemento . "</h3></div>";

            //FORMULÁRIO PARA CONFIRMAÇÃO DA ATIVAÇÃO/DESATIVAÇÃO DO ELEMENTO ESCOLHIDO:
            echo "<div class='caixaFormulario'><form method='post' >
                    <strong>Deseja ".$_REQUEST["estado"]." o ".$elemento."?</strong><br>
                    <input type='hidden' name='acao' value='".$acao."'>
                    <input type='hidden' name='estado' value='confirmado'>
                    <input class='submitButton textoLabels' type='submit' value='Confirmar' name='submit'>
                    </form></div>";
            //APÓS CONFIRMAR, O ESTADO DE EXECUÇÃO MUDA PARA "CONFIRMADO"

        } else if ($_REQUEST["estado"] == "confirmado") { //CASO O UTILIZADOR TENHA CONFIRMADO A ATIVAÇÃO/DESATIVAÇÃO
            //PARA EVITAR REPITIÇÃO DE CÓDIGO:
            if (!empty($_REQUEST["idItem"])) { ///EDIÇÃO DE UM ITEM
                $elemento = "Item";
                $tabela = "item";
                $href = "gestao-de-itens";
                $nome = "idItem";
            } else if (!empty($_REQUEST["idValorPerm"])) { //EDIÇÃO DE UM VALOR PERMITIDO
                $elemento = "Valor Permitido";
                $tabela = "subitem_allowed_value";
                $href = "gestao-de-valores-permitidos";
                $nome = "idValorPerm";
            } else if (!empty($_REQUEST["idSubitem"])) { //EDIÇÃO DE UM SUBITEM
                $elemento = "Subitem";
                $tabela = "subitem";
                $href = "gestao-de-subitens";
                $nome = "idSubitem";
            }

            //SUB-TITULO DA PAGINA:
            echo "<div class='caixaSubTitulo'><h3>Edição de Dados - ".($_REQUEST["acao"] == "ativar" ? 'Ativar ' : 'Desativar ')." " . $elemento . "</h3></div>";

            //AVISAR O UTILIZADOR SOBRE O SUCESSO/INSUCESSO DA OPERAÇÃO:
            echo "<div class='caixaFormulario'>";
            //ALTERAÇÃO DOS VALORES DO ELEMENTO ESCOLHIDO (ATUALIZAÇÃO COM OS VALORES ESCOLHIDOS ANTERIORMENTE)
            $insertQuery = "UPDATE " . $tabela . " SET state='" . ($_REQUEST["acao"] == "ativar" ? 'active' : 'inactive') . "' WHERE " . $tabela . ".id ='" . $_REQUEST[$nome] . "'";
            if (!mysqli_query($mySQL, $insertQuery)) {
                echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>"; //MOSTRA ERRO NO CÓDIGO SQL
            } else {
                echo "<span class='information'>" . ($_REQUEST["acao"] == "ativar" ? 'O ' . $elemento . ' foi ativado com sucesso' : 'O ' . $elemento . ' foi desativado com sucesso') . "
                <br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                echo "<a href='" . $href . "'><button class='continuarButton textoLabels'>Continuar</button></a>"; //BOTÃO PARA REGRESSAR À PAGINA INICIAL (ONDE ESTÁ APRESENTADA A TABELA)
            }
            echo "</div>";

        } else {
            //CASO O UTILIZADOR TENHA EDITADO UM ITEM
            if ($_REQUEST["estado"] == "itemEditado") {
                //SUB-TITULO DA PAGINA:
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Editar Item</h3></div>";
                //AVISO DO INSUCESSO/SUCESSO DA EDIÇÃO DOS DADOS:
                echo "<div class='caixaFormulario'>";
                $faltaDado = false; //TRUE SE FALTAR ALGUM DADO
                $campos = ""; //VAI JUNTANDO OS CAMPOS OBRIGATORIOS EM FALTA E LISTA-OS AO FINAL

                //NÃO ESCREVEU O NOME:
                if (empty($_REQUEST["nome_valor"])) {
                    $campos .= "<li><br><strong>Nome</strong></li>"; //JUNTA O NOME DO CAMPO EM FALTA (NOME)
                    $faltaDado = true; //FALTA UM DADO (NOME)
                }
                //SE TODOS OS CAMPOS OBRIGATORIOS FORAM PREENCHIDOS:
                if (!$faltaDado) {
                    //ALTERAÇÃO DOS VALORES DO ITEM ESCOLHIDO (ATUALIZAÇÃO COM OS VALORES ESCOLHIDOS ANTERIORMENTE)
                    $insertQuery = "UPDATE item SET name='" . testarInput($_REQUEST["nome_item"]) . "', item_type_id='" . $_REQUEST["tipo_item"] . "', state='" . $_REQUEST["estado_item"] . "' WHERE item.id ='" . $_REQUEST["idItem"] . "'";
                    if (!mysqli_query($mySQL, $insertQuery)) {
                        //MOSTRA ERRO NO CÓDIGO SQL:
                        echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                    } else {
                        //O UTILIZADOR É INFORMADO SOBRE O SUCESSO DA EDIÇÃO:
                        echo "<span class='information'>Alterou os dados do item com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                        //SE CLICAR NO BOTÃO, O UTILIZADOR VOLTA PARA A PAGINA DE GESTÃO DE ITENS:
                        echo "<a href='gestao-de-itens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                    }
                } else { //SE FALTOU ALGUM CAMPO OBRIGATÓRIO:
                    //SÃO LISTADOS OS NOMES DOS CAMPOS EM FALTA:
                    echo "<span class='warning'>Os seguintes campos são <strong>obrigatórios</strong></span>:<ul>" . $campos . "</ul>";
                    voltarAtras();
                }
                echo "</div>";

            //CASO O UTILIZADOR TENHA EDITADO UM VALOR PERMITIDO:
            } else if ($_REQUEST["estado"] == "valorPermEditado") {
                //SUB-TITULO DA PAGINA:
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Editar Valor Permitido</h3></div>";
                //AVISO DO INSUCESSO/SUCESSO DA EDIÇÃO DOS DADOS:
                echo "<div class='caixaFormulario'>";
                $faltaDado = false; //TRUE SE FALTAR ALGUM DADO
                $campos = ""; //VAI JUNTANDO OS CAMPOS OBRIGATORIOS EM FALTA E LISTA-OS AO FINAL

                //NÃO ESCREVEU O VALOR:
                if (empty($_REQUEST["valor"])) {
                    $campos .= "<li><br><strong>Valor</strong></li>"; //JUNTA O NOME DO CAMPO EM FALTA (VALOR)
                    $faltaDado = true; //FALTA UM DADO (VALOR)
                }

                //SE TODOS OS CAMPOS OBRIGATORIOS FORAM PREENCHIDOS:
                if (!$faltaDado) {
                    //ALTERAÇÃO DOS VALORES DO VALOR PERMITIDO ESCOLHIDO (ATUALIZAÇÃO COM OS VALORES ESCOLHIDOS ANTERIORMENTE)
                    $insertQuery = "UPDATE subitem_allowed_value SET value='" . testarInput($_REQUEST["valor"]) . "', subitem_id='" . $_REQUEST["subitem_valor"] . "', state='" . $_REQUEST["estado_valorper"] . "' WHERE id ='" . $_REQUEST["idValorPerm"] . "'";
                    if (!mysqli_query($mySQL, $insertQuery)) {
                        //MOSTRA ERRO NO CÓDIGO SQL:
                        echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                    } else {
                        //O UTILIZADOR É INFORMADO SOBRE O SUCESSO DA EDIÇÃO:
                        echo "<span class='information'>Alterou os dados do valor permitido com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                        //SE CLICAR NO BOTÃO, O UTILIZADOR VOLTA PARA A PAGINA DE GESTÃO DE VALORES PERMITIDOS:
                        echo "<a href='gestao-de-valores-permitidos'><button class='continuarButton textoLabels'>Continuar</button></a>";
                    }
                } else { //SE FALTOU ALGUM CAMPO OBRIGATÓRIO:
                    //SÃO LISTADOS OS NOMES DOS CAMPOS EM FALTA:
                    echo "<span class='warning'>Os seguintes campos são <strong>obrigatórios</strong></span>:<ul>" . $campos . "</ul>";
                    voltarAtras();
                }
                echo "</div>";

            //CASO O UTILIZADOR TENHA EDITADO UM SUBITEM:
            } else if ($_REQUEST["estado"] == "subitemEditado") {
                //SÃO TESTADOS OS INPUTS DO UTILIZADOR:
                $nome_subitem = testarInput($_REQUEST["nome_subitem"]);
                $tipo_valor = testarInput($_REQUEST["tipo_valor"]);
                $tipo_camp_form = testarInput($_REQUEST["tipo_campo"]);
                $tipo_unidade = testarInput($_REQUEST["tipo_unidade"]);
                $nome_campo = testarInput($_REQUEST["nome_campo"]);
                $ordem_campo_form = testarInput($_REQUEST["ordem_campo"]);
                $obrigatorio = testarInput($_REQUEST["obrigatorio"]);

                $houveErros = false; //TRUE SE HOUVEREM ERROS NOS VALORES INSERIDOS

                //CASO NÃO HOUVER PREENCHIDO O NOME DO SUBITEM, TIPO DE VALOR, TIPO DE CAMPO DO FORMULARIO OU ORDEM DO CAMPO DO FORMULARIO:
                if (empty($nome_subitem) || empty($tipo_valor) || empty($tipo_camp_form) || $ordem_campo_form == "") {
                    //É INFORMADO QUE NÃO PREENCHEU TODOS OS CAMPOS OBRIGATORIOS:
                    echo "<span class='warning textoLabels'>Não preencheu todos os campos obrigatórios!<br></span>";
                    $houveErros = True;
                }
                //CASO TENHA SIDO INSERIDO NÚMEROS NO NOME DO SUBITEM:
                if (1 === preg_match('~[0-9]~', $nome_subitem)) {
                    echo "<span class='warning textoLabels'>O nome do subitem não pode conter números!<br></span>";
                    $houveErros = True;
                }

                //CASO O NOME DO CAMPO NO FORMULÁRIO NÃO FOR UM CÓDIGO ASCII:
                if (preg_match('/[^\x20-\x7e]/', $nome_campo)) {
                    echo "<span class='warning textoLabels'>O nome do campo no formulário é um código ASCII!<br></span>";
                    $houveErros = True;
                }
                //CASO A ORDEM DO CAMPO NO FORMULÁRIO NÃO FOR UM NUMERO SUPERIOR A 0:
                if (!is_numeric($ordem_campo_form) || $ordem_campo_form <= 0) {
                    echo "<span class='warning textoLabels'>A ordem do campo no formulário tem que ser um número superior a 0!<br></span>";
                    $houveErros = True;
                }

                //SUBTITULO DA PAGINA:
                echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Editar Subitem</h3></div>";

                echo "<div class='caixaFormulario'>";
                //CASO NÃO HOUVER DADOS INVÁLIDOS OU EM FALTA:
                if ($houveErros) {
                    //BOTÃO PARA VOLTAR ATRÁS:
                    voltarAtras();
                } else {
                    $insertQuery = "UPDATE subitem SET name='" . $nome_subitem . "', value_type='" . $tipo_valor . "', item_id='" . $_REQUEST["item_subitem"] . "', 
                    form_field_name='" . $nome_campo . "', form_field_type='" . $tipo_camp_form . "', unit_type_id=";

                    //SE ESCOLHEU OPÇÃO VAZIA PARA O TIPO DE UNIDADE, COLOCA O VALOR NULL (SEM ' ')
                    if ($tipo_unidade == '') {
                        //ATUALIZAÇÃO DOS VALORES ESCOLHIDOS:
                        $insertQuery .= "NULL, form_field_order='" . $ordem_campo_form . "', mandatory='" . $obrigatorio . "', state='" . $_REQUEST["estado_subitem"] . "' 
                        WHERE id ='" . $_REQUEST["idSubitem"] . "'";

                    //SE ESCOLHEU UM TIPO DE UNIDADE EXISTENTE, COLOCA O RESPETIVO ID (COM ' ')
                    } else {
                        //ATUALIZAÇÃO DOS VALORES ESCOLHIDOS:
                        $insertQuery .= "'" . $tipo_unidade . "', form_field_order='" . $ordem_campo_form . "', mandatory='" . $obrigatorio . "', state='" . $_REQUEST["estado_subitem"] . "' 
                     WHERE id ='" . $_REQUEST["idSubitem"] . "'";
                    }

                    if (!mysqli_query($mySQL, $insertQuery)) {
                        //MENSAGEM DE ERRO NO CÓDIGO SQL
                        echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                    } else {
                        //O UTILIZADOR É INFORMADO SOBRE O SUCESSO DA EDIÇÃO:
                        echo "<span class='information'>Alterou os dados do subitem com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";

                        //AO CLICAR NO BOTÃO, O UTILIZADOR VOLTA PARA A PAGINA GESTÃO DE SUBITENS
                        echo "<a href='gestao-de-subitens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                    }
                }
                echo "</div>";
            }
        }
    }
    //SE O UTILIZADOR NÃO TEM A CAPABILITY:
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>
