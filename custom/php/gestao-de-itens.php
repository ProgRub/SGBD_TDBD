<?php
echo "MUDOU3\n";
require_once("custom/php/common.php");

if (verificaCapability("manage_items")) {

    $mySQL = ligacaoBD();

    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());

    } else {
        if ($_REQUEST["estado"] == "inserir") {
            echo "<h3>Gestão de itens - inserção</h3>";
            $queryTipoItem="SELECT id FROM item_type WHERE name=".$_REQUEST["tipo_item"];
            $item = mysqli_query($mySQL,$queryTipoItem);
            $tipoItem=$item["id"];
            $insertQuery="INSERT INTO item (id, name,item_type_id,state) VALUES (NULL,'". testarInput($_REQUEST["nome_item"])."','".$tipoItem."','".$_REQUEST["radio_estado"]=="ativo"?"active":"inactive"."');";
            if (!mysqli_query($mySQL, $insertQuery)) {
                echo "Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL);
            } else {
                echo "Inseriu os dados de novo tipo de unidade com sucesso.\nClique em Continuar para avançar.";
                echo "<br><a href='gestao-de-itens'>Continuar</a>";
            }
        } else {
            $queryItens = "SELECT * FROM item"; //TODOS OS ITENS
            $tabelaItens = mysqli_query($mySQL, $queryItens);

            if ($tabelaItens == true && mysqli_num_rows($tabelaItens) > 0) { //sucesso na query
                $queryTipos = "SELECT * FROM item_type ORDER BY NAME"; //TODOS OS TIPOS DE ITENS
                $tabelaTipos = mysqli_query($mySQL, $queryTipos);

                if ($tabelaTipos == true && mysqli_num_rows($tabelaTipos) > 0) {
                    echo "<table>";
                    echo "<tr><th>tipo de item</th><th>id</th><th>nome do item</th><th>estado</th><th>ação</th></tr>";

                    while ($linhaTipo = mysqli_fetch_assoc($tabelaItens)) { //CADA TIPO DE ITEM
                        $queryItensTipo = "SELECT * FROM item WHERE item_type_id = " . $linhaTipo["id"] . " ORDER BY NAME"; //TODOS OS ITENS DE DESSE TIPO
                        $tabelaItensTipo = mysqli_query($mySQL, $queryItensTipo);

                        if ($tabelaItensTipo == true && mysqli_num_rows($tabelaItensTipo) > 0) {
                            $newItem = true;
                            $numeroItens = mysqli_num_rows($tabelaItensTipo);
                            while ($linhaItemTipo = mysqli_fetch_assoc($tabelaItensTipo)) {
                                if ($newItem == true) {
                                    echo "<tr><td rowspan='$numeroItens'>" . $linhaTipo["name"] . "</td>"; //NOME DESSE TIPO
                                    $newItem = false;
                                } else {
                                    echo "<tr>";
                                }
                                echo "<td>" . $linhaItemTipo["id"] . "</td><td>" . $linhaItemTipo["name"] . "</td><td>" . $linhaItemTipo["state"] . "</td><td>[editar] [desativar]</td>"; //DADOS DE CADA ITEM DESSE TIPO
                                echo "</tr>";
                            }
                        }
                    }
                    echo "</table>";
                }
            } else {
                echo "Não há itens";
            }
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
