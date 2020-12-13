<?php
    require_once("custom/php/common.php");

    if(verificaCapability("manage_items")){

        $mySQL = ligacaoBD();

        if(!$mySQL){
            die("Connection failed: " . mysqli_connect_error());

        }else{
                $queryItens = "SELECT * FROM item"; //TODOS OS ITENS
                $tabelaItens = mysqli_query($mySQL, $queryItens);

                if($tabelaItens == true && mysqli_num_rows($tabelaItens) > 0){ //sucesso na query
                    $queryTipos = "SELECT * FROM item_type ORDER BY NAME"; //TODOS OS TIPOS DE ITENS
                    $tabelaTipos = mysqli_query($mySQL, $queryTipos);

                    if($tabelaTipos == true && mysqli_num_rows($tabelaTipos)>0){
                        echo "<table>";
                        echo "<tr><th>tipo de item</th><th>id</th><th>nome do item</th><th>estado</th><th>ação</th></tr>";

                        while($linhaTipo = mysqli_fetch_assoc($tabelaItens)){ //CADA TIPO DE ITEM
                            $queryItensTipo = "SELECT * FROM item WHERE item_type_id = " .$linhaTipo["id"]." ORDER BY NAME"; //TODOS OS ITENS DE DESSE TIPO
                            $tabelaItensTipo = mysqli_query($mySQL, $queryItensTipo);

                            if($tabelaItensTipo == true && mysqli_num_rows($tabelaItensTipo)>0){
                                $newItem = true;
                                $numeroItens = mysqli_num_rows($tabelaItensTipo);
                                while($linhaItemTipo = mysqli_fetch_assoc($tabelaItensTipo)){
                                    if($newItem == true){
                                        echo "<tr><td rowspan='$numeroItens'>" .$linhaTipo["name"]."</td>"; //NOME DESSE TIPO
                                        $newItem = false;
                                    }else{
                                        echo "<tr>";
                                    }
                                    echo "<td>".$linhaItemTipo["id"]."</td><td>".$linhaItemTipo["name"]."</td><td>".$linhaItemTipo["state"]."</td><td>[editar] [desativar]</td>"; //DADOS DE CADA ITEM DESSE TIPO
                                    echo "</tr>";
                                }
                            }
                        }
                        echo "</table>";
                    }
                }else{
                    echo "Não há itens";
                }
            }
    }else {
        echo "Não tem autorização para aceder a esta página";
    }
