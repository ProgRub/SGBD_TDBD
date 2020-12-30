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
            if (empty($nomeCrianca) && empty($dataNascimento)) {//se nenhum dos campos estiver preenchido
                echo "<span class='warning'>Pelo menos um dos campos tem de estar preenchido!</span>";
                voltarAtras();
            } else {
                $query = "SELECT name,birth_date,id FROM child WHERE ";//início da query
                if (!empty($nomeCrianca)) {//se foi especificado nome de criança adiciona-se este à query
                    $query .= "name LIKE '%$nomeCrianca%'";
                }
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
                    echo "<span class='information'>Não há crianças com os dados especificados.</span>";
                }
                voltarAtras();//mostrar botão para voltar atrás
            }
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "escolher_item") {//escolher item dos que estão na base de dados, apresentados numa lista desordenada em que cada item é um link
            session_start();
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - escolher item</h3></div>";
            echo "<div class='caixaFormulario'>";
            $_SESSION["child_id"] = $_REQUEST["crianca"];
            echo "<ul>";
            $query = "SELECT name,id FROM item_type ORDER BY id";
            $result = mysqli_query($mySQL, $query);
            while ($tipoItem = mysqli_fetch_assoc($result)) {
                echo "<li>" . $tipoItem["name"] . "</li><ul>";
                $query = "SELECT name,id FROM item WHERE item_type_id=" . $tipoItem["id"];
                $result = mysqli_query($mySQL, $query);
                while ($item = mysqli_fetch_assoc($result)) {
                    echo "<li><a href='insercao-de-valores?estado=introducao&item=" . $item["id"] . "'>[" . $item["name"] . "]</a></li>";
                }
                echo "</ul>";
            }
            echo "</ul>";
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "introducao") {//introduzir novos subitems
            $_SESSION["item_id"] = $_REQUEST["item"];
            $query = "SELECT name from item WHERE id=" . $_SESSION["item_id"];
            $result = mysqli_query($mySQL, $query);
            $item = mysqli_fetch_assoc($result);
            $_SESSION["item_name"] = $item["name"];
            $query = "SELECT item_type_id from item WHERE id=" . $_SESSION["item_id"];
            $result = mysqli_query($mySQL, $query);
            $item = mysqli_fetch_assoc($result);
            $_SESSION["item_type_id"] = $item["item_type_id"];
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - " . $_SESSION["item_name"] . "</h3></div>";
            echo "<div class='caixaFormulario'>";
            echo "<form method='post' name='item_type_" . $_SESSION["item_type_id"] . "item_" . $_SESSION["item_id"] . "' action='insercao_de_valores?estado=validar&item=" . $_SESSION["item_id"] . "'>";
            $query = "SELECT * from subitem WHERE item_id=" . $_SESSION["item_id"] . "AND state='active'";
            $result = mysqli_query($mySQL, $query);
            while ($subItem = mysqli_fetch_assoc($result)) {
                $inputFields = "<span class='=textoLabels'>" . $subItem["form_field_name"] . "</span><br><input name='" . $subItem["form_field_name"];//criar a label e input com nome determinado pelos dados na base de dados
                switch ($subItem["value_type"]) {//definir o tipo de input de acordo com o valor
                    case "text":
                        $inputFields .= " type='" . $subItem["form_field_type"] . "'>";
                        break;
                    case "bool":
                        $inputFields .= " type='radio'>";
                        break;
                    case "double":
                    case "int":
                        $inputFields .= " type='text'>";
                        break;
                    case "enum":
                        $query = "SELECT value from subitem_allowed_value WHERE subitem_id=" . $subItem["id"];
                        $result = mysqli_query($mySQL, $query);
                        $id = 0;
                        if ($subItem["form_field_type"] == "radio") {
                            $inputFields .= " checked ";
                        }
                        while ($valor = mysqli_fetch_assoc($result)) {
                            $inputFields .= "id='" . $id . "' type='" . $subItem["form_field_type"] . "'><span for='" . $id . "' class='textoLabels'>" . $valor["value"] . "</span>";
                        }
                        break;
                }
                if ($subItem["unit_type_id"] != null) {//se o subitem tem uma unidade associada acrescentá-la ao lado do input
                    $query = "SELECT name from subitem_unit_type WHERE id=" . $subItem["unit_type_id"];
                    $result = mysqli_query($mySQL, $query);
                    $unidade = mysqli_fetch_assoc($result);
                    $inputFields .= "<span class='=textoLabels'>" . $unidade["name"] . "</span>";
                }
                echo $inputFields . "<br>";
            }
            echo "<input type='hidden' value='validar' name='estado'><input type='submit' class='submitButton' name='submit' value='Submeter'>";
            echo "</form>";
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "validar") {
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - " . $_SESSION["item_name"] . " - validar</h3></div>";
            echo "<div class='caixaFormulario'>";
            $query = "SELECT * from subitem WHERE item_id=" . $_SESSION["item_id"] . "AND state='active'";
            $result = mysqli_query($mySQL, $query);
            $error=false;
            $listaSubItems=array();
            while ($subItem = mysqli_fetch_assoc($result)) {
                array_push($listaSubItems,$subItem);
            }
            foreach ($listaSubItems as $subItem){
                $input=testarInput($_REQUEST[$subItem["form_field_name"]]);
                if (empty($input)){
                    echo "<span class='warning'>O campo do subitem ".$subItem["name"]." é obrigatório!</span><br>";
                    $error=true;
                }
            }
            if (!$error){
                echo "<span class='information'>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão corretos e pretende submeter os mesmos?</span><br>";
                foreach ($listaSubItems as $subItem){
                    $input=testarInput($_REQUEST[$subItem["form_field_name"]]);
                }
                echo "<form method='post' action='insercao-de-valores?estado=inserir&item=".$_SESSION["item_id"]."'>";
                foreach ($listaSubItems as $subItem){
                    $input=testarInput($_REQUEST[$subItem["form_field_name"]]);
                }
                echo "<input type='submit' class='submitButton' value='Submeter'>";
                echo "</form>";
            }
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "inserir") {
        } else {
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - criança - procurar</h3></div>";
            echo "<div class='caixaFormulario'><span class='information'>Introduza um dos nomes da criança a encontrar e/ou a data de nascimento dela</span>
                <form method='post'>
                <strong class='textoLabels'>Nome: </strong><br><input type='text' name='nome_crianca' class='textoLabels'><br>
                <strong class='textoLabels'>Data de Nascimento: </strong><br><input type='text' placeholder='AAAA-MM-DD' name='data_nascimento' class='textoLabels'><br>                
                <input type='hidden' name='estado' value='escolher_crianca'>
                <input type='submit' class='submitButton' value='Submeter'>
                </form></div>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}