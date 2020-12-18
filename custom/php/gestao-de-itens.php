<?php
echo "MUDOU\n";
require_once("custom/php/common.php");

if (verificaCapability("manage_items")) {

    $mySQL = ligacaoBD();

    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());

    } else {
        if ($_REQUEST["estado"] == "inserir") {
            echo "<h3>Gestão de itens - inserção</h3>";
//            $queryTipoItem="SELECT id FROM item_type WHERE name='".$_REQUEST["tipo_item"]."'";
//            $item = mysqli_query($mySQL,$queryTipoItem);
//            $tipoItem=mysqli_fetch_assoc($item)["id"];
            if (!empty($_REQUEST["nome_item"])) {
                $insertQuery = "INSERT INTO item (id, name,item_type_id,state) VALUES (NULL,'" . testarInput($_REQUEST["nome_item"]) . "'," . $_REQUEST["tipo_item"] . ",'" . $_REQUEST["estado_item"] . "');";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL);
                } else {
                    echo "Inseriu os dados de novo item com sucesso.\nClique em Continuar para avançar.";
                    echo "<br><a href='gestao-de-itens'>Continuar</a>";
                }
            }else{
                echo "O campo 'Nome' é obrigatório!\n";
                voltarAtras();
            }
        } else {
            $queryItens = "SELECT * FROM item"; //TODOS OS ITENS
            $tabelaItens = mysqli_query($mySQL, $queryItens);

            if (mysqli_num_rows($tabelaItens) > 0) { //sucesso na query
                $queryTipos = "SELECT * FROM item_type ORDER BY name"; //TODOS OS TIPOS DE ITENS
                $tabelaTipos = mysqli_query($mySQL, $queryTipos);

                if (mysqli_num_rows($tabelaTipos) > 0) {
                    echo "<table>";
                    echo "<tr><th>tipo de item</th><th>id</th><th>nome do item</th><th>estado</th><th>ação</th></tr>";

                    while ($linhaTipo = mysqli_fetch_assoc($tabelaTipos)) { //CADA TIPO DE ITEM
                        $queryItensTipo = "SELECT * FROM item WHERE item_type_id = " . $linhaTipo["id"] . " ORDER BY name"; //TODOS OS ITENS DE DESSE TIPO
                        $tabelaItensTipo = mysqli_query($mySQL, $queryItensTipo);

                        if (mysqli_num_rows($tabelaItensTipo) > 0) {
                            $newItem = true;
                            $numeroItens = mysqli_num_rows($tabelaItensTipo);
                            while ($linhaItemTipo = mysqli_fetch_assoc($tabelaItensTipo)) {
                                echo "<tr><td>".$linhaTipo["name"]."</td><td>".$linhaItemTipo["id"]."</td><td>".$linhaItemTipo["name"]."</td><td>".($linhaItemTipo["state"]=='active'?'ativo':'inativo')."</td><td>[editar] [desativar]</td></tr>";
//                                if ($newItem) {
//                                    echo "<tr><td rowspan='$numeroItens'>" . $linhaTipo["name"] . "</td>"; //NOME DESSE TIPO
//                                    $newItem = false;
//                                } else {
//                                    echo "<tr>";
//                                }
//                                echo "<td>" . $linhaItemTipo["id"] . "</td><td>" . $linhaItemTipo["name"] . "</td><td>" . ($linhaItemTipo["state"] == 'active' ? 'ativo' : 'inativo') . "</td><td>[editar] [desativar]</td>"; //DADOS DE CADA ITEM DESSE TIPO
//                                echo "</tr>";
                            }
                        }
                    }
                    echo "</table>";
                }
            } else {
                echo "Não há itens";
            }
            echo "<h3>Gestão de itens - introdução</h3><body>
<form method='post' > Nome <input type='text' name='nome_item' ><br><br>
    Tipo<br><input type='radio' id='dc' value='1' name='tipo_item' checked='checked'><label for='dc'>dado de criança</label><br>
    <input type='radio' id='di' value='2' name='tipo_item'><label for='di'>diagnóstico</label><br>
    <input type='radio' id='in' value='3' name='tipo_item'><label for='in'>intervenção</label><br>
    <input type='radio' id='av' value='4' name='tipo_item'><label for='av'>avaliação</label><br><br>
    Estado<br><input type='radio' id='at' value='active' name='estado_item' checked='checked'><label for='at'>ativo</label><br>
    <input type='radio' id='inat' value='inactive' name='estado_item'><label for='inat'>inativo</label><br><input type='hidden' value='inserir' name='estado'>
    <input type='submit' value='Inserir item' name='submit'>
</form>
</body>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
