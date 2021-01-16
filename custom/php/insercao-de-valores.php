<?php
require_once("custom/php/common.php");
if (verificaCapability("insert_values")) {//VERIFICAR SE UTILIZADOR FEZ LOGIN E TEM A CAPACIDADE
    $mySQL = ligacaoBD();//ESTABELECE A LIGAÇÃO COM A BASE DE DADOS
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {//SE SELECINAR A BASE DE DADOS DÁ ERRO
        die("Connection failed: " . mysqli_connect_error());
    } else {
        if ($_REQUEST["estado"] == "escolher_crianca") {//SE ESTADO É ESCOLHER_CRIANCA UTILIZADOR VAI ESCOLHER CRIANÇA
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - criança - escolher</h3></div>";
            echo "<div class='caixaFormulario'>";
            $nomeCrianca = testarInput($_REQUEST["nome_crianca"]);
            $dataNascimento = testarInput($_REQUEST["data_nascimento"]);
            $query = "SELECT name,birth_date,id FROM child WHERE name LIKE '%$nomeCrianca%'";//INÍCIO DA QUERY
            if (!empty($dataNascimento)) {//SE FOI ESPECIFICADA DATA DE NASCIMENTO ACRESCENTA-SE À QUERY
                $query .= "AND birth_date='$dataNascimento'";
            }
            $criancas = mysqli_query($mySQL, $query);
            if (mysqli_num_rows($criancas) > 0) {//SE HOUVER PELO MENOS UMA CRIANÇA LISTAR UMA LISTA COM TODAS
                echo "<ol>";
                while ($child = mysqli_fetch_assoc($criancas)) {
                    echo "<li><a href='insercao-de-valores?estado=escolher_item&crianca=" . $child['id'] . "'>[" . $child["name"] . "] (" . $child["birth_date"] . ")</a>";//LIST ITEM TEM O NOME E DATA DA CRIANÇA, É UM LINK
                    $query = "SELECT id FROM value WHERE child_id=" . $child["id"];
                    $valoresCrianca = mysqli_query($mySQL, $query);
                    if (mysqli_num_rows($valoresCrianca) > 0) {//SE A CRIANÇA TEM VALORES ASSOCIADOS A ELA NA BASE DE DADOS, APRESENTAR LINK PARA EDITÁ-LOS
                        echo " <a href='edicao-de-dados?estado=editar&id=" . $child["id"] . "&tipo=crianca'>[editar valores]</a>";
                    }
                    echo "</li> ";
                }
                echo "</ol>";
            } else {//INFORMAR UTILIZADOR QUE NÃO HÁ CRIANÇAS COM OS DADOS ESPECIFICADOS
                echo "<span class='information'>Não há crianças com os dados especificados.</span><br>";
            }
            voltarAtras();
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "escolher_item") {//SE ESTADO É ESCOLHER_ITEM UTILIZADOR VAI ESCOLHER ITEM NO QUAL INSERIR OS VALORES
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - escolher item</h3></div>";
            echo "<div class='caixaFormulario'>";
            $_SESSION["child_id"] = $_REQUEST["crianca"];
            echo "<ul>";
            $query = "SELECT name,id FROM item_type ORDER BY id";//QUERY PARA OBTER OS TIPOS DE ITEM
            $tipoItens = mysqli_query($mySQL, $query);
            while ($tipoItem = mysqli_fetch_assoc($tipoItens)) {
                echo "<li>" . $tipoItem["name"] . "</li><ul>";
                $query = "SELECT name,id FROM item WHERE item_type_id=" . $tipoItem["id"];//QUERY PARA OBTER OS ITENS ASSOCIADO AO TIPO DE ITEM
                $itens = mysqli_query($mySQL, $query);
                while ($item = mysqli_fetch_assoc($itens)) {
                    echo "<li><a href='insercao-de-valores?estado=introducao&item=" . $item["id"] . "'>[" . $item["name"] . "]</a></li>";//LIST ITEM É LINK PARA PRÓXIMO ESTADO
                }
                echo "</ul>";
            }
            echo "</ul>";
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "introducao") {//SE ESTADO É INTRODUCAO UTILIZADOR VAI ESPECIFICAR VALORES A INTRODUZIR
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_subitens.js', array('jquery'), 1.1, true);
            }
            $_SESSION["item_id"] = $_REQUEST["item"];
            $query = "SELECT name,item_type_id from item WHERE id=" . $_SESSION["item_id"];//QUERY PARA GUARDAR NOME DO ITEM E ID DO TIPO DE ITEM EM VÁRIAVEL DE SESSÃO
            $item = mysqli_fetch_assoc(mysqli_query($mySQL, $query));
            $_SESSION["item_name"] = $item["name"];
            $_SESSION["item_type_id"] = $item["item_type_id"];
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - " . $_SESSION["item_name"] . "</h3></div>";
            echo "<div class='caixaFormulario'>";
            $nomeFormulario = sprintf("item_type_%d_item_%d", $_SESSION["item_type_id"], $_SESSION["item_id"]);//NOME DO FORMULÁRIO
            $action = sprintf("%s?estado=validar&item=%d", get_site_url() . '/' . $current_page, $_SESSION["item_id"]);
            echo "<span class='warning'>Campos obrigatórios *</span>";
            echo "<form method='post' name='$nomeFormulario' action='$action'>";
            $query = "SELECT * from subitem WHERE item_id=" . $_SESSION["item_id"] . " AND state='active' ORDER BY form_field_order";//QUERY PARA BUSCAR SUBITENS ASSOCIADOS AO ITEM, QUE ESTÃO ATIVOS, ORDENADOS PELO FORM_FIELD_ORDER
            $subItens = mysqli_query($mySQL, $query);
            $id = 0;//INPUTS SÓ TEM ID SE FOREM OBRIGATÓRIOS, PURAMENTE PARA A VALIDAÇÃO CLIENT-SIDE
            while ($subItem = mysqli_fetch_assoc($subItens)) {
                $nomeInput = $subItem["form_field_name"];//NOME DO INPUT
                $inputFields = "<span class='textoLabels'><strong>" . $subItem["name"] . "</strong></span>" . ($subItem["mandatory"] == 1 ? "<span class='warning'>*</span>" : "") . "<br>";//criar a label
                switch ($subItem["value_type"]) {//DEFINIR O TIPO DE INPUT DE ACORDO COM O TIPO DE VALOR
                    case "text":
                        if ($subItem["form_field_type"] == "text") {//SE É TEXT FAZER INPUT TEXT
                            $inputFields .= "<input name='$nomeInput' type='text'  class='textInput'" . ($subItem["mandatory"] == 1 ? " id='$id'" : "") . ">";
                        } else {//SE NÃO, É TEXTBOX E FAZ-SE UMA TEXTAREA
                            $inputFields .= "<textarea class='textArea' name='$nomeInput' rows='5' cols='50'" . ($subItem["mandatory"] == 1 ? " id='$id'" : "") . "></textarea>";
                        }
                        break;
                    case "bool":
                        $inputFields .= "<input name='$nomeInput' type='radio' checked value='verdadeiro'><br><input name='$nomeInput' type='radio' value='falso'>";
                        break;
                    case "double":
                    case "int":
                        $inputFields .= "<input name='$nomeInput' type='text' class='textInput'" . ($subItem["mandatory"] == 1 ? " id='$id'" : "") . ">";
                        break;
                    case "enum":
                        $isSelectBox = $subItem["form_field_type"] == "selectbox";//VER SE INPUT É DO TIPO SELECTBOX
                        $index = 0;
                        if ($isSelectBox) {//SE É DO TIPO SELECTBOX CRIAR O SELECT E INSERIR UMA OPÇÃO "PLACEHOLDER"
                            $inputFields .= "<select name='$nomeInput'" . ($subItem["mandatory"] == 1 ? " id='$id'" : "") . " class='textInput textoLabels'>";
                            $inputFields .= "<option value='empty'>Selecione um valor</option>";
                        } else {//SE NÃO É SELECT BOX CRIAR INPUT
                            $inputFields .= "<input name='$nomeInput";
                            if ($subItem["form_field_type"] == "radio") {//SE É RADIO O PRIMEIRO FICA CHECKED
                                $inputFields .= "'";
                                $inputFields .= " checked ";
                            } else {//SE NÃO É, É CHECKBOX, ACRESCENTA-SE _$INDEX PARA DISTINGUIR AS DIFERENTES CHECKBOXES DE MODO A INSERIR EM TUPLOS SEPARADOS MAIS À FRENTE
                                $inputFields .= "_$index'";
                            }
                        }
                        $query = "SELECT value from subitem_allowed_value WHERE subitem_id=" . $subItem["id"];//QUERY PARA ARRANJAR OS VALORES PERMITIDOS DO ENUM
                        $valoresPermitidos = mysqli_query($mySQL, $query);
                        while ($valor = mysqli_fetch_assoc($valoresPermitidos)) {
                            if ($isSelectBox) {//SE É SELECTBOX, CADA VALOR FICA NUMA OPÇÃO DO SELECTBOX
                                $inputFields .= "<option value='" . $valor["value"] . "'>" . $valor["value"] . "</option>";
                            } else {//CAS CONTRÁRIO, ESPECIFICAR TYPE DO INPUT E VALOR
                                $inputFields .= " type='" . $subItem["form_field_type"] . "' value='" . $valor["value"] . "'" . ($subItem["mandatory"] == 1 ? " id='$id'" : "") . "><span class='textoLabels'>" . $valor["value"] . "</span><br>";
                            }
                            $index++;
                            if ($index < mysqli_num_rows($valoresPermitidos) && !$isSelectBox) {//VERIFICAR, SE NÃO FOR SELECTBOX, SE DEVE-SE COMEÇAR OUTRO INPUT, ISTO É, NÃO SE CHEGOU AO FIM DOS VALORES PERMITIDOS
                                $inputFields .= "<input name='$nomeInput";
                                if ($subItem["form_field_type"] == "checkbox") {
                                    $inputFields .= "_$index";
                                }
                                $inputFields .= "'";
                            }
                        }
                        if ($isSelectBox) {//SE INPUT É DO TIPO SELECTBOX, FECHAR A TAG HTML
                            $inputFields .= "</select>";
                        }
                        break;
                }
                if ($subItem["unit_type_id"] != null) {//SE O SUBITEM TEM UMA UNIDADE ASSOCIADA, ACRESCENTÁ-LA AO LADO DO INPUT
                    $query = "SELECT name from subitem_unit_type WHERE id=" . $subItem["unit_type_id"];
                    $unidade = mysqli_fetch_assoc(mysqli_query($mySQL, $query));
                    $inputFields .= "<span class='textoLabels'> " . $unidade["name"] . "</span>";
                }
                echo $inputFields . "<br>";
                if ($subItem["mandatory"] == 1) {//SÓ SE AUMENTA O ID SE O SUBITEM ERA OBRIGATÓRIO
                    $id++;
                }
            }
            echo "<input type='hidden' value='validar' name='estado'><input type='submit' class='submitButton' name='submit' value='Submeter'>";
            echo "</form>";
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "validar") {//SE O ESTADO É VALIDAR VERIFICA-SE SE HÁ CAMPOS OBRIGATÓRIOS NÃO PREENCHIDOS
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - " . $_SESSION["item_name"] . " - validar</h3></div>";
            echo "<div class='caixaFormulario'>";
            $query = "SELECT form_field_name,name,mandatory,form_field_type from subitem WHERE item_id=" . $_SESSION["item_id"] . " AND state='active'";//QUERY PARA OBTER SUBITENS
            $subItens = mysqli_query($mySQL, $query);
            $error = false;
            $listaSubItems = array();//GUARDA-SE OS SUBITENS NUM ARRAY PARA NÃO TER DE ESTAR SEMPRE A FAZER A QUERY
            while ($subItem = mysqli_fetch_assoc($subItens)) {
                array_push($listaSubItems, $subItem);
            }
            foreach ($listaSubItems as $subItem) {//PERCORRE-SE O ARRAY DOS SUBITENS PARA VER SE CAMPOS OBRIGATÓRIOS NÃO FORAM PREENCHIDOS
                if ($subItem["mandatory"] == 1) {
                    switch ($subItem["form_field_type"]) {
                        case "text":
                        case "textbox":
                            $input = testarInput($_REQUEST[$subItem["form_field_name"]]);
                            if (empty($input)) {
                                echo "<span class='warning'>O campo do subitem " . $subItem["name"] . " é obrigatório!</span><br>";
                                $error = true;
                            }
                            break;
                        case "selectbox":
                            if ($_REQUEST[$subItem["form_field_name"]] == "empty") {//SE O UTILIZADOR NÃO SELECIONOU OPÇÃO E DEIXOU NO "PLACEHOLDER"
                                echo "<span class='warning'>O campo do subitem " . $subItem["name"] . " é obrigatório!</span><br>";
                                $error = true;
                            }
                            break;
                        case "checkbox"://SE FOR CHECKBOX VER SE NENHUMA DAS CHECKBOX ESTÁ CHECKED
                            $umaCheckBoxPreenchida = false;
                            foreach ($_REQUEST as $key => $value) {//KEY É O NOME DO INPUT NO HTML
                                if (strpos($key, $subItem["form_field_name"]) !== false) {//VER SE O NOME DO INPUT CONTÉM O NOME DO INPUT NA BD, CONTÉM PORQUE NOME DO INPUT NO HTML É UNICO PARA AS DIFERENTES CHECKBOXES
                                    array_push($valoresChecked, $key);
                                    $umaCheckBoxPreenchida = true;
                                    break;
                                }
                            }
                            if (!$umaCheckBoxPreenchida) {
                                echo "<span class='warning'>O campo do subitem " . $subItem["name"] . " é obrigatório!</span><br>";
                                $error = true;
                            }
                            break;
                    }
                }
            }
            if (!$error) {//SE TODOS OS CAMPOS OBRIGATÓRIOS FORAM PREENCHIDOS
                echo "<span class='information'>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão corretos e pretende submeter os mesmos?</span><br>";
                echo "<ul>";
                foreach ($listaSubItems as $subItem) {
                    $nomeInput = $subItem["name"];
                    $valoresAListar = array();//VALORES DO SUBITEM QUE VÃO PARA A BD, ÚTIL PARA LISTAR AS DIFERENTES CHECKBOXES QUE FORAM ASSINALADAS
                    foreach ($_REQUEST as $key => $value) {
                        if (strpos($key, $subItem["form_field_name"]) !== false) {
                            array_push($valoresAListar, $value);
                        }
                    }
                    echo "<li><p class='textoValidar'>$nomeInput</p></li><ul>";
                    foreach ($valoresAListar as $input) {
                        if ($subItem["form_field_type"] == "text" || $subItem["form_field_type"] == "textbox") {//SÓ É PRECISO TESTAR INPUT SE INPUTS PERMITEM UTILIZADOR CUSTOMIZAR VALOR
                            $input = testarInput($input);
                        }
                        echo "<li>$input</li>";
                    }
                    echo "</ul>";
                }
                echo "</ul>";
                $action = sprintf("%s?estado=inserir&item=%d", get_site_url() . '/' . $current_page, $_SESSION["item_id"]);
                echo "<form method='post' action='$action'>";//FORMULÁRIO COM INPUTS HIDDEN
                foreach ($listaSubItems as $subItem) {
                    $nomeInput = $subItem["form_field_name"];
                    foreach ($_REQUEST as $key => $value) {//KEY É O NOME DO INPUT QUE VEM DA PÁGINA ANTERIOR, PARA PRESERVAR AS CHECKBOXES ÚNICAS
                        if (strpos($key, $nomeInput) !== false) {//CONTÉM POR CAUSA DAS CHECKBOXES, COMO REFERIDO ANTERIORMENTE
                            $input = $value;
                            if ($subItem["form_field_type"] == "text" || $subItem["form_field_type"] == "textbox") {
                                $input = testarInput($input);
                            }
                            echo "<input type='hidden' name='$key' value='$input'>";
                        }
                    }
                }
                echo "<input type='submit' class='submitButton' value='Submeter'>";
                echo "</form>";
            } else {//SE HÁ PELO MENOS UM CAMPO OBRIGATÓRIO NÃO PREENCHIDO MOSTRAR BOTÃO PARA VOLTAR ATRÁS E LISTA DE CAMPOS OBRIGATÓRIOS EM FALTA
                voltarAtras();
            }
            echo "</div>";
        } elseif
        ($_REQUEST["estado"] == "inserir") {//SE ESTADO É INSERIR VAMOS INSERIR OS VALORES NA BASE DE DADOS
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - " . $_SESSION["item_name"] . " - inserção</h3></div>";
            echo "<div class='caixaFormulario'>";
            $insertQueries = array();//ARRAY ONDE SE VAI METER TODAS AS QUERIES DE INSERT GERADAS
            foreach ($_REQUEST as $key => $value) {//KEY É O NOME DO INPUT QUE VEM DESDE O ESTADO INTRODUCAO E VALUE O VALOR DO INPUT
//                $nomeInput = $key;
//                if (is_numeric($key[-1])) {//VERIFICAR SE O ÚLTIMO DÍGITO É NUMÉRICO, NESTE CASO INPUT ERA CHECKBOX POR ISSO TIRAMOS O IDENTIFICADOR ÚNICO QUE ESTAVA NO NOME DO INPUT PARA FICAR SÓ O NOME DO INPUT NA BD
//                    $nomeInput = substr($key, 0, -2);
//                }
                $idSubItem=explode("-",$key)[1];//ID DO SUBITEM APARECE DEPOIS DE UM TRAÇO POR ISSO ESTARÁ NA POSIÇÃO 1 DO ARRAY OBTIDO PELO EXPLODE
//                $query = "SELECT id from subitem WHERE form_field_name='$nomeInput' AND state='active'";//QUERY PARA OBTER ID DO SUBITEM
//                $result = mysqli_query($mySQL, $query);mysqli_fetch_assoc($result)["id"]
                if (mysqli_num_rows($result) > 0) {
                    $query = "INSERT INTO `value` (`id`, `child_id`, `subitem_id`, `value`, `date`, `time`, `producer`) VALUES (NULL," . $_SESSION["child_id"] . "," . $idSubItem . ",'$value','" . date("Y-m-d") . "','" . date("H:i:s") . "','" . wp_get_current_user()->user_login . "')";
                    array_push($insertQueries, $query);
                }
            }
            $query = "START TRANSACTION;\n";//INÍCIO DE TRANSAÇÃO
            $ocorreuErro = false;
            if (!mysqli_query($mySQL, $query)) {
                echo "<span class='warning'>Erro: " . $query . "<br>" . mysqli_error($mySQL) . "</span>";
                $ocorreuErro = true;
            }
            foreach ($insertQueries as $insertQuery) {//EFETUAR AS QUERIES DE INSERT
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                    $ocorreuErro = true;
                    break;
                }
            }
            if (!$ocorreuErro) {//SE NÃO HOUVE ERROS ANTERIORMENTE, COMMIT DA TRANSAÇÃO
                $query = "COMMIT;";
                if (!mysqli_query($mySQL, $query)) {
                    echo "<span class='warning'>Erro: " . $query . "<br>" . mysqli_error($mySQL) . "</span>";
                    $ocorreuErro = true;
                }
            } else {//CASO CONTRÁRIO, ROLLBACK DA TRANSAÇÃO
                $query = "ROLLBACK;";
                if (!mysqli_query($mySQL, $query)) {
                    echo "<span class='warning'>Erro: " . $query . "<br>" . mysqli_error($mySQL) . "</span>";
                    $ocorreuErro = true;
                }
            }
            if (!$ocorreuErro) {//SE NÃO ACONTECEU ERROS INFORMAR UTILIZADOR DE INSERÇÃO COM SUCESSO E MOSTRAR OS DOIS BOTÕES
                echo "<span class='information'>Inseriu o(s) valor(es) com sucesso.<br>Clique em <strong>Voltar</strong> para voltar ao início da inserção de valores ou em <strong>Escolher item</strong> se quiser continuar a inserir valores associados a esta criança.<br></span>";
                echo "<a href='insercao-de-valores'><button class='atrasButton textoLabels'>Voltar</button></a>";
                echo "<a href='?estado=escolher_item&crianca=" . $_SESSION["child_id"] . "'><button class='continuarButton textoLabels'>Escolher item</button></a>";
            } else {//SE OCORREU UM ERRO MOSTRAR O BOTÃO PARA VOLTAR ATRÁS
                voltarAtras();
            }
            echo "</div>";
        } else {//SE NÃO VEM NADA SOBRE O ESTADO APRESENTAR O FORMULÁRIO PARA INSERIR NOME E DATA DA CRIANÇA
            $action = get_site_url() . '/' . $current_page;
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - criança - procurar</h3></div>";
            echo "<div class='caixaFormulario'><span class='information'>Introduza um dos nomes da criança a encontrar e/ou a data de nascimento dela</span>
                <form method='post' action='$action'>
                <strong class='textoLabels'>Nome: </strong><br><input type='text' class='textInput' name='nome_crianca' class='textoLabels'><br>
                <strong class='textoLabels'>Data de Nascimento: </strong><br><input type='text' class='textInput' placeholder='AAAA-MM-DD' name='data_nascimento' class='textoLabels'><br>                
                <input type='hidden' name='estado' value='escolher_crianca'>
                <input type='submit' class='submitButton' value='Submeter'>
                </form></div>";
        }
    }
} else {//CASO NÃO TENHA EFETUADO LOGIN E/OU NÃO TEM A CAPACIDADE
    echo "<span class='warning'>Não tem autorização para aceder a esta página</span>";
}