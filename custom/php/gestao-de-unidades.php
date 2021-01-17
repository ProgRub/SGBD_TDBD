<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_unit_types")) {//VERIFICAR SE UTILIZADOR FEZ LOGIN E TEM ESTA CAPACIDADE
    $mySQL = ligacaoBD();//ESTABELECE A LIGAÇÃO COM A BASE DE DADOS
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {//SELECIONAR A BASE DE DADOS, SE DER ERRO -> DIE
        die("Connection failed: " . mysqli_connect_error());
    } else {//SE NÃO DER ERRO
        if ($_REQUEST["estado"] == "inserir") {//SE ESTADO É INSERIR VAI-SE INSERIR NA BASE DE DADOS
            echo "<div class='caixaSubTitulo'><h3>Gestão de unidades - inserção</h3></div>";
            echo "<div class='caixaFormulario'>";
            $nomeUnidade = testarInput($_REQUEST["nome_unidade"]);

            //SE FOR TRUE, FALTOU PREENCHER ALGUM CAMPO OBRIGATÓRIO
            $faltaDado = false;
            //JUNTA OS NOMES DE TODOS OS CAMPOS EM FALTA NUMA LISTA
            $campos = "";

            if (estaVazio($nomeUnidade)) {
                $campos .= "<li><strong>Nome</strong></li>";
                $faltaDado = true;
            } else if (1 === preg_match('~[0-9]~', $nomeUnidade)) {
                $campos .= "<li><strong>Nome não deve conter números!</strong></li>";
                $faltaDado = true;
            }

            if (!$faltaDado) {//SE NÃO FALTA O NOME PODEMOS INSERIR NA BASE DE DADOS
                $insertQuery = "INSERT INTO subitem_unit_type (id, name) VALUES (NULL,'$nomeUnidade');";

                if (!mysqli_query($mySQL, $insertQuery)) {//SE HÁ ERRO AO INSERIR OS DADOS
                    echo "<span class='warning'>Erro: $insertQuery<br>mysqli_error($mySQL)</span>";
                } else {//INFORMAR UTILIZADOR DE INSERÇÃO COM SUCESSO E MOSTRAR BOTÃO PARA CONTINUAR
                    echo "<span class='information'>Inseriu os dados de novo tipo de unidade com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.</span><br>";
                    echo "<a href='gestao-de-unidades'><button class='continuarButton textoLabels'>Continuar</button></a>";
                }
            } else {
                //LISTA OS NOMES DOS CAMPOS EM FALTA
                echo "<span class='warning'>Os seguintes campos são <strong>obrigatórios</strong></span>:<ul>" . $campos . "</ul>";
                //BOTÃO PARA VOLTAR ATRÁS
                voltarAtras();
            }
            echo "</div>";
        } else {//SE O ESTADO NÃO FOR INSERIR
            if ($clientsideval) {
                wp_enqueue_script('script', get_bloginfo('wpurl') . '/custom/js/gestao_unidades.js', array('jquery'), 1.1, true);
            }
            $query = "SELECT id,name FROM subitem_unit_type ORDER BY name";//AS UNIDADES DEVEM SER ORDENADAS ALFABETICAMENTE
            $result = mysqli_query($mySQL, $query);
            if (mysqli_num_rows($result) > 0) {//SE HÁ UNIDADES NA BASE DE DADOS
                $table = "<table class='tabela'><tr><th class='textoTabela cell'>id</th><th class='textoTabela  cell'>unidade</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {//CADA LINHA DA TABELA TEM O ID E O NOME DA UNIDADE
                    $table .= "<tr class='row'><td class='textoTabela  cell'>" . $row["id"] . "</td><td class='textoTabela  cell'>" . $row["name"] . "</td></tr>";
                }
                $table .= "</table>";
                echo $table;
            } else {//SE NÃO HÁ UNIDADES INFORMAMOS O UTILIZADOR
                echo "<span class='information'>Não há tipos de unidades.</span>";
            }
            //FORMULÁRIO
            echo "<div class='caixaSubTitulo'><h3>Gestão de unidades - introdução</h3></div>
                    <div class='caixaFormulario'><span class='warning'>* Campos obrigatórios</span><br><br>";
            $action = get_site_url() . '/' . $current_page;
            echo "<form method ='post' action='$action'>";
            echo "<strong class='textoLabels'>Nome:</strong><span class='warning textoLabels'> * </span><br><input type='text' name='nome_unidade' id='nome_unidade' class='textInput'><br>
                        <input type='hidden' value='inserir' name='estado' class='textInput'><br>";
            echo "<input class='submitButton textoLabels' type='submit' value='Inserir tipo de unidade'>";
            echo "</form></div>";
        }
    }
} else {//SE UTILIZADOR NÃO FEZ LOGIN E/OU NÃO TEM A CAPACIDADE
    echo "<span class='warning'>Não tem autorização para aceder a esta página</span>";
}