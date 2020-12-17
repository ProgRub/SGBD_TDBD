<?php
    require_once("custom/php/common.php");

    if(verificaCapability("manage_items")) {

        $mySQL = ligacaoBD();

        if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
            echo "<h1>Erro</h1>";
        } else {
            if ($_REQUEST["estado"] == "inserir") {
                echo "<h3>Gestão de itens - inserção</h3>";
                $insertQuery="INSERT INTO item (id, name, item_type_id, state) VALUES (NULL,'". testarInput($_REQUEST["nome_item"])."','". testarInput($_REQUEST["id_tipo_item"])."','". testarInput($_REQUEST["estado_item"])."');";
                if (mysqli_query($mySQL, $insertQuery)) {
                    echo "Inseriu os dados de novo item com sucesso.";
                } else {
                    echo "Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL);
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
                                echo "<tr><td rowspan='$numeroItens'>" . $linhaTipo["name"] . "</td>"; //NOME DESSE TIPO
                                while ($linhaItemTipo = mysqli_fetch_assoc($tabelaItensTipo)) {
                                    if ($newItem == true) {
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
                $queryTipos = "SELECT * FROM item_type ORDER BY NAME"; //TODOS OS TIPOS DE ITENS
                $tabelaTipos = mysqli_query($mySQL, $queryTipos);
                echo "<h3>Gestão de itens - introdução</h3>
                    <form action='gestao-de-unidades-submit.php' method='post' >
                    Nome: <input type='text' name='nome_item' ><br>";
                echo "<br>Tipo: </br>";
                if($tabelaTipos == true && mysqli_num_rows($tabelaTipos) > 0){
                    while($linhaTipo = mysqli_fetch_assoc(($tabelaTipos))){
                    echo '<input type="radio" name="id_tipo_item" value='.$linhaTipo["id"].'>'.$linhaTipo["name"].'<br>';
                    }
                }
                echo "<br>Estado:</br> <input type='radio' name='estado_item' value='active'>active<br>";
                echo "<input type='radio' name='estado_item' value='inactive'>inactive<br>";
                echo "<input type='hidden'  name='estado' value='inserir'><br>
                    <input type='submit'  name='submit' value='Inserir item'>
                    </form>";
            }
        }
    }
    else {
        echo "Não tem autorização para aceder a esta página";
    }
    ?>
