<?php

function sendEmail($prFrom, $prTo, $prCc, $prBcc, $prSubject, $prMsg, $prHtmlFlag, $prReply="", $prReplyName=""){
	include_once("../_class/mail/PHPMailerAutoload.php");
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);
	//echo "<br>".$prTo."<br>";
	//echo $prMsg;
	//die();
	$mail = new PHPMailer(true);
	$mail->SetLanguage("en", '_class/mail/language/');
	//ENVIAR VIA PHP MAIL
	//Se utilizar serviço SMTP (IsSMTP) teremos problemas de:
	//1) envio sem autenticação 
	//2) destinatários de mais um email (separados por , ou ;)
	//$mail->IsMail();						
	$mail->IsSMTP(); // Define que a mensagem será SMTP
	//$mail->Mailer = "smtp";
try {	
	//$mail->Host 	 = CFG_SMTP_SERVER; //SERVIDOR DE SMTP, USE smtp.SeuDominio.com
	$mail->Host	= 'smtp.gmail.com';
	//$mail->Debugoutput = 'html';
	$mail->SMTPAuth  = true; //ATIVA O /SMTP AUTENTICADO
	$mail->Port = 587; 
	$mail->SMTPSecure = "tls";
	$mail->SMTP_PORT = 587;
	
	$mail->Username = 'tradeunion@proevento.com.br';
	$mail->Password = 'am>2=U4e';
	//$mail->SMTPDebug  = 1;  
	//Estamos sem autenticar porque o envio é mais rápido
	// by Clv/Aless/Luciano 14/09/2009
	
	
	//$mail->Username  = CFG_EMAIL_SENDER;		  //EMAIL PARA SMTP AUTENTICADO (pode ser qualquer conta de email do seu domínio)
	//$mail->Password  = CFG_EMAIL_PASS;			  //SENHA DO EMAIL PARA SMTP AUTENTICADO
	
	//$mail->Username ="tradeunion@proevento.com.br";
	//$mail->Password = "ath319bbsi503";
	//$mail->SMTP_PORT = CFG_SMTP_PORT; 			  //PORTA do serviço
	
	$mail->From = "tradeunion@proevento.com.br";	
	//$mail->From 	= "tradeunion@proevento.com.br";					  //E-MAIL DO REMETENTE 
	//$mail->FromName = "Tradeunion - ".CFG_SYSTEM_NAME; 			  //NOME DO REMETENTE
	$mail->FromName = strtoupper( getsession(CFG_SYSTEM_NAME . "_dir_cliente"));
	//$mail->FromName = "Trad";
	$mail->AddAddress($prTo,"");				  //E-MAIL DO DESINATÁRIO, NOME DO DESINATÁRIO 
	//$mail->AddAddress("gabriel@proevento.com.br","");
	$mail->AddBcc(CFG_EMAIL_AUDITORIA);			  //E-MAIL DO COPIA OCULTA
	
	if ($prReply != ""){	
		$mail->AddReplyTo($prReply, $prReplyName);
	}
	$mail->WordWrap = 50;                         //ATIVAR QUEBRA DE LINHA
	//$mail->IsHTML($prHtmlFlag);                   //ATIVA MENSAGEM NO FORMATO HTML
	$mail->IsHTML(true);
	$mail->Subject = $prSubject;                  //ASSUNTO DA MENSAGEM	
	$mail->Body    = $prMsg;                      //MENSAGEM NO FORMATO HTML
	
	
	//$mail->Send();


	if(!$mail->Send())
	{
	  echo "Mailer Error: " . $mail->ErrorInfo ;
	  print_r($mail);
	}
	else
	{
	  //echo "Email Enviado";
	}



}catch(phpmailerException $e) {
      echo $e->errorMessage(); //Mensagem de erro costumizada do PHPMailer
}

}

function emailNotify($prBody, $prSubject, $prEmails, $prFrom, $prDebug="", $prReply="", $prReplyName=""){
	
	if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br"))
		$strPATH = "http://" . $_SERVER["SERVER_NAME"] . "/_" . CFG_SYSTEM_NAME;
	else
		$strPATH = "http://" . $_SERVER["SERVER_NAME"] . "/" . CFG_SYSTEM_NAME . "/_" . CFG_SYSTEM_NAME;
	
	$strBody =	"
	<html>
	<body>
		<table border=\"0px\" cellpadding=\"0px\" cellspacing=\"0px\" width=\"100%\" style=\"font:11px Tahoma;\">
			<!--tr><td colspan=\"2\"><img src=\"" . $strPATH . "/img/logomarca_mail.gif\" border=\"0\"></td></tr-->
			<tr><td colspan=\"2\" height=\"10px\"></td></tr>
			<tr><td colspan=\"2\"><hr></td></tr>
			<tr><td colspan=\"2\" align=\"right\"><small>
			<b>Data:&nbsp;" . dDate(CFG_LANG,now(),true) . "</b></small></td></tr>
			<!--tr><td height=\"25px\" colspan=\"2\" style='font-weight:bold; padding-left:10px;' align='left'>" . getsession(CFG_SYSTEM_NAME . "_dir_cliente") . "</td></tr-->
			<tr><td height=\"15px\" colspan=\"2\"></td></tr>
			<tr><td colspan=\"2\" style=\"padding-left:10px;\">" . $prBody . "</td></tr>
			<tr><td height=\"3px\"></td></tr>
			<tr><td colspan=\"2\"><hr></td></tr>
			<tr>
				<td align=\"right\" colspan=\"2\" style=\"font-family:Tahoma, Verdana; font-size:9;\">
				<div style=\"padding-right:5px; color:#999999\"><a href='http://www.athenas.com.br' target='_blank' style='color:#006699; font:none 11px; text-decoration:none;'>Copyright GRUPO PROEVENTO</a></div>
				</td>
			</tr>
		</table>
	</body>
	</html>";

	if($prDebug != true) {  
		sendEmail($prFrom, $prEmails, ""   , ""    , strtoupper(getsession(CFG_SYSTEM_NAME . "_dir_cliente") . " " . $prSubject), $strBody, true, $prReply, $prReplyName);
	  //sendEmail($prFrom, $prTo    , $prCc, $prBcc, $prSubject                                                      , $prMsg  , $prHtmlFlag)
	} else {
		echo("<script type=\"text/javascript\" language=\"javascript\">
				var objWin = window.open('','" . CFG_SYSTEM_NAME . "_EMAIL_POPUP','width=800,height=600,scrollbars=yes');
				objWin.document.write('" . str_replace("'","\'",str_replace("\r\n","",$strBody)) . "');
				objWin.document.close();
			  </script>");
	}
}
?>
