<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_items")) {

    $mySQL = ligacaoBD();

    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());

    } else {
        if ($_REQUEST["estado"] == "inserir") {
            echo "<div class='caixaSubTitulo'><h3>Gestão de itens - inserção</h3></div>";
            echo "<div class='caixaFormulario'>";
            $faltaDado = false;
            $campos = "";
            if (empty($_REQUEST["nome_item"])) { //não escreveu nome
                $campos .= "<li><br><strong>Nome</strong></li>";
                $faltaDado = true;
            }
//            if (empty($_REQUEST["tipo_item"])) { //não escolheu tipo
//                $campos .= "<li><br><strong>Tipo</strong></li>";
//                $faltaDado = true;
//            }
//            if (empty($_REQUEST["estado_item"])) { //não escolheu estado
//                $campos .= "<li><strong>Estado</strong></li>";
//                $faltaDado = true;
//            }
            if (!$faltaDado) { //não falta preencher nenhum campo obrigatório
                $insertQuery = "INSERT INTO item (id, name,item_type_id,state) VALUES (NULL,'" . testarInput($_REQUEST["nome_item"]) . "'," . $_REQUEST["tipo_item"] . ",'" . $_REQUEST["estado_item"] . "');";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL)."</span>";
                } else {
                    echo "<span class='information'>Inseriu os dados de novo item com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
//                    echo "<a href='gestao-de-itens'>Continuar</a>";
                    echo "<a href='gestao-de-itens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }
            } else {
                echo "<span class='warning'>Os seguintes campos são <strong>obrigatórios</strong></span>:<ul>" . $campos . "</ul>";
                voltarAtras();
            }
            echo "</div>";
        } else {
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_itens.js', array('jquery'), 1.1, true);
            }
            if (mysqli_num_rows(mysqli_query($mySQL, "SELECT * FROM item")) > 0) { //ver se há items na tabela item
                $queryTipos = "SELECT * FROM item_type ORDER BY name"; //TODOS OS TIPOS DE ITENS
                $tabelaTipos = mysqli_query($mySQL, $queryTipos);
                if (mysqli_num_rows($tabelaTipos) > 0) {
                    echo "<table class='tabela'>";
                    echo "<tr class='row'><th class='textoTabela cell'>tipo de item</th><th class='textoTabela cell'>id</th><th class='textoTabela cell'>nome do item</th><th class='textoTabela cell'>estado</th><th class='textoTabela cell'>ação</th></tr>";

                    while ($linhaTipoItem = mysqli_fetch_assoc($tabelaTipos)) { //CADA TIPO DE ITEM
                        $queryItens = "SELECT * FROM item WHERE item_type_id = " . $linhaTipoItem["id"] . " ORDER BY name"; //TODOS OS ITENS DESSE TIPO
                        $tabelaItens = mysqli_query($mySQL, $queryItens);

                        if (mysqli_num_rows($tabelaItens) > 0) {
                            $newItem = true;
                            $numeroItens = mysqli_num_rows($tabelaItens);
                            while ($linhaItem = mysqli_fetch_assoc($tabelaItens)) {
                                //echo "<tr><td>" . $linhaTipoItem["name"] . "</td><td>" . $linhaItem["id"] . "</td><td>" . $linhaItem["name"] . "</td><td>" . ($linhaItem["state"] == 'active' ? 'ativo' : 'inativo') . "</td><td>[editar] [desativar]</td></tr>";
                                if ($newItem) {
                                    echo "<tr class='row'><td class='textoTabela cell' rowspan='$numeroItens'>" . $linhaTipoItem["name"] . "</td>"; //NOME DESSE TIPO
                                    $newItem = false;
                                } else {
                                    echo "<tr class='row'>";
                                }
                                echo "<td  class='textoTabela cell'>" . $linhaItem["id"] . "</td><td class='textoTabela cell'>" . $linhaItem["name"] . "</td><td class='textoTabela cell'>" . ($linhaItem["state"] == 'active' ? 'ativo' : 'inativo') . "</td><td class='textoTabela cell'>[editar] [desativar]</td>"; //DADOS DE CADA ITEM DESSE TIPO
                                echo "</tr>";
                            }
                        }
                    }
                    echo "</table>";
                }
            } else {
                echo "Não há itens.";
            }
            $queryTipos = "SELECT * FROM item_type"; //TODOS OS TIPOS DE ITENS
            $tabelaTipos = mysqli_query($mySQL, $queryTipos);
            echo "<div class='caixaSubTitulo'><h3><strong>Gestão de itens - introdução</strong></h3></div>
            <div class='caixaFormulario'><form method='post' > <strong>Nome: </strong><br><input type='text' class='textInput' name='nome_item' id='nome_item' ><br><br>";
            echo "<br><strong>Tipo: </strong></br>";
            $primeiro = true;
            if (mysqli_num_rows($tabelaTipos) > 0) {
                while ($linhaTipo = mysqli_fetch_assoc(($tabelaTipos))) {
                    if ($primeiro) {
                        echo '<input  type="radio" name="tipo_item"  checked value=' . $linhaTipo["id"] . '><span class="textoLabels" >' . $linhaTipo["name"] . '</span><br>';
                        $primeiro = false;
                    } else {
                        echo '<input  type="radio" name="tipo_item" value=' . $linhaTipo["id"] . '><span class="textoLabels" >' . $linhaTipo["name"] . '</span><br>';
                    }
                }
            } else if ($tabelaTipos == true && mysqli_num_rows($tabelaTipos) == 0) {
                echo "Não há nenhum tipo de item.<br>";
            }
            echo "
            <br><strong>Estado:</strong></br><input type='radio' id='at' value='active' name='estado_item' checked><span class='textoLabels' for='at'>ativo</span><br>
            <input type='radio' id='inat' value='inactive' name='estado_item'><span for='inat' class='textoLabels' >inativo</span><br><input type='hidden' value='inserir' name='estado'>
            <input class='submitButton textoLabels' type='submit' value='Inserir item' name='submit'>
            </form></div>";
        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>
