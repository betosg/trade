<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$intCodigo = request("var_chavereg");

if($intCodigo != "" && is_numeric($intCodigo)){
	$objConn = abreDBConn(CFG_DB);

	try{
		$strSQL = " 
					SELECT msg_mensagem.cod_mensagem, id_usuario AS remetente, assunto, mensagem, dtt_envio
					  FROM 	
						  msg_mensagem
						, sys_usuario
						, msg_destinatario
						, msg_remetente 
					WHERE 
					 msg_mensagem.cod_mensagem = msg_destinatario.cod_mensagem
					 AND msg_mensagem.cod_mensagem = msg_remetente.cod_mensagem
					 AND msg_remetente.cod_user_remetente = sys_usuario.id_usuario
					 AND msg_mensagem.cod_mensagem = " . $intCodigo;
		$objRS = $objConn->query($strSQL)->fetch();
		
		$objConn->query(" UPDATE msg_mensagem SET lido = true WHERE cod_mensagem = " . $intCodigo);
		
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	try{
		$strSQL = " SELECT id_usuario 
					 FROM msg_destinatario, sys_usuario
					WHERE msg_destinatario.cod_user_destinatario = sys_usuario.id_usuario
					  AND cod_mensagem = " . $intCodigo;
		$objResultDest = $objConn->query($strSQL);
		
		$strDestinatarios = "";
		foreach($objResultDest as $objRSDestinatarios){
			$strDestinatarios .= ($strDestinatarios == "") ? $objRSDestinatarios["id_usuario"] : "; " . $objRSDestinatarios["id_usuario"];
		}
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body style="margin:5px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr bgcolor="#DDDDDD"><td height="5"></td></tr>
	<tr bgcolor="#F0F0F0">
		<td>
			<table border="0" cellpadding="0" cellspacing="4" width="100%" class="padrao_gde">
				<tr>
					<td align="left" class="padrao_gde" style="padding-left:10px;"><big><?php echo(ucwords($objRS["remetente"])); ?></big></td>
					<td align="right" style="padding-right:10px;"><?php echo(dDate(CFG_LANG,$objRS["dtt_envio"],true)); ?></td>
				</tr>
				<tr>
					<td colspan="2" class="padrao_gde" style="padding-left:10px;"><?php echo(getTText("para",C_UCWORDS) . ":&nbsp;" . $strDestinatarios); ?> </td>
				</tr>
				<tr><td height="15"></td></tr>
				<tr>
					<td colspan="2" class="padrao_gde" style="padding-left:10px;"><big><b><?php echo($objRS["assunto"]); ?></b></big></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor="#DDDDDD"><td height="1"></td></tr>
	<tr>
		<td style="padding:20px;" class="padrao_gde"><?php echo($objRS["mensagem"]); ?></td>
	</tr>
</table>
</body>
</html>
<?php
}
?>