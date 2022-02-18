<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	error_reporting(E_ALL);
	
	// REQUESTS
	$strMsg  = request("var_msg");
	$strDB   = request("var_db");
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE);?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" rel="stylesheet" type="text/css">
	</head>
	<body bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	  	<!-- <tr><td valign="top" height="35%"><img src="../img/system_logo.gif" border="0" hspace="10" vspace="10"><td></tr> -->
	  		<tr>
				<td align="center" valign="top" style="padding-top:100px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:0px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
						<tr>
						 	<td align="center" valign="top"> 
								<table width="650" cellpadding="3" cellspacing="0">
									<tr>
										<td height="3" colspan="2">
											<!-- EXIBE MENSAGEM DE MANUTENÇÃO DO LOGIN -->
											<?php mensagem("ATENÇÃO!","Acesso ao sistema TRADEUNION","Visando melhorar a segurança e o acesso ao sistema TRADEUNION, estamos disponibilizando novo endereço personalizado para sua empresa. Para acessar a tela de login, você deve indicar o nome de sua empresa após o endereço do sistema, na barra de endereços.<br /><br />&bull;&nbsp;Exemplo: <strong>https://tradeunion.proevento.com.br</strong> vira <span style='color:green;font-weight:bold;'>https://tradeunion.proevento.com.br/NomeDaSuaEmpresa</span>","","standardinfo",1);?>
										</td>
									</tr>
								</table>
							</td>
						</tr>		
					</table>
				</td>
			</tr>
		</table>			
	</body>
</html>