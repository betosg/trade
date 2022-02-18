<?php
try{
	$strSQL = " SELECT COUNT(cod_mensagem) as num_msgs FROM msg_mensagem, msg_pasta 
				 WHERE lido = false 
				   AND msg_mensagem.cod_msg_pasta = msg_pasta.cod_msg_pasta 
				   AND cod_user = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "'";
	$objResultLido = $objConn->query($strSQL);
	$objRSLido = $objResultLido->fetch();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
	die();
}

if(getValue($objRSLido,"num_msgs") > 0){
	echo("
		<a href=\"../modulo_Mensagem/\" target=\"_parent\">
			<img src=\"../img/icon_nova_mensagem.gif\" border=\"0\"><br>" . getValue($objRSLido,"num_msgs") . getTText("nova_mensagem",C_UCWORDS) . "
		</a>"
		);
}
?>