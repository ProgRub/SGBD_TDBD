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
                * Campos obrigatórios<br><br>
                Nome completo * <input type='text' name='crianca_nome'><br>
                Data de nascimento * <input type='text' name='data_de_nascimento'><br>
                Nome completo do encarregado de educação * <input type='text' name='ee_nome'><br>
                Telefone do encarregado de educação * <input type='text' name='ee_telefone'><br>
                Endereço de e-mail do tutor <input type='text' name='tutor_email'><br><br>
                <input type='hidden' value='validar' name='estado'>
                <input type='submit'>
                </form>
            </body>";}
    }
} else {
    echo "Não tem autorização para aceder a esta página";
}