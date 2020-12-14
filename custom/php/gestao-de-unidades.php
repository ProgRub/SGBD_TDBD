<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_unit_types")) {
    $mySQL=ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        echo "<h1>Erro</h1>";
    } else {
//        echo "<h1>Good</h1>";
        if ($_REQUEST["estadoExecucao"] == "inserir") {
            echo "<h3>Gestão de unidades - inserção</h3>";
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
            echo e"<h3>Gestão de unidades - introdução</h3>
                    <form action='gestao-de-unidades-submit.php' method='post' >
                Nome <input typ='text' name='nome_unidade' ><br>
                        <input type='hidden' value='inserir' name='estado'><br>
                        <input type='submit' value='Inserir tipo de unidade' name='submit'>
                    </form>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}

function checkInputs(){
    echo"Teste";
}
