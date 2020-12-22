
<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_items")) {

    $mySQL = ligacaoBD();

    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());

    } else {
        if ($_REQUEST["estado"] == "inserir") {
            echo "<h3>Gestão de itens - inserção</h3>";
            $faltaDado = false;
            $campos ="";
            if(empty($_REQUEST["nome_item"])){ //não escreveu nome
                $campos.="<li><br><strong>Nome</strong></li>";
                $faltaDado = true;
            }
            if(empty($_REQUEST["tipo_item"])){ //não escolheu tipo
                $campos.="<li><br><strong>Tipo</strong></li>";
                $faltaDado = true;
            }
            if(empty($_REQUEST["estado_item"])){ //não escolheu estado
                $campos.="<li><strong>Estado</strong></li>";
                $faltaDado = true;
            }

            if (!$faltaDado) { //não falta preencher nenhum campo obrigatório
                $insertQuery = "INSERT INTO item (id, name,item_type_id,state) VALUES (NULL,'" . testarInput($_REQUEST["nome_item"]) . "'," . $_REQUEST["tipo_item"] . ",'" . $_REQUEST["estado_item"] . "');";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL);
                } else {
                    echo "Inseriu os dados de novo item com sucesso.<br>Clique em <strong>Continuar para avançar.</strong>";
                    echo "<br><a href='gestao-de-itens'>Continuar</a>";
                }
            } else {
                echo "Os seguintes campos são <font style='color:red'><strong>obrigatórios</strong></font>:<ul>".$campos."</ul>";
                voltarAtras();
            }
        } else {
            if (mysqli_num_rows(mysqli_query($mySQL, "SELECT * FROM item")) > 0) { //ver se há items na tabela item
                $queryTipos = "SELECT * FROM item_type ORDER BY name"; //TODOS OS TIPOS DE ITENS
                $tabelaTipos = mysqli_query($mySQL, $queryTipos);
                if (mysqli_num_rows($tabelaTipos) > 0) {
                    echo "<table>";
                    echo "<tr><th>tipo de item</th><th>id</th><th>nome do item</th><th>estado</th><th>ação</th></tr>";

                    while ($linhaTipoItem = mysqli_fetch_assoc($tabelaTipos)) { //CADA TIPO DE ITEM
                        $queryItens = "SELECT * FROM item WHERE item_type_id = " . $linhaTipoItem["id"] . " ORDER BY name"; //TODOS OS ITENS DESSE TIPO
                        $tabelaItens = mysqli_query($mySQL, $queryItens);

                        if (mysqli_num_rows($tabelaItens) > 0) {
                            $newItem = true;
                            $numeroItens = mysqli_num_rows($tabelaItens);
                            while ($linhaItem = mysqli_fetch_assoc($tabelaItens)) {
                                //echo "<tr><td>" . $linhaTipoItem["name"] . "</td><td>" . $linhaItem["id"] . "</td><td>" . $linhaItem["name"] . "</td><td>" . ($linhaItem["state"] == 'active' ? 'ativo' : 'inativo') . "</td><td>[editar] [desativar]</td></tr>";
                                if ($newItem) {
                                    echo "<tr><td rowspan='$numeroItens'>" . $linhaTipoItem["name"] . "</td>"; //NOME DESSE TIPO
                                    $newItem = false;
                                } else {
                                   echo "<tr>";
                                }
                                echo "<td>" . $linhaItem["id"] . "</td><td>" . $linhaItem["name"] . "</td><td>" . ($linhaItem["state"] == 'active' ? 'ativo' : 'inativo') . "</td><td>[editar] [desativar]</td>"; //DADOS DE CADA ITEM DESSE TIPO
                                echo "</tr>";
                            }
                        }
                    }
                    echo "</table>";
                }
            } else {
                echo "Não há itens.";
            }
            $queryTipos = "SELECT * FROM item_type ORDER BY NAME"; //TODOS OS TIPOS DE ITENS
            $tabelaTipos = mysqli_query($mySQL, $queryTipos);
            echo "<h3><strong>Gestão de itens - introdução</strong></h3><body>
            <form method='post' > <strong>Nome: </strong><br><input style='width: 45%;' type='text' name='nome_item' ><br><br>";
            echo "<br><strong>Tipo: </strong></br>";
            if($tabelaTipos == true && mysqli_num_rows($tabelaTipos) > 0){
                while($linhaTipo = mysqli_fetch_assoc(($tabelaTipos))){
                    echo '<input type="radio" name="tipo_item" value='.$linhaTipo["id"].'>'.$linhaTipo["name"].'<br>';
                }
            }else if($tabelaTipos == true && mysqli_num_rows($tabelaTipos) == 0){
                echo "Não há nenhum tipo de item.<br>";
            }
            echo "
            <br><strong>Estado:</strong></br><input type='radio' id='at' value='active' name='estado_item' ><label for='at'>ativo</label><br>
            <input type='radio' id='inat' value='inactive' name='estado_item'><label for='inat'>inativo</label><br><input type='hidden' value='inserir' name='estado'>
            <input style='background-color: #00AF50; border: none; color: white; padding: 8px 20px; text-decoration: none; margin: 4px 2px; cursor: pointer;'type='submit' value='Inserir item' name='submit'>
            </form>
            </body>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>
