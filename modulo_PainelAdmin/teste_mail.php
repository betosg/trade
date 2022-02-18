<?php	
// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athkernelfunc.php");
	
	
//REQUESTS
	//$strMensagem 	= request("var_mensagem");
	//$strAssinatura 	= request("var_assinatura");
	//$strEmail		= request("var_email");
	//$strAssunto		= request("var_assunto");
    $strEmail = "gabriel.schunck@gmail.com";
    
	if ($strMensagem == ""){
		$strMensagem = "Prezados Senhores, <br>
        <br>
        <br>
        Seu Cadastro foi concluído com sucesso! <br>
        <br>
        Colocamo-nos a disposição para maiores informações.  <br>
        <br>
        <br>
        Atenciosamente,  <br>
        <br>
        Departamento Financeiro <br>
        SINDIPROM - Sindicato das Empresas de Promoção, Organização e Montagem de Feiras, Congressos e Eventos do Estado de São Paulo  <br>
        <br>
        Telefax: (55 11) 3120.7099  <br>
        E-mail: sindiprom@sindiprom.org.br <br>
        Rua Frei Caneca, 91, 11º andar - Cerqueira Cesar - CEP: 01307-001 São Paulo - SP  <br>
        <br>
        ==== ESTA É UMA MENSAGEM AUTOMÁTICA POR FAVOR NÃO RESPONDA ==== <br>";
	}
	if ($strAssunto == ""){
		$strAssunto = "Conclusão de Cadastro Efetuada com Sucesso";
	}
	//if ($strAssinatura == ""){
	//	$strAssinatura = "ATENCOSAMENTE,<br>AQUI VAI UMA ASSINATURA PADRAO CASO NAO VENHA ASSINADO DO FORM ";
	//}
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);

 
				$strBodyEmail  = '';
				$strBodyEmail .= "
					<table width='100%' bgcolor='#FFFFFF' border='0' cellspacing='0' cellpadding='0'>
					<tr>
						<td align='left' valign='top'> 
						<table width='100%' cellpadding='2' cellspacing='2'>
							<tr><td width='100%'></td></tr>
							<!--tr><td>Sr(a).<strong>".$strNome."</strong>,</td></tr//-->
							<tr><td>&nbsp;</td></tr>
							<tr><td><strong></strong></td></tr>
							<tr><td>
								".nl2br($strMensagem)."
							</td></tr>
							<!--tr><td>&nbsp;</td></tr>							
							<tr><td>&nbsp;</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td>".nl2br($strAssinatura)."</td></tr//-->
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