<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_allowed_values")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        if ($_REQUEST["estado"] == "introducao") {
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_valores_permitidos.js', array('jquery'), 1.1, true);
            }
            $_SESSION["subitem_id"] = $_REQUEST["subitem"];
            echo "<div class='caixaSubTitulo'><h3><strong>Gestão de valores permitidos - introdução</strong></h3></div>
                <div class='caixaFormulario'><span class='warning'>Campos obrigatórios*</span><br><form method='post' > <strong class='textoLabels'>Valor<span class='warning'>*</span>: </strong><br><input type='text' class='textInput' id='valor_permitido' name='valor_permitido' ><br><br>";
            echo "<br><input type='hidden' value='inserir' name='estado'><input class='submitButton textoLabels' type='submit' value='Inserir valor permitido' name='submit'></form></div>";
        } else if ($_REQUEST["estado"] == "inserir") {
            echo "<div class='caixaSubTitulo'><h3><strong>Gestão de valores permitidos - inserção</strong></h3></div><div class='caixaFormulario'>";
            $faltaDado = false;
            $campos = "";
            if (empty($_REQUEST["valor_permitido"])) { //não escreveu valor
                $campos .= "<li><br><strong>Nome</strong></li>";
                $faltaDado = true;
            }
            if (!$faltaDado) { //não falta preencher nenhum campo obrigatório
                $insertQuery = "INSERT INTO subitem_allowed_value  (id, subitem_id, value, state) VALUES (NULL," . $_SESSION["subitem_id"] . ",'" . $_REQUEST["valor_permitido"] . "','active');";
                if (!mysqli_query($mySQL, $insertQuery)) {
                    echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL)."</span>";
                } else {
                    echo "<span class='information'>Inseriu os dados de novo valor permitido com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.</span><br>";
                    echo "<a href='gestao-de-valores-permitidos'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }
            } else {
                echo "<span>Os seguintes campos são <span class='warning'><strong>obrigatórios</strong></span>:</span><ul>" . $campos . "</ul>";
                voltarAtras();
            }
            echo "</div>";
        } else {
            $queryTodosSubitensEnum = "SELECT * FROM subitem WHERE value_type='enum'"; //TODOS SUBITENS TIPO VALOR ENUM
            $tabelaTodosSubitensEnum = mysqli_query($mySQL, $queryTodosSubitensEnum);

            if ($tabelaTodosSubitensEnum && mysqli_num_rows($tabelaTodosSubitensEnum) > 0) {
                echo "<table class='tabela'>";
                echo "<tr class='row'><th class='textoTabela cell'>item</th><th class='textoTabela cell'>id</th><th class='textoTabela cell'>subitem</th><th class='textoTabela cell'>id</th><th class='textoTabela cell'>valores permitidos</th><th class='textoTabela cell'>estado</th><th class='textoTabela cell'>ação</th></tr>";
                $queryItensComSubitens = "SELECT DISTINCT item.id, item.name FROM subitem, item  WHERE item.id = subitem.item_id AND subitem.value_type='enum'"; //TODOS ITENS
                $tabelaItensComSubitens = mysqli_query($mySQL, $queryItensComSubitens);
                while ($linhaItemComSubitens = mysqli_fetch_assoc($tabelaItensComSubitens)) { //POR CADA ITEM
                    $newItem = true;

                    $querySubitemEnum = "SELECT * FROM subitem WHERE item_id ='" . $linhaItemComSubitens["id"] . "' AND value_type='enum'";
                    $tabelaSubitemEnum = mysqli_query($mySQL, $querySubitemEnum);


                    $queryValoresPermitidosItem = "SELECT item.id, item.name, subitem_allowed_value.value FROM subitem, item, subitem_allowed_value WHERE item.id=subitem.item_id AND subitem_allowed_value.subitem_id=subitem.id AND item.id='" . $linhaItemComSubitens["id"] . "' AND subitem.value_type='enum'";
                    $tabelaValoresPermitidosItem = mysqli_query($mySQL, $queryValoresPermitidosItem);

                    $numeroValoresPermitidosItem = mysqli_num_rows($tabelaValoresPermitidosItem);

                    //Contar como +1 valor permitido, se não houverem valores permitidos para esse subitem (para contar no rowspan do item)
                    while ($linhaSubitemEnum = mysqli_fetch_assoc($tabelaSubitemEnum)) {
                        $queryValoresPermitidosSubitem = "SELECT * FROM subitem_allowed_value WHERE subitem_id =".$linhaSubitemEnum["id"];
                        $tabelaValoresPermitidosSubitem = mysqli_query($mySQL, $queryValoresPermitidosSubitem);
                        $numeroValoresPermitidosSubitem = mysqli_num_rows($tabelaValoresPermitidosSubitem);

                        if ($numeroValoresPermitidosSubitem == 0) {
                            $numeroValoresPermitidosItem++;
                        }
                    }

                    //Faz reset do apontador (voltar a usar o mysqli_fetch_assoc da mesma tabela)
                    mysqli_data_seek($tabelaSubitemEnum,0);

                    while ($linhaSubitemEnum = mysqli_fetch_assoc($tabelaSubitemEnum)) { //POR CADA SUBITEM
                        $newValorPermitido = true;

                        $queryValoresPermitidosSubitem = "SELECT * FROM subitem_allowed_value WHERE subitem_id =".$linhaSubitemEnum["id"];
                        $tabelaValoresPermitidosSubitem = mysqli_query($mySQL, $queryValoresPermitidosSubitem);

                        $numeroValoresPermitidosSubitem = mysqli_num_rows($tabelaValoresPermitidosSubitem);
                        if ($numeroValoresPermitidosSubitem == 0) {
                            if ($newItem) {
                                echo "<tr class='row'><td class='textoTabela cell' rowspan='$numeroValoresPermitidosItem'>" . $linhaItemComSubitens["name"] . "</td>"; //NOME DESSE TIPO
                                $newItem = false;
                            } else {
                                echo "<tr class='row'>";
                            }
                            if ($newValorPermitido) {
                                echo "<td class='textoTabela cell'>" . $linhaSubitemEnum["id"] . "</td>";
                                echo "<td class='textoTabela cell'><a href='gestao-de-valores-permitidos?estado=introducao&subitem=".$linhaSubitemEnum["id"]."'>[" . $linhaSubitemEnum["name"] . "]</a></td>";
                                $newValorPermitido = false;
                            }
                            echo "<td class='textoTabela cell' colspan='3'>Não há valores permitidos definidos</td>";
                            echo "<td class='textoTabela cell'>[editar] [desativar]</td></tr>";

                        } else {
                            while ($linhaValoresPermitidos = mysqli_fetch_assoc($tabelaValoresPermitidosSubitem)) {
                                if ($newItem) {
                                    echo "<tr class='row'><td class='textoTabela cell' rowspan='$numeroValoresPermitidosItem'>" . $linhaItemComSubitens["name"] . "</td>"; //NOME DESSE TIPO
                                    $newItem = false;
                                } else {
                                    echo "<tr class='row'>";
                                }
                                if ($newValorPermitido) {
                                    echo "<td class='textoTabela cell' rowspan='$numeroValoresPermitidosSubitem'>" . $linhaSubitemEnum["id"] . "</td>";
                                    echo "<td class='textoTabela cell' rowspan='$numeroValoresPermitidosSubitem'><a href='gestao-de-valores-permitidos?estado=introducao&subitem=".$linhaSubitemEnum["id"]."'>[" . $linhaSubitemEnum["name"] . "]</a></td>";
                                    $newValorPermitido = false;
                                }
                                echo "<td class='textoTabela cell'>" . $linhaValoresPermitidos["id"] . "</td>";
                                echo "<td class='textoTabela cell'>" . $linhaValoresPermitidos["value"] . "</td>";
                                echo "<td class='textoTabela cell'>" . ($linhaValoresPermitidos["state"] == 'active' ? 'ativo' : 'inativo') . "</td>";
                                echo "<td class='textoTabela cell'>[editar] [desativar]</td></tr>";
                            }
                        }
                    }
                }
                echo "</table>";
            } else {
                echo "<span class='information'>Não há subitems especificados cujo tipo de valor seja enum. Especificar primeiro novo(s) iten(s) e depois voltar a esta opção.</span><br>";
            }

        }
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>
