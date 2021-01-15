<?php
require_once("custom/php/common.php");
//VERIFICA SE TEM A CAPABILITY "manage_items":
if (verificaCapability("manage_items")) {

    //ESTABELECE CONEÇÃO COM A BASE DE DADOS:
    $mySQL = ligacaoBD();

    //MUDA A CONEÇÃO MYSQL E CASO SEJA FALSE, OCORREU UM ERRO:
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    //SE NÃO DEU ERRO:
    } else {
        //SE O ESTADO DE EXECUÇÃO FOR "INSERIR":
        if ($_REQUEST["estado"] == "inserir") {
            //SUBTITULO DA PAGINA:
            echo "<div class='caixaSubTitulo'><h3>Gestão de itens - inserção</h3></div>";
            echo "<div class='caixaFormulario'>";

            //SE FOR TRUE, FALTOU PREENCHER ALGUM CAMPO OBRIGATÓRIO:
            $faltaDado = false;
            //JUNTA OS NOMES DE TODOS OS CAMPOS EM FALTA PARA DEPOIS LISTA-LOS:
            $campos = "";

            //SE NÃO PREENCHEU O NOME:
            if (empty($_REQUEST["nome_item"])) {
                $campos .= "<li><br><strong>Nome</strong></li>";
                $faltaDado = true;
            }
            //SE NÃO PREENCHEU O TIPO:
            if (empty($_REQUEST["tipo_item"])) {
                $campos .= "<li><br><strong>Tipo</strong></li>";
                $faltaDado = true;
            }
            //SE NÃO ESCOLHEU O ESTADO:
            if (empty($_REQUEST["estado_item"])) {
               $campos .= "<li><strong>Estado</strong></li>";
               $faltaDado = true;
            }
            //SE NÃO FALTOU PREENCHER NENHUM CAMPO OBRIGATÓRIO:
            if (!$faltaDado) {
                //INSERÇÃO DO NOVO ITEM NA BASE DE DADOS (COM OS VALORES PRETENDIDOS):
                $insertQuery = "INSERT INTO item (id, name,item_type_id,state) VALUES (NULL,'" . testarInput($_REQUEST["nome_item"]) . "'," . $_REQUEST["tipo_item"] . ",'" . $_REQUEST["estado_item"] . "');";

                //SE OCORREU NENHUM ERRO NA INSERÇÃO:
                if (!mysqli_query($mySQL, $insertQuery)) {
                    //MOSTRA ERRO QUE OCORREU:
                    echo "<span class='warning'>Erro: " . $insertQuery . "<br>" . mysqli_error($mySQL)."</span>";

                //SE NÃO OCORREU NENHUM ERRO NA INSERÇÃO:
                } else {
                    //INFORMA QUE A INSERÇÃO FOI UM SUCESSO:
                    echo "<span class='information'>Inseriu os dados de novo item com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.<br></span>";
                    //BOTÃO PARA CONTINUAR (IR PARA A PÁGINA "INICIAL" ONDE ESTÁ APRESENTADA A TABELA):
                    echo "<a href='gestao-de-itens'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }

            //SE FALTOU PREENCHER ALGUM CAMPO OBRIGATÓRIO:
            } else {
                //LISTA OS NOMES DOS CAMPOS EM FALTA:
                echo "<span class='warning'>Os seguintes campos são <strong>obrigatórios</strong></span>:<ul>" . $campos . "</ul>";
                //BOTÃO PARA VOLTAR ATRÁS:
                voltarAtras();
            }
            echo "</div>";

        //SE O ESTADO DE EXECUÇÃO NÃO FOR "inserir":
        } else {
            //VALIDAÇÃO CLIENT-SIDE:
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_itens.js', array('jquery'), 1.1, true);
            }

            //SE HOUVER ITENS NA BASE DE DADOS:
            if (mysqli_num_rows(mysqli_query($mySQL, "SELECT * FROM item")) > 0) {
                //QUERY PARA OBTER TODOS OS TIPOS DE ITEM:
                $queryTipos = "SELECT * FROM item_type ORDER BY name";
                $tabelaTipos = mysqli_query($mySQL, $queryTipos);

                //SE HÁ TIPOS DE ITEM NA BASE DE DADOS:
                if (mysqli_num_rows($tabelaTipos) > 0) {
                    //CABEÇALHO DA TABELA:
                    echo "<table class='tabela'>";
                    echo "<tr class='row'><th class='textoTabela cell'>tipo de item</th><th class='textoTabela cell'>id</th><th class='textoTabela cell'>nome do item</th><th class='textoTabela cell'>estado</th><th class='textoTabela cell'>ação</th></tr>";

                    //PERCORRE TABELA RESULTADO DA QUERY DE TODOS OS TIPOS DE ITEM:
                    while ($linhaTipoItem = mysqli_fetch_assoc($tabelaTipos)) {
                        //QUERY PARA OBTER TODOS OS ITENS DAQUELE TIPO:
                        $queryItens = "SELECT * FROM item WHERE item_type_id = " . $linhaTipoItem["id"] . " ORDER BY name";
                        $tabelaItens = mysqli_query($mySQL, $queryItens);

                        //SE HOUVEREM ITENS DESSE TIPO:
                        if (mysqli_num_rows($tabelaItens) > 0) {
                            //PARA EVITAR QUE CRIE SEMPRE A CÉLULA COM O NOME DO TIPO DE ITEM:
                            $newItem = true;

                            //NÚMERO DE ITENS DAQUELE TIPO:
                            $numeroItens = mysqli_num_rows($tabelaItens);

                            //PERCORRE TABELA RESULTADO DA QUERY PARA OBTER TODOS OS ITENS DE UM TIPO DE ITEM:
                            while ($linhaItem = mysqli_fetch_assoc($tabelaItens)) {
                                //SE FOR A PRIMEIRA VEZ QUE CRIA A CÉLULA COM O NOME DO TIPO:
                                if ($newItem) {
                                    //CELULA COM NOME DO TIPO DE ITEM E ROWSPAN IGUAL AO NÚMERO DE ITENS DESSE TIPO:
                                    echo "<tr class='row'><td class='textoTabela cell' rowspan='$numeroItens'>" . $linhaTipoItem["name"] . "</td>";
                                    //PARA EVITAR QUE CRIE NOVAMENTE A MESMA CÉLULA:
                                    $newItem = false;
                                } else {
                                    echo "<tr class='row'>";
                                }
                                //CRIA CELULAS COM VALORES DAQUELE ITEM E LINKS PARA EDITAR E ATIVAR/DESATIVAR O ITEM:
                                echo "<td  class='textoTabela cell'>" . $linhaItem["id"] . "</td><td class='textoTabela cell'>" . $linhaItem["name"] . "</td><td class='textoTabela cell'>" . ($linhaItem["state"] == 'active' ? 'ativo' : 'inativo') . "
                                </td><td class='textoTabela cell'>
                                <a href='edicao-de-dados?estado=editar&id=".$linhaItem["id"]."&tipo=item' >[editar] </a><a href='edicao-de-dados?estado=".($linhaItem["state"] == 'active' ? 'desativar' : 'ativar')."&id=".$linhaItem["id"]."&tipo=item'>".($linhaItem["state"] == 'active' ? '[desativar]' : '[ativar]')."</a> </td>";
                                echo "</tr>";
                            }
                        }
                    }
                    echo "</table>";
                }
            //SE NÃO HOUVEREM ITENS NA BASE DE DADOS:
            } else {
                echo "Não há itens.";
            }

            //INDEPENDENTEMENTE DE HAVER ITENS OU NÃO, PERMITE A INSERÇÃO DE UM NOVO ITEM:

            //QUERY PARA OBTER TODOS OS TIPOS DE ITENS:
            $queryTipos = "SELECT * FROM item_type";
            $tabelaTipos = mysqli_query($mySQL, $queryTipos);

            //SUBTITULO DA PAGINA E INICIO DO FORMULÁRIO:
            $action=get_site_url().'/'.$current_page;
            echo "<div class='caixaSubTitulo'><h3><strong>Gestão de itens - introdução</strong></h3></div>
            <div class='caixaFormulario'><form method='post' action='$action'> <strong>Nome: </strong><br><input type='text' class='textInput' name='nome_item' id='nome_item' ><br><br>"; //TEXTBOX PARA ESCREVER O NOME DO ITEM A INSERIR:
            echo "<br><strong>Tipo: </strong></br>";

            //PARA MARCAR CHECKED NO PRIMEIRO ITEM APRESENTADO:
            $primeiro = true;

            //SE HOUVEREM TIPOS DE ITEM NA BASE DE DADOS:
            if (mysqli_num_rows($tabelaTipos) > 0) {
                //PERCORRE A TABELA RESULTADO DA QUERY PARA OBTER OS TIPOS DE ITEM:
                while ($linhaTipo = mysqli_fetch_assoc(($tabelaTipos))) {

                    //SE FOR O PRIMEIRO ITEM APRESENTADO, MARCA CHECKED:
                    if ($primeiro) {
                        //OPÇÃO RADIO COM O NOME DO TIPO DE ITEM:
                        echo '<input  type="radio" name="tipo_item"  checked value=' . $linhaTipo["id"] . '><span class="textoLabels" >' . $linhaTipo["name"] . '</span><br>';
                        //MUDA PARA FALSE, PARA EVITAR COLOCAR CHECKED NAS RESTANTES OPÇÕES:
                        $primeiro = false;

                    //PARA OS RESTANTES TIPOS DE ITEM:
                    } else {
                        //OPÇÃO RADIO COM O NOME DO TIPO DO ITEM (SEM ESTAR CHECKED):
                        echo '<input  type="radio" name="tipo_item" value=' . $linhaTipo["id"] . '><span class="textoLabels" >' . $linhaTipo["name"] . '</span><br>';
                    }
                }
            //SE NÃO HÁ TIPOS DE ITEM NA BASE DE DADOS:
            } else if (mysqli_num_rows($tabelaTipos) == 0) {
                //INDICA QUE NÃO HÁ TIPOS DE ITEM:
                echo "Não há nenhum tipo de item.<br>";
            }

            //OPÇÕES RADIO COM OS VALORES "ATIVO" E "INATIVO" PARA O ESTADO DO ITEM A INSERIR E BOTÃO DE SUBMIT:
            echo "
            <br><strong>Estado:</strong></br><input type='radio' id='at' value='active' name='estado_item' checked><span class='textoLabels' for='at'>ativo</span><br>
            <input type='radio' id='inat' value='inactive' name='estado_item'><span for='inat' class='textoLabels' >inativo</span><br>
            <input type='hidden' value='inserir' name='estado'>
            <input class='submitButton textoLabels' type='submit' value='Inserir item' name='submit'>
            </form></div>";
            //AO SUBMETER, O VALOR DO ESTADO DE EXECUÇÃO MUDA PARA "inserir"
        }
    }
//SE NÃO TIVER A CAPABILITY:
} else {
    echo "Não tem autorização para aceder a esta página";
}
?>
