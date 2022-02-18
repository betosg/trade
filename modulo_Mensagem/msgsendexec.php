<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strPara     = request("var_para");
$strAssunto  = request("var_assunto");
$strMensagem = request("var_mensagem");
$strLocation = request("default_location");

$objConn = abreDBConn(CFG_DB);

if($strPara != ""){
	
	$arrPara = explode(";",$strPara);
		
	foreach($arrPara as $strIdUsuario){
		try{
			$strSQL = "SELECT cod_msg_pasta FROM msg_pasta WHERE cod_user LIKE '" . $strIdUsuario . "' AND pasta = 'caixa_entrada'";
			$objRS = $objConn->query($strSQL)->fetch();
		
			$strSQL = " INSERT INTO msg_mensagem (cod_msg_pasta, assunto, mensagem, dtt_envio) VALUES (" . getValue($objRS,"cod_msg_pasta") . ",'" . $strAssunto . "','" . $strMensagem . "',current_timestamp)";
			$objConn->query($strSQL);
		
			$intCodMensagem = $objConn->lastInsertId("msg_mensagem_cod_mensagem_seq");
		
			$strSQL = " INSERT INTO msg_remetente (cod_mensagem, cod_user_remetente) VALUES (" . $intCodMensagem . ", '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "')";
			$objConn->query($strSQL);
		
			$strSQL = " INSERT INTO msg_destinatario (cod_mensagem, cod_user_destinatario) VALUES (" . $intCodMensagem . ", '" . $strIdUsuario . "')";
			$objConn->query($strSQL);
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
	}
	
	if($strLocation == "window.close()"){
		echo("<script>" . $strLocation . "</script>");
	}
	else{
		redirect($strLocation);
	}
	
}
else{
	mensagem("err_dados_titulo","err_dados_submit_desc","","","erro",1);
	die();
}
?>