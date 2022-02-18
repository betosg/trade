<?php	
// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athkernelfunc.php");
	
	
//REQUESTS
	$strMensagem 	= request("var_mensagem");
	$strAssinatura 	= request("var_assinatura");
	$strEmail		= request("var_email");
	$strAssunto		= request("var_assunto");
	
	if ($strMensagem == ""){
		$strMensagem = "AQUI VAI UMA MENSAGEM PADRAO SE ELA VIER VAZIA";
	}
	if ($strAssunto == ""){
		$strAssunto = "FELIZ ANIVERÁRIO";
	}
	if ($strAssinatura == ""){
		$strAssinatura = "ATENCOSAMENTE,<br>AQUI VAI UMA ASSINATURA PADRAO CASO NAO VENHA ASSINADO DO FORM ";
	}
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);

 
				$strBodyEmail  = '';
				$strBodyEmail .= "
					<table width='100%' bgcolor='#FFFFFF' border='0' cellspacing='0' cellpadding='0'>
					<tr>
						<td align='left' valign='top'> 
						<table width='100%' cellpadding='2' cellspacing='2'>
							<tr><td width='100%'></td></tr>
							<tr><td>Sr(a).<strong>".$strNome."</strong>,</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td><strong></strong></td></tr>
							<tr><td>
								".nl2br($strMensagem)."
							</td></tr>
							<tr><td>&nbsp;</td></tr>							
							<tr><td>&nbsp;</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td>".nl2br($strAssinatura)."</td></tr>
						</table>
						</td>
					</tr>
					</table>";
				
				// CONFIGURA LINHA DE DESTINATÁRIOS
				$strEmailLINE  = "";
				$strEmailLINE .= ($strEmail == "") ? "" : $strEmail.",";				
				$strEmailLINE  = trim($strEmailLINE,",");
				// $strEmailLINE .= ($strEmailPF == "") ? "" : $strEmailPF.",";
				// echo($strEmailLINE);
				
				// CONFIGURA TÍTULO DO EMAIL / SUBJECT
				$strSUBJECT    = ucwords(CFG_SYSTEM_NAME)." - ". $strAssunto;
					
				// Encaminha o email somente se estiver ONLINE
				//if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
					emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
				//}


?>
<script language="javascript">
	window.close();
</script>