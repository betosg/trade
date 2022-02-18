<?php
	function sendEmail($prFrom, $prTo, $prCc, $prBcc, $prSubject, $prMsg, $prHtmlFlag){
		// INCLUDE DA CLASSE
		include_once("../_class/mail/phpmailer.php");
		
		// INSTANCIA A CLASSE MAILER
		$mail = new PHPMailer();
		
		// ENVIAR VIA PHP MAIL
		// Se utilizar serviço SMTP (IsSMTP) teremos problemas de:
		// 1) envio sem autenticação 
		// 2) destinatários de mais um email (separados por , ou ;)
		$mail->IsMail();						
		
		// SERVIDOR DE SMTP, USE smtp.SeuDominio.com
		$mail->Host      = CFG_SMTP_SERVER;
		
		// Estamos sem autenticar porque o envio é mais rápido
		// by Clv/Aless/Luciano 14/09/2009
		// ATIVA O SMTP AUTENTICADO
		$mail->SMTPAuth  = false;
		
		$mail->Username  = CFG_EMAIL_SENDER;	//EMAIL PARA SMTP AUTENTICADO (pode ser qualquer conta de email do seu domínio)
		$mail->Password  = CFG_EMAIL_PASS;		//SENHA DO EMAIL PARA SMTP AUTENTICADO
		$mail->SMTP_PORT = CFG_SMTP_PORT; 		//PORTA do serviço
		
		$mail->From      = $prFrom;				//E-MAIL DO REMETENTE 
		$mail->FromName  = ucwords(CFG_SYSTEM_NAME); 	//NOME DO REMETENTE
		$mail->AddAddress($prTo,"");			//E-MAIL DO DESINATÁRIO, NOME DO DESINATÁRIO 
		$mail->AddBcc(CFG_EMAIL_AUDITORIA);		//E-MAIL DO COPIA OCULTA
		
		$mail->WordWrap  = 50;              	//ATIVAR QUEBRA DE LINHA
		$mail->IsHTML($prHtmlFlag);         	//ATIVA MENSAGEM NO FORMATO HTML
		
		$mail->Subject   = $prSubject;        	//ASSUNTO DA MENSAGEM
		$mail->Body      = $prMsg;              //MENSAGEM NO FORMATO HTML
		
		// DEBUG - MENSAGEM NO FORMATO TXT
		// $mail->AltBody = "Teste de envio via PHP";
		
		// ADDING OUTROS EMAILS - UTILIZE PARA DEFINIR OUTRO EMAIL DE RESPOSTA (opcional)
		// $mail->AddReplyTo("suporte@proevento.com.br"," Suporte Proevento ");
		
		// Tratamento contra ERRO de ENVIO
		if(!$mail->Send()){
			mensagem("err_mail_titulo","err_mail_desc1",$mail->ErrorInfo,"","erro",1);
			if($_SERVER["SERVER_NAME"] != "the_atena") { die(); }
		}
	}

	function emailNotify($prBody, $prSubject, $prEmails, $prFrom){
		// Tratamento para Descobrir o tipo de 
		// PATH, em função ONLINE / LOCAL DIFF
		if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br"))
			$strPATH = "http://" . $_SERVER["SERVER_NAME"] . "/_" . CFG_SYSTEM_NAME;
		else
			$strPATH = "http://" . $_SERVER["SERVER_NAME"] . "/" . CFG_SYSTEM_NAME . "/_" . CFG_SYSTEM_NAME;
		
		// PRÉ-CONFIGURA UM CABEÇALHO E RODAPÉ 
		// PARA QUE SEJA NECESSÁRIO SOMENTE INCLUIR O BODY
		$strBody ='
			<html>
			<head><link rel="stylesheet" type="text/css" href="'. $strPATH .'/_css/'. CFG_SYSTEM_NAME .'.css" /></head>
			<body>
				<table border="0px" cellpadding="0px" cellspacing="0px" width="100%" style="font:11px Tahoma;">
					<tr>
						<td width="50%" style="text-align:left;vertical-align:bottom;border-bottom:1px solid #CCC;">
						<a href="http://www.tradeunion.com.br" target="_blank">
							<img src="'. $strPATH .'/img/logomarca_mail.gif" border="0" width="120">
						</a>
						</td>
						<td width="50%" style="text-align:right;vertical-align:bottom;border-bottom:1px solid #CCC;">
							<img src="'. $strPATH .'/img/icon_novo_email.png" border="0" width="48">
						</td>
					</tr>
					<tr><td colspan="2" align="right" style="padding-top:5px;"><small><b>'. ucwords(getsession(CFG_SYSTEM_NAME."_dir_cliente")) .' | Data: '. dDate(CFG_LANG,now(),true) .'</b></small></td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td colspan="2" style="padding-left:10px;">'. $prBody .'</td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td colspan="2" style="border-bottom:1px solid #CCC;">&nbsp;</td></tr>
					<tr>
						<td align="right" colspan="2" style="font-family:Tahoma,Verdana;font-size:9;">
						<div style="padding-right:5px;color:#999999"><strong>'.strtoupper(CFG_SYSTEM_NAME).'</strong> | <a href="http://www.athenas.com.br" target="_blank" style="color:#339900;text-decoration:none;">Copyright GRUPO PROEVENTO - Athenas Software & Systems</a>.</div>
						</td>
					</tr>
				</table>
			</body>
			</html>';
		
		// Envia o email MONTADO
		sendEmail($prFrom,$prEmails,"","",$prSubject,$strBody, true);
	}
?>