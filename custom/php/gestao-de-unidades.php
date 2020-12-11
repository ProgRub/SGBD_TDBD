<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_unit_types")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
//        echo "<h1>Erro</h1>";
    } else {
//        echo "<h1>Good</h1>";
        if ($_REQUEST["estadoExecucao"] == "inserir") {
//        echo "<h1>Inserir</h1>";
        } else {
//        echo "<h1>Tabela</h1>";
            $query = "SELECT * FROM subitem_unit_type ORDER BY name";
            $result = mysqli_query($mySQL, $query);
            if (mysqli_num_rows($result) > 0) {
                $table = "<table><tr><th>id</th><th>unidade</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    $table .= "<tr><td>$row[id]</td><td>$row[name]</td></tr>";
                }
                $table .= "</table>";
                echo $table;
            } else {
                echo "Não há tipos de unidades";
            }
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}