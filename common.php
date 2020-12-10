<?php

    //Verifica se o utilizador está logado e tem uma certa capability
    function verificaCapability($capability){
        if(is_user_logged_in() && current_user_can($capability)){
            return true;
        }else{
            return false;
        }
    }

    //Mostra ligação "Voltar atrás"
    function voltarAtras(){
        echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
        <noscript>
        <a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>
        </noscript>";
    }

    //Recomendação do professor
    function get_enum_values($connection, $table, $field ){
        $query = " SHOW COLUMNS FROM `$table` LIKE '$field' ";
        $result = mysqli_query($connection, $query );
        $row = mysqli_fetch_array($result , MYSQL_NUM );
        #extract the values
        #the values are enclosed in single quotes
        #and separated by commas
        $regex = "/'(.*?)'/";
        preg_match_all( $regex , $row[1], $enum_array );
        $enum_fields = $enum_array[1];
        return( $enum_fields );
    }

    //Realizar ligação à Base de Dados
    function ligacaoBD(){
        $ligacao = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
        return $ligacao;
    }

    $link = ligacaoBD();

    $clientsideval=0; //Usada para a validação clientside
