<?php
global $wp,$current_page,$clientsideval;
$current_page = add_query_arg(array(), $wp->request);
//Verifica se o utilizador está autenticado e tem uma certa capability
function verificaCapability($capability)
{
    return is_user_logged_in() && current_user_can($capability);
}

function testarInput($input)
{
    $input = trim($input);//ELIMINA ESPAÇOS NO INICIO E NO FINAL
    $input = stripslashes($input); //ELIMINA "\"
    $input = htmlspecialchars($input); //TRANSFORMA CARATERES ESPECIAIS EM ENTIDADES HTML
    return $input;
}

//Mostra ligação "Voltar atrás"
function voltarAtras()
{
    echo "<input type='button' class='atrasButton textoLabels' value='Voltar atrás' onClick='history.back();'></input>";
}


function get_enum_values($table, $field)
{
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $enum_array = array();
    $query = 'SHOW COLUMNS FROM `' . $table . '` LIKE "' . $field . '"';
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_row($result);
    preg_match_all('/\'(.*?)\'/', $row[1], $enum_array);
    foreach ($enum_array[1] as $mkey => $mval) {
        $enum_fields[$mkey + 1] = $mval;
    }
    return $enum_fields;
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

$clientsideval= false; //Usada para a validação clientside
