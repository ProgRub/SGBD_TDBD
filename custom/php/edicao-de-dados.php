<?php
require_once("custom/php/common.php");
//ESTEBELECE LIGAÇÃO COM A BASE DE DADOS:
$mySQL = ligacaoBD();

//MUDA A CONEÇÃO MYSQL E CASO SEJA FALSE, OCORREU UM ERRO:
if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
    die("Connection failed: " . mysqli_connect_error());

//SE NÃO OCORREU NENHUM ERRO NA MUDANÇA DE CONEÇÃO:
} else {
    //SE O ESTADO DE EXECUÇÃO FOR "EDITAR":
    if ($_REQUEST["estado"] == "editar") {

        //SE ESCOLHEU EDITAR UM ITEM:
        if ($_REQUEST["tipo"] == "item") {
            //VALIDAÇÃO CLIENT-SIDE:
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_itens.js', array('jquery'), 1.1, true);
            }
            //VARIAVEIS DE SESSÃO COM O ID E O TIPO:
            $_SESSION["tipo"] = $_REQUEST["tipo"];
            $_SESSION["id"] = $_REQUEST["id"];

            //SUB-TITULO DA PAGINA:
            echo "<div class='caixaSubTitulo'><h3><strong>Edição de Dados - Editar Item</strong></h3></div>";
            //QUERY PARA ENCONTRAR VALORES DO ITEM ESCOLHIDO E DO SEU TIPO DE ITEM:
            $queryItem = "SELECT item.name as itemName, item.id, item.item_type_id, item_type.name as typeName, item.state  
            FROM item, item_type WHERE item.id ='" . $_REQUEST["id"] . "' AND item.item_type_id=item_type.id";

            //RESULTADO DA QUERY:
            $tabelaItem = mysqli_query($mySQL, $queryItem);

            //PERCORRE A TABELA DO RESULTADO DA QUERY:
            while ($linhaItem = mysqli_fetch_assoc(($tabelaItem))) { //APENAS 1 RESULTADO (REFERENTE AO ITEM ESCOLHIDO)
                //FORMULÁRIO PARA A ESCOLHA DOS NOVOS VALORES:
                $action=get_site_url().'/'.$current_page;
                echo "<div class='caixaFormulario'><span class='warning'>* Campos obrigatórios</span><br><form method='post' action='$action'>";

                //TEXTBOX COM O NOME ANTERIOR DO ITEM ESCOLHIDO:
                echo "<strong>Nome<span class='warning'>*</span>:</strong><br>
                <input type='text' class='textInput' name='nome_item' id='nome_item' value='" . $linhaItem["itemName"] . "' ><br><br>";

                //ESCOLHA DO TIPO DE ITEM:
                echo "<br><strong>Tipo<span class='warning'>*</span>:</strong></br>";
                //QUERY DE TODOS OS TIPOS DE ITENS:
                $queryTipos = "SELECT * FROM item_type";
                //RESULTADO DA QUERY:
                $tabelaTipos = mysqli_query($mySQL, $queryTipos);

                //PERCORRE A TABELA RESULTADO DA QUERY:
                while ($linhaTipos = mysqli_fetch_assoc(($tabelaTipos))) {
                    //SE FOR O TIPO DE ITEM DO ITEM ESCOLHIDO (FICA CHECKED):
                    if ($linhaTipos["id"] == $linhaItem["item_type_id"]) {
                        echo '<input  type="radio" name="tipo_item"  checked value=' . $linhaTipos["id"] . '><span class="textoLabels" >' . $linhaItem["typeName"] . '</span><br>';
                    } else { //OS RESTANTES TIPOS DE ITENS SÃO APRESENTADOS PARA ESCOLHA:
                        echo '<input  type="radio" name="tipo_item" value=' . $linhaTipos["id"] . '><span class="textoLabels" >' . $linhaTipos["name"] . '</span><br>';
                    }
                }

                //ESCOLHA DO ESTADO DO ITEM:
                echo "<br><strong>Estado<span class='warning'>*</span>:</strong></br>";
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
        } else if ($_REQUEST["tipo"] == "valorPermitido") {
            //VALIDAÇÃO CLIENT-SIDE:
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_valores_permitidos.js', array('jquery'), 1.1, true);
            }
            //VARIAVEIS DE SESSÃO COM O ID E O TIPO:
            $_SESSION["tipo"] = $_REQUEST["tipo"];
            $_SESSION["id"] = $_REQUEST["id"];

            //SUBTITULO DA PAGINA:
            echo "<div class='caixaSubTitulo'><h3><strong>Edição de Dados - Editar Valor Permitido</strong></h3></div>";
            //QUERY PARA ENCONTRAR OS VALORES DO VALOR PERMITIDO E DO SEU SUBITEM
            $queryValorPermitido = "SELECT subitem_allowed_value.value as valorName, subitem_allowed_value.id, subitem_allowed_value.state, subitem.id as subitemId, subitem.name as subitemName 
            FROM subitem_allowed_value, subitem 
            WHERE subitem_allowed_value.id ='" . $_REQUEST["id"] . "' AND subitem.id = subitem_allowed_value.subitem_id";

            //RESULTADO DA QUERY:
            $tabelaValorPermitido = mysqli_query($mySQL, $queryValorPermitido);

            //PERCORRE TABELA RESULTADO DA QUERY:
            while ($linhaValorPermitido = mysqli_fetch_assoc(($tabelaValorPermitido))) {
                //VARIAVEL DE SESSÃO COM O ID DO SUBITEM:
                $_SESSION["subitemId"] = $linhaValorPermitido["subitemId"];

                //VARIAVEL DE SESSÃO COM O ESTADO DO VALOR PERMITIDO:
                $_SESSION["state"] = $linhaValorPermitido["state"];

                //FORMULARIO PARA ESCOLHA DOS NOVOS VALORES:
                //TEXTBOX COM O NOME ANTERIOR DO VALOR PERMITIDO ESCOLHIDO:
                $action=get_site_url().'/'.$current_page;
                echo "<div class='caixaFormulario'><span class='warning'>* Campos obrigatórios</span><br><form method='post' action='$action'>
                <strong>Valor<span class='warning'>*</span>:</strong><br>
                <input type='text' class='textInput' name='valor' id='valor_permitido' value='" . $linhaValorPermitido["valorName"] . "' ><br><br>";

                echo "<input type='hidden' value='valorPermEditado' name='estado'>
                <input class='submitButton textoLabels' type='submit' value='Editar Valor Permitido' name='submit'>
                </form></div>";
                //AO CLICAR NO BOTÃO, O ESTADO DE EXECUÇÃO MUDA PARA "valorPermEditado"
            }
        } else if ($_REQUEST["tipo"] == "subitem") {//SE FOR ESCOLHIDO EDITAR UM SUBITEM:
            //VALIDAÇÃO CLIENT-SIDE:
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_subitens.js', array('jquery'), 1.1, true);
            }
            //VARIAVEIS DE SESSÃO COM O ID E O TIPO:
            $_SESSION["tipo"] = $_REQUEST["tipo"];
            $_SESSION["id"] = $_REQUEST["id"];

            //SUBTITULO DA PAGINA:
            echo "<div class='caixaSubTitulo'><h3><strong>Edição de Dados - Editar Subitem</strong></h3></div>";
            //QUERY DOS VALORES DO SUBITEM ESCOLHIDO E DO SEU ITEM:
            $querySubitem = "SELECT subitem.name as subitemName, subitem.value_type, subitem.form_field_name, subitem.form_field_type, 
            subitem.unit_type_id, subitem.form_field_order, subitem.mandatory, subitem.state, item.id as itemId, item.name as itemName
            FROM subitem, item 
            WHERE subitem.id ='" . $_REQUEST["id"] . "' AND subitem.item_id = item.id";

            //RESULTADO DA QUERY:
            $tabelaSubitem = mysqli_query($mySQL, $querySubitem);

            //PERCORRE A TABELA RESULTADO DA QUERY:
            while ($linhaSubitem = mysqli_fetch_assoc(($tabelaSubitem))) {
                //OBTEM VALORES ENUM DAS TABELAS SUBITEM (CAMPOS VALUE_TYPE E FORM_FIELD_TYPE):
                $tipo_valores = get_enum_values("subitem", "value_type");
                $tipo_camp_form = get_enum_values("subitem", "form_field_type");

                //FORMULARIO PARA EDIÇÃO DOS VALORES:
                //TEXTBOX COM O NOME ANTERIOR DO SUBITEM ESCOLHIDO:
                $action=get_site_url().'/'.$current_page;
                echo "<div class='caixaFormulario'><span class='warning'>* Campos obrigatórios</span><br><form method='post' action='$action'>
                <strong>Nome<span class='warning'>*</span>:</strong><br>
                <input type='text' class='textInput' name='nome_subitem' id='nome_subitem' value='" . $linhaSubitem["subitemName"] . "' ><br><br>";

                //ESCOLHA DO TIPO DE VALOR:
                echo "<strong>Tipo de valor<span class='warning'>*</span>:</strong><br>";
                //PERCORRE CADA TIPO:
                foreach ($tipo_valores as $val_tip) {
                    //SE FOR O TIPO DE VALOR DO SUBITEM ESCOLHIDO, FICA CHECKED:
                    if ($linhaSubitem["value_type"] == $val_tip) {
                        echo "<input type='radio' id='tipo_valor' checked value='" . $val_tip . "' name='tipo_valor'><span class='textoLabels'>" . $val_tip . "</span><br>";
                    } else { //COLOCA COMO OPÇÃO OS RESTANTES TIPOS DE VALOR:
                        echo "<input type='radio' id='tipo_valor' value='" . $val_tip . "' name='tipo_valor'><span class='textoLabels'>" . $val_tip . "</span><br>";
                    }
                }

                //VARIAVEL DE SESSÃO COM O "value_type" INICIAL DO SUBITEM: (PARA VERIFICAR SE ERA "ENUM" E FOI ALTERADO OU NÃO NA PARTE DA ATUALIZAÇÃO DO SUBITEM):
                $_SESSION["value_type_subitem"] = $linhaSubitem["value_type"];


                //ESCOLHA DO TIPO DE ITEM:
                echo "<br><strong>Item<span class='warning'>*</span>:</strong></br>";
                //QUERY DE TODOS OS ITENS:
                $queryItens = "SELECT * FROM item";
                //RESULTADO DA QUERY:
                $tabelaItens = mysqli_query($mySQL, $queryItens);

                //SELECT PARA ESCOLHA DO TIPO DE SUBITEM:
                echo "<select name='item_subitem' id='item'  class='textInput textoLabels'>";
                //PERCORRE A TABELA RESULTADO DA QUERY:
                while ($linhaItens = mysqli_fetch_assoc(($tabelaItens))) {
                    //SE FOR O ITEM DO SUBITEM ESCOLHIDO, FICA SELECTED
                    if ($linhaItens["id"] == $linhaSubitem["itemId"]) {
                        echo "<option selected value='" . $linhaItens["id"] . "'>" . $linhaSubitem["itemName"] . "</option>";
                    } else { //COLOCA OS RESTANTES ITENS COMO OPÇÃO:
                        echo "<option value='" . $linhaItens["id"] . "'>" . $linhaItens["name"] . "</option>";
                    }
                }
                echo '</select><br><br>';

                //ESCOLHA DO TIPO DO CAMPO NO FORMULARIO:
                echo "<strong>Tipo do campo no formulário<span class='warning'>*</span>:</strong><br>";
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
                echo "<br><strong>Tipo de unidade:</strong></br>";
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
                echo '</select><br><br>';

                //TEXTBOX COM A ORDEM DO CAMPO NO FORMULARIO ANTERIOR PREENCHIDO:
                echo "<strong>Ordem do campo no formulário<span class='warning'>*</span>:</strong><br>
                <input type='text' class='textInput' name='ordem_campo' id='ordem_campo_form' value='" . $linhaSubitem["form_field_order"] . "' ><br>";

                //ESCOLHA DO VALOR DO MANDATORY (FICA CHECKED O VALOR ATUAL):
                echo "<br><strong>Obrigatório<span class='warning'>*</span>:</strong><br>
                <input type='radio' id='obrig' value='1' name='obrigatorio' " . ($linhaSubitem["mandatory"] == '1' ? 'checked' : '') . "><span class='textoLabels' for='obrig'>sim</span><br>
                <input type='radio' id='n_obrig' value='0' name='obrigatorio' " . ($linhaSubitem["mandatory"] == '0' ? 'checked' : '') . "><span for='n_obrig' class='textoLabels'>não</span><br>";

                //ESCOLHA DO ESTADO DO SUBITEM (FICA CHECKED O VALOR ATUAL):
                echo "<br><strong>Estado:</strong><br>
                <input type='radio' id='at' value='active' name='estado_subitem' " . ($linhaSubitem["state"] == 'active' ? 'checked' : '') . "><span class='textoLabels' for='at'>ativo</span><br>
                <input type='radio' id='inat' value='inactive' name='estado_subitem' " . ($linhaSubitem["state"] == 'inactive' ? 'checked' : '') . "><span for='inat' class='textoLabels'>inativo</span><br><br>
                <input type='hidden' value='subitemEditado' name='estado'>
                <input class='submitButton textoLabels' type='submit' value='Editar Subitem' name='submit'>
                </form></div>";
                //APÓS CLICAR NO BOTÃO, O ESTADO DE EXECUÇÃO MUDA PARA "subitemEditado"

            }
        } else if ($_REQUEST["tipo"] == "crianca") { //SE FOR ESCOLHIDO EDITAR UMA CRIANÇA:
            //VARIAVEL DE SESSÃO COM O ID DA CRIANÇA:
            $_SESSION["idCrianca"] = $_REQUEST["id"];

            echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Editar valores de criança</h3></div>";
            echo "<div class='caixaFormulario'>";

            //QUERY PARA OBTER TODOS OS VALORES DA CRIANÇA:
            $queryValores = "SELECT * FROM value WHERE child_id=" . $_REQUEST["id"];
            //TABELA RESULTADO DA EXECUÇÃO DO CODIGO SQL:
            $tabelaValores = mysqli_query($mySQL, $queryValores);


            echo"<ul>";
            //ARRAY PARA GUARDAR O NOME DOS SUBITENS CUJO TIPO DE CAMPO DO FORMULÁRIO É "checkbox" (À MEDIDA QUE SÃO LISTADOS):
            $subitensListados = array();
            //PERCORRE A TABELA RESULTADO DA EXECUÇÃO DO CODIGO SQL (VALORES DA CRIANÇA):
            while ($linhaValores = mysqli_fetch_assoc(($tabelaValores))) {
                //QUERY PARA OBTER O SUBITEM DO VALOR:
                $querySubitemValor = "SELECT * FROM subitem WHERE id=" . $linhaValores["subitem_id"];
                //RESULTADO DA EXECUÇÃO DA QUERY:
                $tabelaSubitemValor= mysqli_query($mySQL, $querySubitemValor);

                //ACEDE AO SUBITEM OBTIDO:
                while($subitemValor = mysqli_fetch_assoc(($tabelaSubitemValor))){
                    //SE AINDA NÃO FOI LISTADO (NOME DO SUBITEM NÃO ESTÁ NO ARRAY):
                    if(!in_array($subitemValor["name"], $subitensListados)) {
                        //É LISTADO O NOME DO SUBITEM COM UM LINK PARA A EDIÇÃO DO VALOR DAQUELE SUBITEM:
                        echo "<li><a href='edicao-de-dados?estado=editar&id=" . $linhaValores["id"] . "&tipo=valor'>[" . $subitemValor["name"] . "]</a></li>";

                        //SE O TIPO DE CAMPO DO FORMULARIO É "checkbox":
                        if($subitemValor["form_field_type"] == "checkbox"){
                            //INSERE O NOME DO SUBITEM NO ARRAY:
                            array_push($subitensListados, $subitemValor["name"]);
                            //NOTA: COMO É CHECKBOX, PODE TER VÁRIOS VALORES. DESTA FORMA, EVITA-SE QUE SEJA APRESENTADO O NOME DESSE SUBITEM VÁRIAS VEZES.
                        }
                    }
                }
            }
            echo"</ul>";
            echo "</div>";

        //SE FOR ESCOLHIDO EDITAR UMA CRIANÇA:
        } else if ($_REQUEST["tipo"] == "valor") {
            //VALIDAÇÃO CLIENT-SIDE:
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/insercao_valores.js', array('jquery'), 1.1, true);
            }

            //VARIAVEIS DE SESSÃO COM O ID E O TIPO:
            $_SESSION["tipo"] = $_REQUEST["tipo"];
            $_SESSION["id"] = $_REQUEST["id"];

            //SUBTITULO DA PAGINA:
            echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Editar Valor</h3></div>";

            $action=get_site_url().'/'.$current_page;
            echo "<div class='caixaFormulario'><span class='warning'>* Campos obrigatórios</span><br><form method='post' action='$action'>";

            //QUERY PARA OBTER O VALOR QUE SE PRETENDE EDITAR:
            $queryValor = "SELECT * FROM value WHERE child_id=" . $_SESSION["idCrianca"] ." AND id =".$_REQUEST["id"];
            //RESULTADO DA EXECUÇÃO DA QUERY:
            $tabelaValor = mysqli_query($mySQL, $queryValor);
            //VALOR QUE SE PRETENDE EDITAR:
            $valor = mysqli_fetch_assoc($tabelaValor);

            //VARIAVEL DE SESSÃO COM O ID DO SUBITEM:
            $_SESSION["subitemId"] = $valor["subitem_id"];

            //QUERY PARA OBTER O SUBITEM DO VALOR:
            $querySubitem = "SELECT * FROM subitem WHERE id=" .$valor["subitem_id"];
            //RESULTADO DA EXECUÇÃO DA QUERY:
            $tabelaSubitem = mysqli_query($mySQL, $querySubitem);
            //SUBITEM DO VALOR:
            $subitem = mysqli_fetch_assoc($tabelaSubitem);

            $idInput=0;
            echo "<strong>".$subitem["name"].($subitem["mandatory"] == 1 ? "<span class='warning'>*</span>" : "").":</strong><br>";

            //SE O "value_type" É ENUM E O "form_field_type" NÃO É TEXTO:
            if($subitem["value_type"] == "enum" && $subitem["form_field_type"] != "text"){
                //QUERY PARA OBTER TODOS OS VALORES PERMITIDOS DO SUBITEM DO VALOR:
                $queryValoresSubitem = "SELECT * FROM subitem_allowed_value WHERE subitem_id=" . $subitem["id"];
                //RESULTADO DA EXECUÇÃO DA QUERY:
                $tabelaValoresSubitem = mysqli_query($mySQL, $queryValoresSubitem);

                //SE O TIPO DE CAMPO DO FORMULARIO É "RADIO":
                if($subitem["form_field_type"] == "radio"){
                    //PERCORRE TODOS OS VALORES PERMITIDOS OBTIDOS:
                    while($valorPermitido = mysqli_fetch_assoc($tabelaValoresSubitem)) {
                        //SE O "value" DO VALOR PERMITIDO FOR IGUAL AO "value" DO VALOR A EDITAR:
                        if ($valorPermitido["value"] == $valor["value"]) {
                            //APRESENTA O "value" DO VALOR COMO OPÇÃO RADIO E FICA "CHECKED":
                            echo "<input type='radio'".($subitem["mandatory"] == 1 ? " id='$idInput'" : "")." checked value='" . $valorPermitido["value"] . "' name='value'><span class='textoLabels'>".$valorPermitido["value"]. "</span><br>";

                        //SE NÃO FOR IGUAL:
                        } else {
                            //APRESENTA OS RESTANTES VALORES COMO OPÇÃO RADIO:
                            echo "<input type='radio'".($subitem["mandatory"] == 1 ? " id='$idInput'" : "")." value='" . $valorPermitido["value"] . "' name='value'><span class='textoLabels'>" . $valorPermitido["value"] . "</span><br>";
                        }
                    }

                //SE O TIPO DE CAMPO DO FORMULARIO É "CHECKBOX":
                }else if($subitem["form_field_type"] == "checkbox"){
                    //QUERY PARA OBTER TODOS OS VALORES DA CRIANÇA:
                    $queryValor = "SELECT * FROM value WHERE child_id=" . $_SESSION["idCrianca"];
                    //RESULTADO DA EXECUÇÃO DA QUERY:
                    $tabelaValor = mysqli_query($mySQL, $queryValor);

                    //ARRAY COM TODOS OS VALORES DA CRIANÇA:
                    $valoresCriança = array();

                    //PERCORRE TABELA RESULTADO DA EXECUÇÃO DA QUERY (TODOS OS VALORES DA CRIANÇA):
                    while($linhaValoresCrianca = mysqli_fetch_assoc($tabelaValor)){
                        //INSERE O VALOR NO ARRAY:
                        array_push($valoresCriança, $linhaValoresCrianca["value"]);
                    }

                    //PARA DISTINGUIR O NOME DE CADA CHECKBOX (SERÁ INCREMENTADO APÓS MOSTRAR UMA CHECKBOX):
                    $numeroCheckbox = 0;

                    //PERCORRE TODOS OS VALORES PERMITIDOS DO SUBITEM DO VALOR:
                    while ($valorPermitido = mysqli_fetch_assoc($tabelaValoresSubitem)) {
                        //SE O VALOR PERMITIDO DO SUBITEM ESTÁ NO ARRAY COM TODOS OS VALORES DA CRIANÇA -> É UM VALOR DA CRIANÇA:
                        if (in_array($valorPermitido["value"], $valoresCriança)) {
                            //APRESENTA CHECKBOX COM VALOR DA CRIANÇA MARCADO COMO CHECKED:
                            echo "<input type='checkbox'".($subitem["mandatory"] == 1 ? " id='$idInput'" : "")." checked value='" . $valorPermitido["value"] . "' name='value" . $numeroCheckbox . "'><label class='textoLabels'>" . $valorPermitido["value"] . "</label><br>";

                        //SE O VALOR NÃO ESTÁ NO ARRAY -> NÃO É VALOR DA CRIANÇA:
                        } else {
                            //APRESENTA CHECKBOX COM O VALOR:
                            echo "<input type='checkbox'".($subitem["mandatory"] == 1 ? " id='$idInput'" : "")." value='" . $valorPermitido["value"] . "' name='value" . $numeroCheckbox . "'><label class='textoLabels'>" . $valorPermitido["value"] . "</label><br>";
                        }
                        //INCREMENTA O VALOR DE "$numeroCheckbox" PARA DISTINGUIR A CHECKBOX:
                        $numeroCheckbox++;
                    }
                    //GUARDA O NÚMERO DA ULTIMA CHECKBOX NUMA VARIAVEL DE SESSÃO:
                    $_SESSION["numeroCheckbox"] = $numeroCheckbox;
                    //$_REQUEST["checkbox"] = "true", PARA UTILIZAR DISTINGUIR DAS RESTANTES EDIÇÕES:
                    echo "<input type='hidden' value='true' name='checkbox'>";

                //SE O TIPO DE CAMPO DO FORMULARIO FOR "SELECTBOX":
                }else if($subitem["form_field_type"] == "selectbox"){
                    echo "<select name='value'".($subitem["mandatory"] == 1 ? " id='$idInput'" : "")." class='textInput textoLabels'>";
                    //PERCORRE TODOS OS VALORES PERMITIDOS DO SUBITEM DO VALOR:
                    while($valorPermitido = mysqli_fetch_assoc($tabelaValoresSubitem)) {
                        //SE O "value" DO VALOR PERMITIDO FOR IGUAL AO "value" DO VALOR A EDITAR:
                        if ($valorPermitido["value"] == $valor["value"]) {
                            //CRIA UMA OPÇÃO DE ESCOLHA COM O VALOR -> FICA SELECTED:
                            echo "<option id='value' selected value='" . $valorPermitido["value"] . "' name='value'> ".$valorPermitido["value"]."</option>";

                        //SE NÃO FOR IGUAL -> NÃO É O VALOR DA CRIANÇA:
                        } else {
                            //CRIA OPÇÃO DE ESCOLHA COM O VALOR:
                            echo "<option id='value' value='" . $valorPermitido["value"] . "' name='value'> ".$valorPermitido["value"]."</option>";
                        }
                    }
                    echo "</select><br>";
                }
            //SE (O TIPO DE VALOR É "INT","DOUBLE" OU "TEXT") E (O TIPO DE CAMPO DO FORMULARIO É "TEXT" OU "TEXTBOX"):
            }else if (($subitem["value_type"] == "int" || $subitem["value_type"] == "double" || $subitem["value_type"] == "text") && ($subitem["form_field_type"] == "text" || $subitem["form_field_type"] == "textbox")){
               //APRESENTA TEXTBOX JÁ PREENCHIDO COM O VALOR A EDITAR:
                echo "<input type='text' class='textInput' name='value'".($subitem["mandatory"] == 1 ? " id='$idInput'" : "")." value='" . $valor["value"] . "' ><br><br>";

            //SE O TIPO DE VALOR É "BOOL":
            }else if($subitem["value_type"] == "bool"){
                //SÃO APRESENTADAS DUAS OPÇÕES RADIO (A OPÇÃO CORRESPONDENTE AO VALOR DO "mandatory" FICA CHECKED):
                echo "<input type='radio'".($subitem["mandatory"] == 1 ? " id='$idInput'" : "")." value='verdadeiro' name='value' " . ($valor["value"] == 'verdadeiro' ? 'checked' : '') . "><span class='textoLabels'>Verdadeiro</span><br>
                <input type='radio'".($subitem["mandatory"] == 1 ? " id='$idInput'" : "")." value='falso' name='value' " . ($valor["value"] == 'falso' ? 'checked' : '') . "><span class='textoLabels'>Falso</span><br>";

            }
            echo "<input type='hidden' value='valorEditado' name='estado'>";
            echo "<input class='submitButton textoLabels' type='submit' value='Confirmar' name='submit'>";
            echo "</form>";
            echo "</div>";
            //AO CONFIRMAR A EDIÇÃO, O ESTADO DE EXECUÇÃO MUDA PARA "valorEditado":
        }

    //SE O ESTADO DE EXECUÇÃO FOR "ativar" OU "desativar":
    } else if ($_REQUEST["estado"] == "ativar" || $_REQUEST["estado"] == "desativar") { //caso tenha escolhido ativar ou desativar um elemento
        //VARIAVEIS DE SESSÃO COM O ID E O TIPO:
        $_SESSION["tipo"] = $_REQUEST["tipo"];
        $_SESSION["id"] = $_REQUEST["id"];

        if ($_REQUEST["tipo"] == "item") { //EDIÇÃO DE UM ITEM
            $elemento = "Item";
        } else if ($_REQUEST["tipo"] == "valorPermitido") { //EDIÇÃO DE UM VALOR PERMITIDO
            $elemento = "Valor Permitido";
        } else if ($_REQUEST["tipo"] == "subitem") { //EDIÇÃO DE UM SUBITEM
            $elemento = "Subitem";
        }
        $acao = ($_REQUEST["estado"] == "ativar" ? 'ativar' : 'desativar'); //VALOR DA AÇÃO A REALIZAR (ATIVAR OU DESATIVAR)

        //SUB-TITULO DA PAGINA:
        echo "<div class='caixaSubTitulo'><h3>Edição de Dados - " . ($_REQUEST["estado"] == "ativar" ? 'Ativar ' : 'Desativar ') . " " . $elemento . "</h3></div>";

        //FORMULÁRIO PARA CONFIRMAÇÃO DA ATIVAÇÃO/DESATIVAÇÃO DO ELEMENTO ESCOLHIDO:
        $action=get_site_url().'/'.$current_page;
        echo "<div class='caixaFormulario'><form method='post' action='$action'>
                <strong>Deseja " . $_REQUEST["estado"] . " o " . $elemento . "?</strong><br>
                <input type='hidden' name='acao' value='" . $acao . "'>
                <input type='hidden' name='estado' value='confirmado'>
                <input class='submitButton textoLabels' type='submit' value='Confirmar' name='submit'>
                </form></div>";
        //APÓS CONFIRMAR, O ESTADO DE EXECUÇÃO MUDA PARA "CONFIRMADO"


    //CASO O UTILIZADOR TENHA CONFIRMADO A ATIVAÇÃO/DESATIVAÇÃO
    } else if ($_REQUEST["estado"] == "confirmado") {
        //PARA EVITAR REPITIÇÃO DE CÓDIGO:
        if ($_SESSION["tipo"] == "item") { ///EDIÇÃO DE UM ITEM
            $elemento = "Item";
            $tabela = "item";
            $href = "gestao-de-itens";
        } else if ($_SESSION["tipo"] == "valorPermitido") { //EDIÇÃO DE UM VALOR PERMITIDO
            $elemento = "Valor Permitido";
            $tabela = "subitem_allowed_value";
            $href = "gestao-de-valores-permitidos";
        } else if ($_SESSION["tipo"] == "subitem") { //EDIÇÃO DE UM SUBITEM
            $elemento = "Subitem";
            $tabela = "subitem";
            $href = "gestao-de-subitens";
        }

        //SUB-TITULO DA PAGINA:
        echo "<div class='caixaSubTitulo'><h3>Edição de Dados - " . ($_REQUEST["acao"] == "ativar" ? 'Ativar ' : 'Desativar ') . " " . $elemento . "</h3></div>";

        //AVISAR O UTILIZADOR SOBRE O SUCESSO/INSUCESSO DA OPERAÇÃO:
        echo "<div class='caixaFormulario'>";
        //ALTERAÇÃO DOS VALORES DO ELEMENTO ESCOLHIDO (ATUALIZAÇÃO COM OS VALORES ESCOLHIDOS ANTERIORMENTE)
        $insertQuery = "UPDATE " . $tabela . " SET state='" . ($_REQUEST["acao"] == "ativar" ? 'active' : 'inactive') . "' WHERE " . $tabela . ".id ='" . $_SESSION["id"] . "'";
        if (!mysqli_query($mySQL, $insertQuery)) {
            echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>"; //MOSTRA ERRO NO CÓDIGO SQL
        } else {
            echo "<span class='information'>" . ($_REQUEST["acao"] == "ativar" ? 'O ' . $elemento . ' foi ativado com sucesso' : 'O ' . $elemento . ' foi desativado com sucesso') . "
            <br>Clique em <strong>Continuar</strong> para avançar.<br></span>";

            //BOTÃO PARA REGRESSAR À PAGINA INICIAL (ONDE ESTÁ APRESENTADA A TABELA):
            echo "<a href='" . $href . "'><button class='continuarButton textoLabels'>Continuar</button></a>";
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
            if (empty($_REQUEST["nome_item"])) {
                $campos .= "<li><strong>Nome</strong></li>"; //JUNTA O NOME DO CAMPO EM FALTA (NOME)
                $faltaDado = true; //FALTA UM DADO (NOME)
            }
            //SE TODOS OS CAMPOS OBRIGATORIOS FORAM PREENCHIDOS:
            if (!$faltaDado) {
                //ALTERAÇÃO DOS VALORES DO ITEM ESCOLHIDO (ATUALIZAÇÃO COM OS VALORES ESCOLHIDOS ANTERIORMENTE)
                $insertQuery = "UPDATE item SET name='" . testarInput($_REQUEST["nome_item"]) . "', item_type_id='" . testarInput($_REQUEST["tipo_item"]) . "', state='" . testarInput($_REQUEST["estado_item"]) . "' WHERE item.id ='" . $_SESSION["id"] . "'";
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
                $campos .= "<li><strong>Valor</strong></li>"; //JUNTA O NOME DO CAMPO EM FALTA (VALOR)
                $faltaDado = true; //FALTA UM DADO (VALOR)
            }

            //SE TODOS OS CAMPOS OBRIGATORIOS FORAM PREENCHIDOS:
            if (!$faltaDado) {
                //ALTERAÇÃO DOS VALORES DO VALOR PERMITIDO ESCOLHIDO (ATUALIZAÇÃO COM OS VALORES ESCOLHIDOS ANTERIORMENTE)
                $insertQuery = "UPDATE subitem_allowed_value SET value='" . testarInput($_REQUEST["valor"]) . "', subitem_id='" . $_SESSION["subitemId"] . "', state='" . $_SESSION["state"] . "' WHERE id ='" . $_SESSION["id"] . "'";
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
            $ordem_campo_form = testarInput($_REQUEST["ordem_campo"]);
            $obrigatorio = testarInput($_REQUEST["obrigatorio"]);

            $queryTipos = "SELECT * FROM item WHERE id=".$_REQUEST["item_subitem"];
            //RESULTADO DA QUERY:
            $tabelaTipos = mysqli_query($mySQL, $queryTipos);
            //PERCORRE A TABELA RESULTADO DA QUERY:
            $tipoItem = mysqli_fetch_assoc(($tabelaTipos));

            //REGRAS PARA TIRAR ACENTOS A UMA STRING:
            $tirarAcento = Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', Transliterator::FORWARD);

            //TRANSFORMA NOME DO ITEM -> APLICA REGRA ANTERIOR:
            $itemSemAcento = $tirarAcento->transliterate($tipoItem["name"]);

            //SUBSTRING (3 LETRAS) DO NOME DO ITEM:
            $tresPrimeirasLetrasItem = substr($itemSemAcento, 0, 3);

            //TRANSFORMA NOME DO SUBITEM NUMA CADEIA DE CARATERES ASCII:
            $subitem_ascii = preg_replace('/[^a-z0-9_ ]/i', '', $nome_subitem);

            //SUBSTITUI OS ESPAÇOS POR "_" NA CADEIA DE CARATERES:
            $subitemSemCaracteresVazios = str_replace(" ", "_", $subitem_ascii);

            //JUNTA AS 3 PRIMEIRAS LETRAS DO NOME DO ITEM COM O ID DO ITEM E O NOME DO SUBITEM:
            $nome_campo_form = $tresPrimeirasLetrasItem . "-" . $_SESSION["id"] . "-" . $subitemSemCaracteresVazios;

            //TRUE SE HOUVEREM ERROS NOS VALORES INSERIDOS:
            $houveErros = false;

            //SUBTITULO DA PAGINA:
            echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Editar Subitem</h3></div>";

            echo "<div class='caixaFormulario'>";

            //CASO NÃO HOUVER PREENCHIDO O NOME DO SUBITEM, TIPO DE VALOR, TIPO DE CAMPO DO FORMULARIO OU ORDEM DO CAMPO DO FORMULARIO:
            //JUNTA OS NOMES DE TODOS OS CAMPOS EM FALTA NUMA LISTA
            $campos = "";
            if (empty($nome_subitem)) {
                $campos .= "<li><strong>Nome do subitem</strong></li>";
                $houveErros = true;

            //SE O NOME DA CRIANÇA OU DO TUTOR CONTÉM NÚMEROS:
            } else if (1 === preg_match('~[0-9]~', $nome_subitem) || 1 === preg_match('~[0-9]~', $nome_subitem)) {
                $campos .= "<li><strong>O nome do subitem não pode conter números!</strong></li>";
                $houveErros = true;
            }
            //SE O TIPO DE VALOR NÃO FOI PREENCHIDO:
            if (empty($tipo_valor)) {
                $campos .= "<li><strong>Tipo de valor</strong></li>";
                $houveErros = true;
            }
            //SE O TIPO DO CAMPO DO FORMULARIO NÃO FOI PREENCHIDO:
            if (empty($tipo_camp_form)) {
                $campos .= "<li><strong>Tipo do campo do formulário</strong></li>";
                $houveErros = true;
            }
            //SE A ORDEM DO CAMPO DO FORMULARIO NÃO FOI PREENCHIDA:
            if (empty($ordem_campo_form)) {
                $campos .= "<li><strong>Ordem do campo no formulário</strong></li>";
                $houveErros = true;
            //SE A ORDEM DO CAMPO DO FORMULARIO NÃO FOR UM NÚMERO OU FOR UM NUMERO MAS INFERIOR OU IGUAL A 0:
            } else if (!is_numeric($ordem_campo_form) || $ordem_campo_form <= 0) {
                $campos .= "<li><strong>A ordem do campo no formulário tem que ser um número superior a 0!</strong></li>";
                $houveErros = True;
            }
            //SE NÃO ESCOLHEU O CAMPO "OBRIGATORIO":
            if ($obrigatorio=="") {
                $campos .= "<li><strong>Obrigatório</strong></li>";
                $houveErros = true;
            }

            //CASO HOUVER DADOS INVÁLIDOS OU EM FALTA:
            if ($houveErros) {
                //LISTA OS NOMES DOS CAMPOS EM FALTA:
                echo "<span class='warning'>Os seguintes campos são <strong>obrigatórios</strong></span>:<ul>" . $campos . "</ul>";
                //BOTÃO PARA VOLTAR ATRÁS:
                voltarAtras();

            //CASO NÃO HOUVER DADOS INVÁLIDOS OU EM FALTA:
            } else {
                //INICIO DO CODIGO PARA ATUALIZAÇÃO DO SUBITEM:
                $updateSQL = "UPDATE subitem SET name='" . $nome_subitem . "', value_type='" . $tipo_valor . "', item_id='" . $_REQUEST["item_subitem"] . "', 
                form_field_name='" . $nome_campo_form . "', form_field_type='" . $tipo_camp_form . "', unit_type_id=";

                //SE ESCOLHEU OPÇÃO VAZIA PARA O TIPO DE UNIDADE, COLOCA O VALOR NULL (SEM ' '):
                if ($tipo_unidade == '') {
                    //FINAL DO CODIGO PARA ATUALIZAÇÃO DO SUBITEM (SEM TIPO DE UNIDADE):
                    $updateSQL .= "NULL, form_field_order='" . $ordem_campo_form . "', mandatory='" . $obrigatorio . "', state='" . $_REQUEST["estado_subitem"] . "' 
                    WHERE id ='" . $_SESSION["id"] . "'";

                    //SE ESCOLHEU UM TIPO DE UNIDADE EXISTENTE, COLOCA O RESPETIVO ID (COM ' '):
                } else {
                    //FINAL DO CODIGO PARA ATUALIZAÇÃO DO SUBITEM (COM TIPO DE UNIDADE):
                    $updateSQL .= "'" . $tipo_unidade . "', form_field_order='" . $ordem_campo_form . "', mandatory='" . $obrigatorio . "', state='" . $_REQUEST["estado_subitem"] . "' 
                 WHERE id ='" . $_SESSION["id"] . "'";
                }

                //EXECUTA ATUALIZAÇÃO NA BASE DE DADOS (SE OCORREU UM ERRO DEVOLVE FALSE):
                if (!mysqli_query($mySQL, $updateSQL)) {
                    //MENSAGEM DE ERRO NO CÓDIGO SQL
                    echo "<span class='warning'>Erro: " . $updateSQL . "<br>" . mysqli_error($mySQL) . "</span>";

                //SE NÃO OCORREU NENHUM ERRO:
                } else {
                    //TRUE SE OCORREU ALGUM ERRO NOS CODIGOS SQL:
                    $erro = false;

                    //SE O VALUE_TYPE ERA "ENUM" E FOI ALTERADO:
                    if($_SESSION["value_type_subitem"] == "enum" && $tipo_valor != "enum") {
                        //CODIGO SQL PARA APAGAR TODOS OS TUPLOS DA TABELA "value" DESSE SUBITEM:
                        $deleteSQL = "DELETE FROM value WHERE value IN (SELECT value FROM subitem_allowed_value WHERE subitem_id ='" . $_SESSION["id"] . "')";

                        //EXECUTA ELIMINAÇÃO NA BASE DE DADOS (SE OCORREU UM ERRO DEVOLVE FALSE):
                        if (!mysqli_query($mySQL, $deleteSQL)) {
                            //MENSAGEM DE ERRO NO CÓDIGO SQL:
                            echo "<span class='warning'>Erro: " . $deleteSQL . "<br>" . mysqli_error($mySQL) . "</span>";
                            $erro = true;
                        }

                        //CODIGO SQL PARA APAGAR TODOS OS TUPLOS DA TABELA "subitem_allowed_value" DESSE SUBITEM:
                        $deleteSQL = "DELETE FROM subitem_allowed_value WHERE subitem_id ='" . $_SESSION["id"] . "'";
                        if (!mysqli_query($mySQL, $deleteSQL)) {
                            //MENSAGEM DE ERRO NO CÓDIGO SQL
                            echo "<span class='warning'>Erro: " . $deleteSQL . "<br>" . mysqli_error($mySQL) . "</span>";
                            $erro = true;
                        }
                    }
                    //SE NÃO OCORREU NENHUM ERRO:
                    if(!$erro){
                        //O UTILIZADOR É INFORMADO SOBRE O SUCESSO DA EDIÇÃO:
                        echo "<span class='information'>Alterou os dados do subitem com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";

                        //AO CLICAR NO BOTÃO, O UTILIZADOR VOLTA PARA A PAGINA GESTÃO DE SUBITENS
                        echo "<a href='gestao-de-subitens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                    }

                }
            }
            echo "</div>";

        //SE O ESTADO DE EXECUÇÃO É "valorEditado" -> (O VALOR DE UMA CRIANÇA FOI EDITADO):
        }else if ($_REQUEST["estado"] == "valorEditado") {
            //SUB-TITULO DA PAGINA:
            echo "<div class='caixaSubTitulo'><h3>Edição de Dados - Editar Valor</h3></div>";
            echo "<div class='caixaFormulario'>";

            //TRUE SE FALTAR ALGUM DADO:
            $faltaDado = false;

            //VAI JUNTANDO OS CAMPOS OBRIGATORIOS EM FALTA OU COM ERROS E LISTA-OS AO FINAL (SE FALTAR ALGUM):
            $campos = "";

            //SE O "form_field_type" NÃO É "checkbox":
            if(empty($_REQUEST["checkbox"])){
                //SE NÃO PREENCHEU O VALOR:
                if (empty($_REQUEST["value"])) {
                    //JUNTA O NOME DO CAMPO EM FALTA (Valor):
                    $campos .= "<li><br><strong>Valor</strong></li>";
                    $faltaDado = true;
                }

                //SE TODOS OS CAMPOS OBRIGATORIOS FORAM PREENCHIDOS:
                if (!$faltaDado) {
                    //CODIGO SQL PARA ATUALIZAÇÃO DO VALOR:
                    $updateSQL = "UPDATE value SET value='" . testarInput($_REQUEST["value"]) . "', date='".date("Y-m-d")."', time='".date("H:i:s")."', producer='".wp_get_current_user()->user_login."' WHERE id ='" . $_SESSION["id"] . "'";

                    //EXECUTA CÓDIGO SQL (SE OCORRER UM ERRO DEVOLVE FALSE):
                    if (!mysqli_query($mySQL, $updateSQL)) {
                        //MOSTRA ERRO NO CÓDIGO SQL:
                        echo "<span class='warning'>Erro: " . $updateSQL . "<br>" . mysqli_error($mySQL) . "</span>";

                    //SE NÃO OCORREU NENHUM ERRO:
                    } else {
                        //O UTILIZADOR É INFORMADO SOBRE O SUCESSO DA EDIÇÃO:
                        echo "<span class='information'>Alterou o valor da criança com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                        //SE CLICAR NO BOTÃO, O UTILIZADOR VOLTA PARA A PAGINA DE GESTÃO DE ITENS:
                        echo "<a href='insercao-de-valores'><button class='continuarButton textoLabels'>Continuar</button></a>";
                    }

                //SE FALTOU ALGUM CAMPO OBRIGATÓRIO:
                } else {
                    //SÃO LISTADOS OS NOMES DOS CAMPOS EM FALTA:
                    echo "<span class='warning'>Os seguintes campos são <strong>obrigatórios</strong></span>:<ul>" . $campos . "</ul>";
                    //BOTÃO PARA VOLTAR ATRÁS:
                    voltarAtras();
                }
                echo "</div>";

            //SE O "form_field_type" É "checkbox":
            }else{
                //FALSE NÃO OCORRER ALGUM ERRO NO CODIGO SQL:
                $semErros = true;

                //INÍCIO DE TRANSAÇÃO
                $query = "START TRANSACTION;";
                if (!mysqli_query($mySQL, $query)) {
                    echo "<span class='warning'>Erro: " . $query . "<br>" . mysqli_error($mySQL) . "</span><br>";
                    $semErros = false;
                }

                //ARRAY COM OS VALORES QUE O UTILIZADOR ESCOLHEU:
                $valoresEscolhidos = array();
                //PERCORRE TODOS OS VALORES LISTADOS COMO OPÇÃO:
                for ($i = $_SESSION["numeroCheckbox"]; $i >= 0; $i--) {
                    //SE O VALOR NA POSIÇÃO $i DO ARRAY $_REQUEST NÃO É VAZIO, ENTÃO FOI ESCOLHIDO -> (OS VALORES QUE NÃO FORAM ESCOLHIDO FICAM VAZIOS):
                    if(!empty($_REQUEST["value" . $i])) {
                        //INSERE NO ARRAY O VALOR ESCOLHIDO (MARCADO COMO CHECKED):
                        array_push($valoresEscolhidos, $_REQUEST["value". $i]);
                    }
                }

                //CODIGO SQL PARA OBTER TODOS OS VALORES DA CRIANÇA:
                $queryValores = "SELECT * FROM value WHERE child_id=" . $_SESSION["idCrianca"];
                //RESULTADO DA EXECUÇÃO DO CODIGO SQL:
                $tabelaValores = mysqli_query($mySQL, $queryValores);

                //PERCORRE A TABELA RESULTADO DA EXECUÇÃO DO CODIGO SQL:
                while($valoresCriança = mysqli_fetch_assoc($tabelaValores)){

                    //SE O VALOR DA CRIANÇA ESTÁ NA BASE DE DADOS MAS NÃO FOI ESCOLHIDO -> FEZ "UNCHECK" DO VALOR:
                    if(!in_array($valoresCriança["value"], $valoresEscolhidos)){
                        //CODIGO SQL PARA ELIMINAÇÃO DO TUPLO CORRESPONDENTE AO VALOR UNCHECKED:
                        $deleteQuery = "DELETE FROM value WHERE id ='" . $valoresCriança["id"] . "'";
                        //EXECUTA CODIGO SQL (SE OCORREU UM ERRO DEVOLVE FALSE):
                        if (!mysqli_query($mySQL, $deleteQuery)) {
                            //MENSAGEM DE ERRO NO CÓDIGO SQL:
                            echo "<span class='warning'>Erro: " . $deleteQuery . "<br>" . mysqli_error($mySQL) . "</span>";

                            //ELIMINA O VALOR DA LISTA DE VALORES ESCOLHIDOS NA POSIÇÃO DEVOLVIDO PELO "array_search":
                            unset($valoresEscolhidos[array_search($valoresCriança["value"],$valoresEscolhidos)]);
                            $semErros = false;
                        }
                    //SE NÃO DESMARCOU O VALOR DA CRIANÇA (QUE JÁ ESTAVA PREENCHIDO NO FORMULÁRIO):
                    }else{
                        //ELIMINA O VALOR DA LISTA DE VALORES ESCOLHIDOS NA POSIÇÃO DEVOLVIDO PELO "array_search":
                        unset($valoresEscolhidos[array_search($valoresCriança["value"],$valoresEscolhidos)]);
                    }
                }

                //PERCORRE ARRAY COM OS VALORES ESCOLHIDOS (MARCADOS COMO CHECKED) E DIFERENTES DOS VALORES QUE JÁ ESTAVAM MARCADOS:
                foreach($valoresEscolhidos as $inserir){
                    //CODIGO SQL PARA INSERÇÃO DOS TUPLOS COM OS NOVOS VALORES MARCADOS COMO "CHECKED":
                    $insertQuery = "INSERT INTO `value` (`id`, `child_id`, `subitem_id`, `value`, `date`, `time`, `producer`) VALUES (NULL,'" . $_SESSION["idCrianca"] . "','" . $_SESSION["subitemId"] . "', '" . $inserir . "', '" . date("Y-m-d") . "', '" . date("H:i:s") . "','" . wp_get_current_user()->user_login . "')";

                    //EXECUTA CODIGO SQL (SE OCORREU UM ERRO, DEVOLVE FALSE):
                    if (!mysqli_query($mySQL, $insertQuery)) {
                        //MENSAGEM DE ERRO NO CÓDIGO SQL
                        echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                        $semErros = false;
                    }
                }

                //SE NÃO HOUVE ERROS ANTERIORMENTE:
                if ($semErros) {
                    //COMMIT DA TRANSAÇÃO:
                    $query = "COMMIT;";
                    if (!mysqli_query($mySQL, $query)) {
                        echo "<span class='warning'>Erro: " . $query . "<br>" . mysqli_error($mySQL) . "</span><br>";
                        $semErros = false;
                    }

                    //O UTILIZADOR É INFORMADO SOBRE O SUCESSO DA EDIÇÃO:
                    echo "<span class='information'>Alterou os valores da criança com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";

                    //AO CLICAR NO BOTÃO, O UTILIZADOR VOLTA PARA A PAGINA INSERÇÃO DE VALORES
                    echo "<a href='insercao-de-valores'><button class='continuarButton textoLabels'>Continuar</button></a>";

                //CASO CONTRÁRIO:
                } else {
                    //ROLLBACK DA TRANSAÇÃO:
                    $query = "ROLLBACK;";
                    if (!mysqli_query($mySQL, $query)) {
                        echo "<span class='warning'>Erro: " . $query . "<br>" . mysqli_error($mySQL) . "</span><br>";
                        $semErros = false;
                    }
                }
            }

        }
    }
}