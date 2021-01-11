<?php
require_once("custom/php/common.php");
if (verificaCapability("insert_values")) {//verificar se utilizador fez login e tem esta capacidade
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {//se selecionar a base de dados der erro
        die("Connection failed: " . mysqli_connect_error());
    } else {
        if ($_REQUEST["estado"] == "escolher_crianca") {//escolher criança com nome e data de nascimento especificadas
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - criança - escolher</h3></div>";
            echo "<div class='caixaFormulario'>";
            $nomeCrianca = testarInput($_REQUEST["nome_crianca"]);
            $dataNascimento = testarInput($_REQUEST["data_nascimento"]);
            $query = "SELECT name,birth_date,id FROM child WHERE ";//início da query
            $query .= "name LIKE '%$nomeCrianca%'";
            if (!empty($dataNascimento)) {//se foi especificada data adiciona-se esta à query
                if (!empty($nomeCrianca)) {//se foi especificado o nome é preciso acresentar um AND
                    $query .= "AND ";
                }
                $query .= "birth_date='$dataNascimento'";
            }
            $result = mysqli_query($mySQL, $query);
            if (mysqli_num_rows($result) > 0) {//se houver pelo menos uma criança, listar todas como links numa lista ordenada
                echo "<ol>";
                while ($child = mysqli_fetch_assoc($result)) {
                    echo "<li><a href='insercao-de-valores?estado=escolher_item&crianca=" . $child['id'] . "'>[" . $child["name"] . "] (" . $child["birth_date"] . ")</a></li> ";
                }
                echo "</ol>";
            } else {
                echo "<span class='information'>Não há crianças com os dados especificados.</span><br>";
            }
            voltarAtras();//mostrar botão para voltar atrás
//            }
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "escolher_item") {//escolher item dos que estão na base de dados, apresentados numa lista desordenada em que cada item é um link
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - escolher item</h3></div>";
            echo "<div class='caixaFormulario'>";
            $_SESSION["child_id"] = $_REQUEST["crianca"];
            echo "<ul>";
            $query = "SELECT name,id FROM item_type ORDER BY id";
            $result = mysqli_query($mySQL, $query);
            while ($tipoItem = mysqli_fetch_assoc($result)) {
//                echo mysqli_num_rows(($result));
                echo "<li>" . $tipoItem["name"] . "</li><ul>";
                $query = "SELECT name,id FROM item WHERE item_type_id=" . $tipoItem["id"];
                $result2 = mysqli_query($mySQL, $query);
                while ($item = mysqli_fetch_assoc($result2)) {
                    echo "<li><a href='insercao-de-valores?estado=introducao&item=" . $item["id"] . "'>[" . $item["name"] . "]</a></li>";
                }
                echo "</ul>";
            }
            echo "</ul>";
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "introducao") {//introduzir novos subitems
//            global $clientsideval;
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/insercao_valores.js', array('jquery'), 1.1, true);
            }
//            echo $_REQUEST["item"]."\n";
            $_SESSION["item_id"] = $_REQUEST["item"];
            $query = "SELECT name from item WHERE id=" . $_SESSION["item_id"];
            $result = mysqli_query($mySQL, $query);
            $item = mysqli_fetch_assoc($result);
            $_SESSION["item_name"] = $item["name"];
            $query = "SELECT item_type_id from item WHERE id=" . $_SESSION["item_id"];
            $result = mysqli_query($mySQL, $query);
            $item = mysqli_fetch_assoc($result);
            $_SESSION["item_type_id"] = $item["item_type_id"];
//            echo $_SESSION["item_id"]."\n";
//            echo $_SESSION["item_name"]."\n";
//            echo $_SESSION["item_type_id"]."\n";
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - " . $_SESSION["item_name"] . "</h3></div>";
            echo "<div class='caixaFormulario'>";
            $nomeFormulario = sprintf("item_type_%d_item_%d", $_SESSION["item_type_id"], $_SESSION["item_id"]);// "item_type_". $_SESSION["item_type_id"] . "item_" . $_SESSION["item_id"];
            $action = sprintf("?estado=validar&item=%d", $_SESSION["item_id"]);//"insercao_de_valores?estado=validar&item=" . $_SESSION["item_id"] ;
//            echo $action."\n";
            echo "<span class='warning'>Campos obrigatórios *</span>";
            echo "<form method='post' name='$nomeFormulario' action='$action'>";
            $query = "SELECT * from subitem WHERE item_id=" . $_SESSION["item_id"] . " AND state='active' ORDER BY form_field_order";
            $result = mysqli_query($mySQL, $query);
//            echo mysqli_num_rows($result);
            $id = 0;
            while ($subItem = mysqli_fetch_assoc($result)) {
                $nomeFormulario = $subItem["form_field_name"];
                $inputFields = "<span class='textoLabels'><strong>" . $subItem["name"] . "</strong></span>" . ($subItem["mandatory"] == 1 ? "<span class='warning'>*</span>" : "") . "<br>";//criar a label
                switch ($subItem["value_type"]) {//definir o tipo de input de acordo com o valor
                    case "text":
                        if ($subItem["form_field_type"] == "text") {
                            $inputFields .= "<input name='$nomeFormulario'";//input com nome determinado pelos dados na base de dados
                            $inputFields .= " type='text'  class='textInput'" . ($subItem["mandatory"] == 1 ? " id='$id'" : "") . ">";
                        } else {
                            $inputFields .= "<textarea class='textArea' name='$nomeFormulario' rows='5' cols='50'" . ($subItem["mandatory"] == 1 ? " id='$id'" : "") . "></textarea>";
                        }
                        break;
                    case "bool":
                        $inputFields .= "<input name='$nomeFormulario'";//input com nome determinado pelos dados na base de dados
                        $inputFields .= " type='radio' value='verdadeiro'><br><input name='$nomeFormulario' type='radio' value='falso'>";
                        break;
                    case "double":
                    case "int":
                        $inputFields .= "<input name='$nomeFormulario'";//input com nome determinado pelos dados na base de dados
                        $inputFields .= " type='text' class='textInput'" . ($subItem["mandatory"] == 1 ? " id='$id'" : "") . ">";
                        break;
                    case "enum":
                        $isSelectBox = $subItem["form_field_type"] == "selectbox";
                        if ($isSelectBox) {
                            $inputFields .= "<select name='$nomeFormulario'" . ($subItem["mandatory"] == 1 ? " id='$id'" : "") . " class='textInput textoLabels'>";
                            $inputFields .= "<option value='empty'>Selecione um valor</option>";
                        } else {
                            $inputFields .= "<input name='$nomeFormulario'";//input com nome determinado pelos dados na base de dados
                            if ($subItem["form_field_type"] == "radio") {
                                $inputFields .= " checked ";
                            }
                        }
                        $index = 0;
                        $query = "SELECT value from subitem_allowed_value WHERE subitem_id=" . $subItem["id"];
                        $result2 = mysqli_query($mySQL, $query);
                        while ($valor = mysqli_fetch_assoc($result2)) {
                            if ($isSelectBox) {
                                $inputFields .= "<option value='" . $valor["value"] . "'>" . $valor["value"] . "</option>";
                            } else {
                                $inputFields .= " type='" . $subItem["form_field_type"] . "' value='" . $valor["value"] . "'" . ($subItem["mandatory"] == 1 ? " id='$id'" : "") . "><span class='textoLabels'>" . $valor["value"] . "</span><br>";
                            }
                            $index++;
                            if ($index < mysqli_num_rows($result2) &&!$isSelectBox) {
                                $inputFields .= "<input name='$nomeFormulario'";
                            }
                        }
                        if ($isSelectBox) {
                            $inputFields .= "</select>";
                        }
                        break;
                }
                if ($subItem["unit_type_id"] != null) {//se o subitem tem uma unidade associada acrescentá-la ao lado do input
                    $query = "SELECT name from subitem_unit_type WHERE id=" . $subItem["unit_type_id"];
                    $result3 = mysqli_query($mySQL, $query);
                    $unidade = mysqli_fetch_assoc($result3);
                    $inputFields .= "<span class='textoLabels'> " . $unidade["name"] . "</span>";
                }
                echo $inputFields . "<br>";
                if ($subItem["mandatory"] == 1) {
                    $id++;
                }
            }
            echo "<input type='hidden' value='validar' name='estado'><input type='submit' class='submitButton' name='submit' value='Submeter'>";
            echo "</form>";
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "validar") {
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - " . $_SESSION["item_name"] . " - validar</h3></div>";
            echo "<div class='caixaFormulario'>";
//            echo "MUDOU\n";
            $query = "SELECT form_field_name,name,mandatory from subitem WHERE item_id=" . $_SESSION["item_id"] . " AND state='active'";
            $result = mysqli_query($mySQL, $query);
            $error = false;
            $listaSubItems = array();
            while ($subItem = mysqli_fetch_assoc($result)) {
                array_push($listaSubItems, $subItem);
            }
            foreach ($listaSubItems as $subItem) {
                $input = testarInput($_REQUEST[$subItem["form_field_name"]]);
                if ($subItem["mandatory"] == 1 && empty($input)) {
                    echo "<span class='warning'>O campo do subitem " . $subItem["name"] . " é obrigatório!</span><br>";
                    $error = true;
                }
            }
            if (!$error) {
                echo "<span class='information'>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão corretos e pretende submeter os mesmos?</span><br>";
                echo "<ul>";
                foreach ($listaSubItems as $subItem) {
                    $nomeFormulario = $subItem["form_field_name"];
                    $input = testarInput($_REQUEST[$nomeFormulario]);
                    echo "<li><p class='textoValidar'>$nomeFormulario</p></li>
						  <ul><li>$input</li></ul>";
                }
                echo "</ul>";
                $action = sprintf("?estado=inserir&item=%d", $_SESSION["item_id"]);//"insercao_de_valores?estado=validar&item=" . $_SESSION["item_id"] ;
                echo "<form method='post' action='$action'>";
                foreach ($listaSubItems as $subItem) {
                    $input = testarInput($_REQUEST[$subItem["form_field_name"]]);
                    $nomeFormulario = $subItem["form_field_name"];
                    echo "<input type='hidden' name='$nomeFormulario' value='$input'>";
                }
                echo "<input type='submit' class='submitButton' value='Submeter'>";
                echo "</form>";
            } else {
                voltarAtras();
            }
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "inserir") {
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - " . $_SESSION["item_name"] . " - inserção</h3></div>";
            echo "<div class='caixaFormulario'>";
            $idsSubitems = array();
//            echo "MUDOU2\n";
            $insertQueries = array();
            foreach ($_REQUEST as $key => $value) {//key é o nome do formulário e value é o valor
                $query = "SELECT id from subitem WHERE form_field_name='$key' AND state='active'";
                $result = mysqli_query($mySQL, $query);
                if (mysqli_num_rows($result) > 0) {
                    $query = "INSERT INTO `value` (`id`, `child_id`, `subitem_id`, `value`, `date`, `time`, `producer`) VALUES (NULL," . $_SESSION["child_id"] . "," . mysqli_fetch_assoc($result)["id"] . ",'$value','" . date("Y-m-d") . "','" . date("H:i:s") . "','" . wp_get_current_user()->user_login . "')";
                    array_push($insertQueries, $query);
                }
            }
            $query = "START TRANSACTION;\n";
            if (!mysqli_query($mySQL, $query)) {
                echo "<span class='warning'>Erro: " . $query . "<br>" . mysqli_error($mySQL) . "</span>";
                $ocorreuErro = true;
            }
            $ocorreuErro = false;
            foreach ($insertQueries as $insertQuery) {
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL) . "</span>";
                    $ocorreuErro = true;
                    break;
                }
            }
            if (!$ocorreuErro) {
                $query = "COMMIT;";
                if (!mysqli_query($mySQL, $query)) {
                    echo "<span class='warning'>Erro: " . $query . "<br>" . mysqli_error($mySQL) . "</span>";
                    $ocorreuErro = true;
                }
                echo "<span class='information'>Inseriu o(s) valor(es) com sucesso.<br>Clique em <strong>Voltar</strong> para voltar ao início da inserção de valores ou em <strong>Escolher item</strong> se quiser continuar a inserir valores associados a esta criança.<br></span>";
                echo "<a href='insercao-de-valores'><button class='continuarButton textoLabels'>Voltar</button></a>";
                echo "<a href='?estado=escolher_item&crianca=" . $_SESSION["child_id"] . "'><button class='continuarButton textoLabels'>Escolher item</button></a>";
            } else {
                $query = "ROLLBACK TRANSACTION;";
                if (!mysqli_query($mySQL, $query)) {
                    echo "<span class='warning'>Erro: " . $query . "<br>" . mysqli_error($mySQL) . "</span>";
                    $ocorreuErro = true;
                }
            }
            echo "</div>";
        } else {
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - criança - procurar</h3></div>";
            echo "<div class='caixaFormulario'><span class='information'>Introduza um dos nomes da criança a encontrar e/ou a data de nascimento dela</span>
                <form method='post'>
                <strong class='textoLabels'>Nome: </strong><br><input type='text' class='textInput' name='nome_crianca' class='textoLabels'><br>
                <strong class='textoLabels'>Data de Nascimento: </strong><br><input type='text' class='textInput' placeholder='AAAA-MM-DD' name='data_nascimento' class='textoLabels'><br>                
                <input type='hidden' name='estado' value='escolher_crianca'>
                <input type='submit' class='submitButton' value='Submeter'>
                </form></div>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}