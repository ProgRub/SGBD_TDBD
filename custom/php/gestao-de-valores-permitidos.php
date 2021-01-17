<?php
require_once("custom/php/common.php");
//SE TEM A CAPABILITY "manage_allowed_values":
if (verificaCapability("manage_allowed_values")) {

    //ESTABELECE CONEÇÃO COM A BASE DE DADOS:
    $mySQL = ligacaoBD();

    //MUDA A CONEÇÃO MYSQL E CASO SEJA FALSE, OCORREU UM ERRO:
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());

        //SE NÃO DEU ERRO:
    } else {

        //SE O ESTADO DE EXECUÇÃO FOR "INTRODUCAO":
        if ($_REQUEST["estado"] == "introducao") {

            //VALIDAÇÃO CLIENT-SIDE:
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_valores_permitidos.js', array('jquery'), 1.1, true);
            }

            //VARIAVEL DE SESSÃO COM O ID DO SUBITEM:
            $_SESSION["subitem_id"] = $_REQUEST["subitem"];

            //FORMULÁRIO PARA INSERIR UM NOVO VALOR PERMITIDO:
            $action = get_site_url() . '/' . $current_page;
            echo "<div class='caixaSubTitulo'><h3><strong>Gestão de valores permitidos - introdução</strong></h3></div>
                <div class='caixaFormulario'><span class='warning'>Campos obrigatórios*</span><br><form method='post' action='$action'>
                <strong class='textoLabels'>Valor<span class='warning'>*</span>: </strong><br>
                <input type='text' class='textInput' id='valor_permitido' name='valor_permitido' ><br>";
            echo "<br><input type='hidden' value='inserir' name='estado'>
            <input class='submitButton textoLabels' type='submit' value='Inserir valor permitido' name='submit'></form></div>";
            //AO SUBMETER, MUDA O ESTADO DE EXECUÇÃO PARA "inserir":

            //SE O ESTADO DE EXECUÇÃO FOR "INSERIR":
        } else if ($_REQUEST["estado"] == "inserir") {

            //SUBTITULO DA PAGINA:
            echo "<div class='caixaSubTitulo'><h3><strong>Gestão de valores permitidos - inserção</strong></h3></div><div class='caixaFormulario'>";

            //TRUE SE FALTA PREENCHER ALGUM CAMPO OBRIGATORIO:
            $faltaDado = false;

            //JUNTA OS NOMES DE TODOS OS CAMPOS EM FALTA PARA DEPOIS LISTA-LOS:
            $campos = "";

            //SE NÃO ESCREVEU NENHUM NOME PARA O VALOR PERMITIDO:
            if (estaVazio($_REQUEST["valor_permitido"])) {
                $campos .= "<li><strong>Valor</strong></li>";
                $faltaDado = true;
            }

            //SE PREENCHEU TODOS OS CAMPOS OBRIGATÓRIOS:
            if (!$faltaDado) {
                //CODIGO SQL PARA INSERÇÃO DO NOVO VALOR PERMITIDO NA BASE DE DADOS:
                $insertSQL = "INSERT INTO subitem_allowed_value  (id, subitem_id, value, state) VALUES (NULL," . $_SESSION["subitem_id"] . ",'" . $_REQUEST["valor_permitido"] . "','active');";

                //EXECUTA INSERÇÃO (SE OCORREU UM ERRO, DEVOLVE FALSE):
                if (!mysqli_query($mySQL, $insertSQL)) {
                    echo "<span class='warning'>Erro: " . $insertSQL . "<br>" . mysqli_error($mySQL) . "</span>";

                    //SE NÃO OCORREU NENHUM ERRO:
                } else {
                    //INDICA QUE A INSERÇÃO FOI SUCESSO:
                    echo "<span class='information'>Inseriu os dados de novo valor permitido com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.</span><br>";
                    echo "<a href='gestao-de-valores-permitidos'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }

                //SE NÃO PREENCHEU TODOS OS CAMPOS OBRIGATORIOS:
            } else {
                //LISTA O NOME DOS CAMPOS EM FALTA:
                echo "<span>Os seguintes campos são <span class='warning'><strong>obrigatórios</strong></span>:</span><ul>" . $campos . "</ul>";

                //BOTÃO PARA VOLTAR ATRÁS:
                voltarAtras();
            }
            echo "</div>";

        //SE O ESTADO DE EXECUÇÃO NÃO FOR "inserir":
        } else {
            //QUERY PARA OBTER TODOS OS SUBITENS COM TIPO DE VALOR ENUM:
            $queryTodosSubitensEnum = "SELECT * FROM subitem WHERE value_type='enum'";
            //RESULTADO DA EXECUÇÃO DA QUERY:
            $tabelaTodosSubitensEnum = mysqli_query($mySQL, $queryTodosSubitensEnum);

            //CASO HAJAM SUBITENS COM TIPO DE VALOR ENUM:
            if (mysqli_num_rows($tabelaTodosSubitensEnum) > 0) {
                //CABEÇALHO DA TABELA:
                echo "<table class='tabela'>";
                echo "<tr class='row'><th class='textoTabela cell'>item</th><th class='textoTabela cell'>id</th>
                <th class='textoTabela cell'>subitem</th><th class='textoTabela cell'>id</th><th class='textoTabela cell'>valores permitidos</th>
                <th class='textoTabela cell'>estado</th><th class='textoTabela cell'>ação</th></tr>";

                //QUERY PARA OBTER TODOS OS ITENS QUE TÊM SUBITENS ENUM:
                $queryItensComSubitens = "SELECT DISTINCT item.id, item.name FROM subitem, item  WHERE item.id = subitem.item_id AND subitem.value_type='enum' ORDER BY item.name";
                //RESULTADO DA QUERY:
                $tabelaItensComSubitens = mysqli_query($mySQL, $queryItensComSubitens);


                //PARA CONTAR O NÚMERO DE ITENS: (PARA O CSS)
                $numeroItens = 0;
                //PARA CONTAR O NÚMERO DE SUBITENS: (PARA O CSS)
                $numeroSubitens=0;

                //PERCORRE TABELA RESULTADO COM OS ITENS DAQUELE SUBITEM:
                while ($linhaItemComSubitens = mysqli_fetch_assoc($tabelaItensComSubitens)) {
                    //FALSE APÓS CRIAR A CELULA COM O NOME DO ITEM PARA EVITAR QUE SEJA CRIADA VARIAS VEZES:
                    $newItem = true;

                    //QUERY PARA OBTER TODOS OS SUBITENS DO TIPO ENUM DAQUELE ITEM:
                    $querySubitemEnum = "SELECT * FROM subitem WHERE item_id ='" . $linhaItemComSubitens["id"] . "' AND value_type='enum' ORDER BY name";
                    $tabelaSubitemEnum = mysqli_query($mySQL, $querySubitemEnum);

                    //QUERY PARA OBTER TODOS OS VALORES PERMITIDOS DO ITEM:
                    $queryValoresPermitidosItem = "SELECT item.id, item.name, subitem_allowed_value.value FROM subitem, item, subitem_allowed_value WHERE item.id=subitem.item_id AND subitem_allowed_value.subitem_id=subitem.id AND item.id='" . $linhaItemComSubitens["id"] . "' AND subitem.value_type='enum'";
                    $tabelaValoresPermitidosItem = mysqli_query($mySQL, $queryValoresPermitidosItem);

                    //NÚMERO DE VALORES PERMITIDOS DAQUELE ITEM (PARA ROWSPAN):
                    $numeroValoresPermitidosItem = mysqli_num_rows($tabelaValoresPermitidosItem);

                    //PARA VERIFICAR SE HÁ SUBITENS DAQUELE ITEM QUE NÃO TÊM VALORES PERMITIDOS:
                    //PERCORRE TABELA RESULTADO DE TODOS OS SUBITENS DO TIPO ENUM DAQUELE ITEM:
                    while ($linhaSubitemEnum = mysqli_fetch_assoc($tabelaSubitemEnum)) {
                        //QUERY PARA OBTER TODOS OS VALORES PERMITIDOS DE CADA SUBITEM DAQUELE ITEM:
                        $queryValoresPermitidosSubitem = "SELECT * FROM subitem_allowed_value WHERE subitem_id =" . $linhaSubitemEnum["id"];
                        $tabelaValoresPermitidosSubitem = mysqli_query($mySQL, $queryValoresPermitidosSubitem);
                        $numeroValoresPermitidosSubitem = mysqli_num_rows($tabelaValoresPermitidosSubitem);
                        //SE O SUBITEM (DAQUELE ITEM) NÃO TIVER VALORES PERMITIDOS:
                        if ($numeroValoresPermitidosSubitem == 0) {
                            //CONTA COMO MAIS UM VALOR PERMITIDO PARA O ITEM (PARA O ROWSPAN DO ITEM):
                            $numeroValoresPermitidosItem++;
                        }
                    }

                    //FAZ RESET DO APONTADOR (PARA VOLTAR A USAR O "mysqli_fetch_assoc")
                    mysqli_data_seek($tabelaSubitemEnum, 0);

                    //PERCORRE TABELA RESULTADO DE TODOS OS SUBITENS DO TIPO ENUM DAQUELE ITEM:
                    while ($linhaSubitemEnum = mysqli_fetch_assoc($tabelaSubitemEnum)) {
                        //PARA EVITAR CRIAR A CELULA DO SUBITEM VARIAS VEZES:
                        $newSubitem = true;

                        //QUERY PARA OBTER TODOS OS VALORES PERMITIDOS DE CADA SUBITEM DAQUELE ITEM:
                        $queryValoresPermitidosSubitem = "SELECT * FROM subitem_allowed_value WHERE subitem_id =" . $linhaSubitemEnum["id"] . " ORDER BY value";
                        $tabelaValoresPermitidosSubitem = mysqli_query($mySQL, $queryValoresPermitidosSubitem);

                        //NÚMERO DE VALORES PERMITIDOS DE CADA SUBITEM (PARA ROWSPAN):
                        $numeroValoresPermitidosSubitem = mysqli_num_rows($tabelaValoresPermitidosSubitem);

                        //SE AQUELE SUBITEM NÃO TEM VALORES PERMITIDOS:
                        if ($numeroValoresPermitidosSubitem == 0) {
                            //SE É UM ITEM DIFERENTE NA TABELA (CRIA APENAS UMA VEZ):
                            if ($newItem) {
                                //CRIA CELULA COM O NOME DO ITEM:
                                echo "<tr class='row'><td class='textoTabela cell " . ($numeroItens % 2 == 0 ? "par" : "impar") . "' rowspan='$numeroValoresPermitidosItem'>" . $linhaItemComSubitens["name"] . "</td>";
                                //FICA FALSE PARA EVITAR QUE SEJA CRIADO NOVAMENTE:
                                $newItem = false;
                                //INCREMENTA NUMERO DE ITENS:
                                $numeroItens++;

                            //SE É O MESMO ITEM:
                            } else {
                                echo "<tr class='row'>";
                            }
                            //SE É UM VALOR SUBITEM DIFERENTE (CRIAR APENAS UMA VEZ):
                            if ($newSubitem) {
                                //CRIAR CELULA COM O ID DO SUBITEM:
                                echo "<td class='textoTabela cell " . ($numeroSubitens % 2 == 0 ? "par" : "impar") . "'>" . $linhaSubitemEnum["id"] . "</td>";

                                //CRIA CELULA COM O NOME DO SUBITEM QUE É UM LINK PARA O FORMULARIO DE INSERÇÃO DE UM NOVO VALOR PERMITIDO DAQUELE SUBITEM:
                                echo "<td class='textoTabela cell " . ($numeroSubitens % 2 == 0 ? "par" : "impar") . "'><a href='gestao-de-valores-permitidos?estado=introducao&subitem=" . $linhaSubitemEnum["id"] . "'>[" . $linhaSubitemEnum["name"] . "]</a></td>";
                                //FICA FALSE PARA EVITAR QUE SEJA CRIADO NOVAMENTE:
                                $newSubitem = false;
                                //INCREMENTA NUMERO DE SUBITENS:
                                $numeroSubitens++;
                            }
                            //AVISA QUE NÃO HÁ VALORES PERMITIDOS DEFINIDOS PARA AQUELE ITEM:
                            echo "<td class='textoTabela cell' colspan='4'>Não há valores permitidos definidos</td>";

                            //SE AQUELE SUBITEM TEM VALORES PERMITIDOS:
                        } else {
                            //PERCORRE A TABELA RESULTADO DE TODOS OS VALORES PERMITIDOS DE CADA SUBITEM DAQUELE ITEM:
                            while ($linhaValoresPermitidos = mysqli_fetch_assoc($tabelaValoresPermitidosSubitem)) {
                                //SE É UM ITEM DIFERENTE NA TABELA (CRIA APENAS UMA VEZ):
                                if ($newItem) {
                                    //CRIA CELULA COM O NOME DO ITEM:
                                    echo "<tr class='row'><td class='textoTabela cell " . ($numeroItens % 2 == 0 ? "par" : "impar") . "' rowspan='$numeroValoresPermitidosItem'>" . $linhaItemComSubitens["name"] . "</td>";
                                    //FICA FALSE PARA EVITAR QUE CRIE A CELULA NOVAMENTE:
                                    $newItem = false;
                                    //INCREMENTA NUMERO DE ITENS:
                                    $numeroItens++;

                                //SE NÃO É UM ITEM DIFERENTE:
                                } else {
                                    echo "<tr class='row'>";
                                }
                                //SE É UM VALOR SUBITEM DIFERENTE (CRIAR APENAS UMA VEZ):
                                if ($newSubitem) {
                                    //CRIA CELULA COM O ID DO SUBITEM E OUTRA COM O NOME DO SUBITEM QUE É UM LINK PARA UM FORMULARIO PARA INSERIR UM VALOR PERMITIDO DAQUELE SUBITEM:
                                    echo "<td class='textoTabela cell " . ($numeroSubitens % 2 == 0 ? "par" : "impar") . "' rowspan='$numeroValoresPermitidosSubitem'>" . $linhaSubitemEnum["id"] . "</td>";
                                    echo "<td class='textoTabela cell " . ($numeroSubitens % 2 == 0 ? "par" : "impar") . "' rowspan='$numeroValoresPermitidosSubitem'>
                                    <a href='gestao-de-valores-permitidos?estado=introducao&subitem=" . $linhaSubitemEnum["id"] . "'>[" . $linhaSubitemEnum["name"] . "]</a></td>";
                                    //FICA FALSE PARA EVITAR QUE SEJAM CRIADO NOVAMENTE:
                                    $newSubitem = false;
                                    //INCREMENTA NUMERO DE SUBITENS:
                                    $numeroSubitens++;
                                }
                                //CRIA CELULAR COM O ID, VALOR E ESTADO DO VALOR PERMITIDO:
                                echo "<td class='textoTabela cell'>" . $linhaValoresPermitidos["id"] . "</td>";
                                echo "<td class='textoTabela cell'>" . $linhaValoresPermitidos["value"] . "</td>";
                                echo "<td class='textoTabela cell'>" . ($linhaValoresPermitidos["state"] == 'active' ? 'ativo' : 'inativo') . "</td>";

                                //CRIA CELULA COM 2 LINKS, UM PARA EDITAR O VALOR PERMITIDO E OUTRO PARA ATIVAR/DESATIVAR O VALOR PERMITIDO:
                                echo "<td class='textoTabela cell'><a href='edicao-de-dados?estado=editar&id=" . $linhaValoresPermitidos["id"] . "&tipo=valorPermitido'>[editar]</a><a href='edicao-de-dados?estado=" . ($linhaValoresPermitidos["state"] == 'active' ? 'desativar' : 'ativar') . "&id=" . $linhaValoresPermitidos["id"] . "&tipo=valorPermitido'>" . ($linhaValoresPermitidos["state"] == 'active' ? ' [desativar]' : ' [ativar]') . "</a></td></tr>";
                            }
                        }
                    }
                }
                echo "</table>";


            //SE NÃO HOUVEREM SUBITENS CUJO TIPO DE VALOR SEJA ENUM:
            } else {
                echo "<span class='information'>Não há subitems especificados cujo tipo de valor seja enum. Especificar primeiro novo(s) iten(s) e depois voltar a esta opção.</span><br>";
            }
        }
    }
//CASO NÃO TENHA A CAPABILITY:
} else {
    echo "<span class='warning'>Não tem autorização para aceder a esta página</span>";
}
