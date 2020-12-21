<?php
//echo "MUDOU1\n";
require_once("custom/php/common.php");
if (verificaCapability("manage_unit_types")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        if ($_REQUEST["estado"] == "inserir") {
            echo "<h3>Gestão de unidades - inserção</h3>";
            $nomeUnidade = testarInput($_REQUEST["nome_unidade"]);
            if (!empty($nomeUnidade)) {
                $insertQuery = "INSERT INTO subitem_unit_type (id, name) VALUES (NULL,'" . $nomeUnidade . "');";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL);
                } else {
                    echo "Inseriu os dados de novo tipo de unidade com sucesso.\nClique em Continuar para avançar.";
                    echo "<br><a href='gestao-de-itens'>Continuar</a>";
                }
            } else {
                echo "O campo <strong>'Nome'</strong> é obrigatório!\n";
                voltarAtras();
            }
        } else {
            $query = "SELECT * FROM subitem_unit_type ORDER BY name";
            $result = mysqli_query($mySQL, $query);
            if (mysqli_num_rows($result) > 0) {
                $table = "<table><tr><th>id</th><th>unidade</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    $table .= "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td></tr>";
                }
                $table .= "</table>";
                echo $table;
            } else {
                echo "Não há tipos de unidades.";
            }
            echo "<h3>Gestão de unidades - introdução</h3><body>
<form method='post' > <strong>Nome:</strong> <input type='text' name='nome_unidade' ><br>
    <input type='hidden' value='inserir' name='estado'><br>
    <input type='submit' value='Inserir tipo de unidade' name='submit'>
</form>
</body>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>
<!--<!DOCTYPE HTML>-->
<!--<html>-->
<!--<body>-->
<!--<form method='post' action=""> Nome <input type='text' name='nome_unidade' ><br>-->
<!--    action='--><?php //echo htmlspecialchars($_SERVER["PHP_SELF"]);?><!--' -->
<!--    <input type='hidden' value='inserir' name='estado'><br>-->
<!--    <input type='submit' value='Inserir tipo de unidade' name='submit'>-->
<!--</form>-->
<!--</body>-->
<!--</html>-->
