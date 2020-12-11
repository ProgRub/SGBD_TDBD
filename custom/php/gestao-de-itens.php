<?php

    require_once("custom/php/common.php");
    if(verificaCapability("manage_items")){
        echo "Tem autorização para aceder a esta página";
    }else{
        echo "Não tem autorização para aceder a esta página";
    }
