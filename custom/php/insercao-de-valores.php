<?php
require_once("custom/php/common.php");
if (verificaCapability("insert_values")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        if ($_REQUEST["estado"] == "escolher_crianca") {
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - criança - escolher</h3></div>";
            echo "<div class='caixaFormulario'>";
            if (empty($_REQUEST["nome_crianca"]) && empty($_REQUEST["data_nascimento"])) {
                echo "<span class='warning'>Pelo menos um dos campos tem de estar preenchido!</span>";
                voltarAtras();
            } else {
                $nomeCrianca = testarInput($_REQUEST["nome_crianca"]);
                $dataNascimento = testarInput($_REQUEST["data_nascimento"]);
                $query = "SELECT name,birth_date,id FROM child WHERE ";
                if (!empty($_REQUEST["nome_crianca"])) {
                    $query .= "name LIKE '%" . $nomeCrianca . "%' ";
                }
                if (!empty($_REQUEST["data_nascimento"])) {
                    if (!empty($_REQUEST["nome_crianca"])) {
                        $query .= "AND ";
                    }
                    $query .= "birth_date='" . $dataNascimento . "'";
                }
                $result = mysqli_query($mySQL, $query);
                if (mysqli_num_rows($result) > 0) {
                    echo "<ol>";
                    while ($child = mysqli_fetch_assoc($result)) {
                        echo "<li><a href='insercao-de-valores?estado=escolher_item&crianca=" . $child["id"] . "'>[" . $child["name"] . "] (" . $child["birth_date"] . ")</a></li> ";
                    }
                    echo "</ol>";
                } else {
                    echo "<span class='information'>Não há crianças com os dados especificados.</span>";
                }
                voltarAtras();
            }
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "escolher_item") {
            session_start();
            echo "<div class='caixaSubTitulo'><h3>Inserção de valores - escolher item</h3></div>";
            echo "<div class='caixaFormulario'>";
            $_SESSION["child_id"]=$_REQUEST["crianca"];
            echo "<ul>";
            $query="SELECT name,id FROM item_type ORDER BY id";
            $result=mysqli_query($mySQL,$query);
            while ($tipoItem = mysqli_fetch_assoc($result)){
                echo "<li>".$tipoItem["name"]."</li><ul>";
                $query="SELECT name,id FROM item WHERE item_type_id=".$tipoItem["id"];
                $result=mysqli_query($mySQL,$query);
                while($item=mysqli_fetch_assoc($result)){
                    echo "<li><a href='insercao-de-valores?estado=introducao&item=".$item["id"]."'>[".$item["name"]."]</a></li>";
                }
                echo "</ul>";
            }
            echo "</ul>";
            echo "</div>";
        } elseif ($_REQUEST["estado"] == "introducao") {
        } elseif ($_REQUEST["estado"] == "validar") {
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