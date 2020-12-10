<?php

    require_once("custom/php/common.php");
    if(verificaCapability("manage items")){

    }else{
        echo "Não tem autorização para aceder a esta página";
    }
