<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_records")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        echo "Erro na ligação com a base de dados";
    } else {
        if($_REQUEST["estado"]=="validar"){
            echo"Validar";
        }
        elseif ($_REQUEST["estado"]=="inserir"){
            echo"Inserir";}
        else{
        echo "<h3>Dados de registo - introdução</h3>";
        echo "Introduza os dados pessoais básicos da criança:";
        echo "<body>
                <form method='post'>
                <p style='color:red;'>* Campos obrigatórios</p><br>
                <strong> Nome completo:</strong> * <input type='text' name='crianca_nome'><br>
                <strong>Data de nascimento: * <input type='text' name='data_de_nascimento'><br>
                <strong>Nome completo do encarregado de educação:</strong> * <input type='text' name='ee_nome'><br>
                <strong>Telefone do encarregado de educação:</strong> * <input type='text' name='ee_telefone'><br>
                <strong>Endereço de e-mail do tutor:</strong> <input type='text' name='tutor_email'><br><br>
                <input type='hidden' value='validar' name='estado'>
                <input type='submit'>
                </form>
            </body>";}
    }
} else {
    echo "Não tem autorização para aceder a esta página.";
}