<?php
require_once("custom/php/common.php");
if (verificaCapability("insert_values")) {
    echo "Capability";
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    }else{
        if ($_REQUEST["estado"] == "escolher_crianca") {}
        elseif ($_REQUEST["estado"] == "escolher_item") {}
        elseif ($_REQUEST["estado"] == "introducao") {}
        elseif ($_REQUEST["estado"] == "validar") {}
        elseif ($_REQUEST["estado"] == "inserir") {}
        else{}
    }
}else{
    echo "Não tem autorização para aceder a esta página";
}