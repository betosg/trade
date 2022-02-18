<?php

function sendEmail($prFrom, $prTo, $prCc, $prBcc, $prSubject, $prMsg, $prHtmlFlag){
	include_once("../_class/mail/phpmailer.php");
	
	//TEMPORARIO
	$prTo = "clvsutil@athenas.com.br";
	
	$mail = new PHPMailer();
	
	//ENVIAR VIA PHP MAIL
	//Se utilizar serviço SMTP (IsSMTP) teremos problemas de:
	//1) envio sem autenticação 
	//2) destinatários de mais um email (separados por , ou ;)
	$mail->IsMail();						
	
	$mail->Host = CFG_SMTP_SERVER; //SERVIDOR DE SMTP, USE smtp.SeuDominio.com
	
	//Estamos sem autenticar porque o envio é mais rápido
	// by Clv/Aless 14/09/2009
	$mail->SMTPAuth = false; //ATIVA O /SMTP AUTENTICADO
	
	$mail->Username = CFG_EMAIL_SENDER;		//EMAIL PARA SMTP AUTENTICADO (pode ser qualquer conta de email do seu domínio)
	$mail->Password = CFG_EMAIL_PASS;		//SENHA DO EMAIL PARA SMTP AUTENTICADO
	$mail->SMTP_PORT = CFG_SMPT_PORT; 		//PORTA do serviço
	
	$mail->From = $prFrom;					//E-MAIL DO REMETENTE 
	$mail->FromName = CFG_SYSTEM_NAME; 		//NOME DO REMETENTE
	$mail->AddAddress($prTo,"");			//E-MAIL DO DESINATÁRIO, NOME DO DESINATÁRIO 
	$mail->AddBcc(CFG_EMAIL_AUDITORIA);		//E-MAIL DO COPIA OCULTA
	
	$mail->WordWrap = 50;                         //ATIVAR QUEBRA DE LINHA
	$mail->IsHTML($prHtmlFlag);                   //ATIVA MENSAGEM NO FORMATO HTML
	
	$mail->Subject = $prSubject;                  //ASSUNTO DA MENSAGEM
	$mail->Body = $prMsg;                         //MENSAGEM NO FORMATO HTML
	//$mail->AltBody = "Teste de envio via PHP";  //MENSAGEM NO FORMATO TXT

	//$mail->AddReplyTo("suporte@proevento.com.br"," Suporte Proevento "); //UTILIZE PARA DEFINIR OUTRO EMAIL DE RESPOSTA (opcional)
	
	if(!$mail->Send()){
		mensagem("err_mail_titulo","err_mail_desc1",$mail->ErrorInfo,"","erro",1);
		if($_SERVER["SERVER_NAME"] != "the_atena") { die(); }
	}
}

function emailNotify($prBody, $prSubject, $prEmails, $prFrom){
	
	if ($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") 
		$strPATH = "http://" . $_SERVER["SERVER_NAME"] . "/_tradeunion";
	else
		$strPATH = "http://" . $_SERVER["SERVER_NAME"] . "/tradeunion/_tradeunion";
	
	$strBody =	"
	<html>
	<body>
		<table border=\"0px\" cellpadding=\"0px\" cellspacing=\"0px\" width=\"100%\" style=\"font:11px Tahoma;\">
			<tr><td colspan=\"2\"><img src=\"" . $strPATH . "/img/logomarca_mail.gif\" border=\"0\"></td></tr>
			<tr><td colspan=\"2\" height=\"10px\"></td></tr>
			<tr><td colspan=\"2\"><hr></td></tr>
			<tr><td colspan=\"2\" align=\"right\"><small>
			<b>Data:&nbsp;" . dDate(CFG_LANG,now(),true) . "</b></small></td></tr>
			<tr><td height=\"25px\" colspan=\"2\" style='font-weight:bold; padding-left:10px;' align='left'>" . CFG_CLIENTE . "</td></tr>
			<tr><td height=\"15px\" colspan=\"2\"></td></tr>
			<tr><td colspan=\"2\" style=\"padding-left:10px;\">" . $prBody . "</td></tr>
			<tr><td height=\"3px\"></td></tr>
			<tr><td colspan=\"2\"><hr></td></tr>
			<tr>
				<td align=\"right\" colspan=\"2\" style=\"font-family:Tahoma, Verdana; font-size:9;\">
				<div style=\"padding-right:5px; color:#999999\"><a href='http://www.athenas.com.br' target='_blank' style='color:#006699; font:none 11px; text-decoration:none;'>Copyright GRUPO PROEVENTO - Athenas Software & Systems</a></div>
				</td>
			</tr>
		</table>
	</body>
	</html>";
	
	sendEmail($prFrom, $prEmails, "", "", CFG_CLIENTE . ": " . $prSubject, $strBody, true);
}
?>