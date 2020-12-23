<?php
require_once("custom/php/common.php");
if (verificaCapability("manage_records")) {
    $mySQL = ligacaoBD();
    if (!mysqli_select_db($mySQL, "bitnami_wordpress")) {
        die("Connection failed: " . mysqli_connect_error());
    } 
	else {
		if ($_POST["estado"] == "validar") {			
		}
		else{
            if (mysqli_num_rows(mysqli_query($mySQL, "SELECT * FROM subitem")) > 0) { 
                $queryItem = "SELECT * FROM item ORDER BY name";				
                $tabelaItens = mysqli_query($mySQL, $queryItem);				
                if (mysqli_num_rows($tabelaItens) > 0) {
                    echo "<table class='tabela'>";					
					echo "<tr class='row'><th class='textoTabela cell'>item</th><th class='textoTabela cell'>id</th><th class='textoTabela cell'>subitem</th><th class='textoTabela cell'>tipo de valor</th><th class='textoTabela cell'>nome do campo no formulário</th><th class='textoTabela cell'>tipo do campo no formulário</th><th class='textoTabela cell'>tipo de unidade</th><th class='textoTabela cell'>ordem do campo no formulário</th><th class='textoTabela cell'>obrigatório</th><th class='textoTabela cell'>estado</th><th class='textoTabela cell'>ação</th></tr>";				                 
                    while ($rowItem = mysqli_fetch_assoc($tabelaItens)) { 					
                        $querySubitens = "SELECT * FROM subitem WHERE item_id = " . $rowItem["id"] . " ORDER BY name"; 						
                        $tabelaSubitens = mysqli_query($mySQL, $querySubitens);
                        if (mysqli_num_rows($tabelaSubitens) > 0) {														
                            $newSubitem = true;					
                            $numeroSubitens = mysqli_num_rows($tabelaSubitens);														
                            while ($rowSubitem = mysqli_fetch_assoc($tabelaSubitens)) {                              
                                if ($newSubitem) {
                                    echo "<tr class='row'><td class='textoTabela cell' rowspan='$numeroSubitens'>" . $rowItem["name"] . "</td>"; 
                                    $newSubitem = false;
                                } else {
                                    echo "<tr class='row'>";
                                }								
								$queryUnidade = "SELECT name FROM subitem_unit_type WHERE id = " . $rowSubitem["unit_type_id"] ."   ";		
								$tabelaUnidade = mysqli_query($mySQL, $queryUnidade);			
								$rowUnidade = mysqli_fetch_assoc($tabelaUnidade);								
                                echo "<td  class='textoTabela cell'>" . $rowSubitem["id"] . "</td><td class='textoTabela cell'>" . $rowSubitem["name"] . "<td  class='textoTabela cell'>" . $rowSubitem["value_type"] . "<td  class='textoTabela cell'>" . $rowSubitem["form_field_name"] . "<td  class='textoTabela cell'>" . $rowSubitem["form_field_type"] . "<td  class='textoTabela cell'>" . $rowUnidade["name"] . "<td  class='textoTabela cell'>" . $rowSubitem["form_field_order"] . "<td  class='textoTabela cell'>" . ($rowSubitem["mandatory"] == '1' ? 'sim' : 'não') . "</td><td class='textoTabela cell'>" . ($rowSubitem["state"] == 'active' ? 'ativo' : 'inativo') . "</td><td class='textoTabela cell'>[editar] [desativar]</td>"; 
                                echo "</tr>";								
                            }
                        }
                    }
                    echo "</table>";
                }
            } 						
			else {
                echo "Não há subitems especificados.";
            }
			
			echo "<div class='caixaSubTitulo'><h3><strong>Gestão de subitens - introdução</strong></h3></div>
            <div class='caixaFormulario'><body><form method='post' > <strong>Nome do subitem: </strong><br><input type='text' name='nome_subitem' ><br><br>";
			
			// ***
			
			
		}   
	}
} 

else {
    echo "Não tem autorização para aceder a esta página";
}
?>
