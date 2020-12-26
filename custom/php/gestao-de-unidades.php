<?php
//echo "MUDOU1\n";
require_once("custom/php/common.php");
if (verificaCapability("manage_unit_types")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        if ($_REQUEST["estado"] == "inserir") {
            echo "<div class='caixaSubTitulo'><h3>Gestão de unidades - inserção</h3></div>";
            $nomeUnidade = testarInput($_REQUEST["nome_unidade"]);
            if (!empty($nomeUnidade)) {
                $insertQuery = "INSERT INTO subitem_unit_type (id, name) VALUES (NULL,'" . $nomeUnidade . "');";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL);
                } else {
                    echo "<span class='information'>Inseriu os dados de novo tipo de unidade com sucesso.<br>Clique em Continuar para avançar.</span><br>";
                    echo "<a href='gestao-de-itens'><input type='submit' class='continuarButton textoLabels' value='Continuar'>";
                }
            } else {
                echo "<div class='textoTabela'>O campo <strong>'Nome'</strong> é obrigatório!\n</div>";
                voltarAtras();
            }
        } else {
            $query = "SELECT id,name FROM subitem_unit_type ORDER BY name";
            $result = mysqli_query($mySQL, $query);
            if (mysqli_num_rows($result) > 0) {
                $table = "<table class='tabela'><tr><th class='textoTabela cell'>id</th><th class='textoTabela  cell'>unidade</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    $table .= "<tr class='row'><td class='textoTabela  cell'>" . $row["id"] . "</td><td class='textoTabela  cell'>" . $row["name"] . "</td></tr>";
                }
                $table .= "</table>";
                echo $table;
            } else {
                echo "Não há tipos de unidades.";
            }
            echo "<div class='caixaSubTitulo'><h3>Gestão de unidades - introdução</h3></div>
<div class='caixaFormulario'><form method='post' > <strong class='textoLabels'>Nome:</strong><span class='warning textoLabels' textoLabels'> * </span><br><input type='text'  name='nome_unidade' class='textoLabels'><br>
    <input type='hidden' value='inserir' name='estado'><br>
    <input class='submitButton textoLabels' type='submit' value='Inserir tipo de unidade' name='submit'>
</form></div>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>
