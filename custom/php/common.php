<?php

//Verifica se o utilizador está autenticado e tem uma certa capability
function verificaCapability($capability)
{
    return is_user_logged_in() && current_user_can($capability);
}

function testarInput($input){
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
    return $input;
}

//Mostra ligação "Voltar atrás"
function voltarAtras()
{
    echo "<input type='button' class='atrasButton textoLabels' value='Voltar atrás' onClick='history.back();'>";
}

//Recomendação do professor
function get_enum_values($connection, $table, $field)
{
    $query = " SHOW COLUMNS FROM `$table` LIKE '$field' ";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_array($result, MYSQL_NUM);
    #extract the values
    #the values are enclosed in single quotes
    #and separated by commas
    $regex = "/'(.*?)'/";
    preg_match_all($regex, $row[1], $enum_array);
    $enum_fields = $enum_array[1];
    return ($enum_fields);
}

//Realizar ligação à Base de Dados
function ligacaoBD()
{
    $ligacao = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!$ligacao) {
        die("Erro na ligação: " . mysqli_error());
}
    return $ligacao;
}

$clientsideval = 0; //Usada para a validação clientside
