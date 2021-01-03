<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_unit_types")) {//verificar se utilizador fez login e tem esta capacidade
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {//se selecionar a base de dados der erro
        die("Connection failed: " . mysqli_connect_error());
    } else {
        if ($_REQUEST["estado"] == "inserir") {//Vai-se inserir os dados na base de dados
            echo "<div class='caixaSubTitulo'><h3>Gestão de unidades - inserção</h3></div>";
            echo "<div class='caixaFormulario'>";
            $nomeUnidade = testarInput($_REQUEST["nome_unidade"]);
            if (!empty($nomeUnidade)) {//se após testar o input este não for vazio, pode-se inserir os dados
                $insertQuery = "INSERT INTO subitem_unit_type (id, name) VALUES (NULL,'$nomeUnidade');";
                if (!mysqli_query($mySQL, $insertQuery)) {//se há erro ao inserir os dados
                    echo "<span class='warning'>Erro: $insertQuery<br>mysqli_error($mySQL)</span>";
                } else {//informar o utilizador da inserção com sucesso e apresentar botão para continuar
                    echo "<span class='information'>Inseriu os dados de novo tipo de unidade com sucesso.<br>Clique em <strong>Continuar</strong> para avançar.</span><br>";
                    echo "<a href='gestao-de-itens'><input type='submit' class='continuarButton textoLabels' value='Continuar'>";
                }
            } else {//se o input está vazio, informar utilizador e botão para voltar atrás
                echo "<div class='textoTabela'>O campo <strong>'Nome'</strong> é obrigatório!\n</div>";
                voltarAtras();
            }
            echo "</div>";
        } else {//apresentar tabela com todos as unidades e formulário para inserir
            $query = "SELECT id,name FROM subitem_unit_type ORDER BY name";//as unidades devem ser ordenadas alfabeticamente
            $result = mysqli_query($mySQL, $query);
            if (mysqli_num_rows($result) > 0) {//se há unidades na base de dados faz-se a tabela
                $table = "<table class='tabela'><tr><th class='textoTabela cell'>id</th><th class='textoTabela  cell'>unidade</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {//cada linha da tabela tem o id e o nome da unidade
                    $table .= "<tr class='row'><td class='textoTabela  cell'>" . $row["id"] . "</td><td class='textoTabela  cell'>" . $row["name"] . "</td></tr>";
                }
                $table .= "</table>";
                echo $table;
            } else {//se não há unidades informamos o utilizador
                echo "<span class='information'>Não há tipos de unidades.</span>";
            }
            //formulário
            echo "<div class='caixaSubTitulo'><h3>Gestão de unidades - introdução</h3></div>
                    <div class='caixaFormulario'><form method='post' > <strong class='textoLabels'>Nome:</strong><span class='warning textoLabels' textoLabels'> * </span><br><input type='text'  name='nome_unidade' class='textoLabels'><br>
                        <input type='hidden' value='inserir' name='estado'><br>
                        <input class='submitButton textoLabels' type='submit' value='Inserir tipo de unidade' name='submit'>
                    </form></div>";
        }
    }
} else {
    echo "<span class='information'>Não tem autorização para aceder a esta página</span>";
}
?>
