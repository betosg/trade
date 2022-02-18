<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	</head>
	<body bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	  	<!-- <tr><td valign="top" height="35%"><img src="../img/system_logo.gif" border="0" hspace="10" vspace="10"><td></tr> -->
	  		<tr>
				<td align="center" valign="middle">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:0px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
						<tr>
						 	<td align="center" valign="top"> 
								<table width="500" cellpadding="3" cellspacing="0">
									<tr>
										<td height="3" colspan="2">
											<?php mensagem("Aguarde","Solicitação de Cadastro Pendente","Obrigado por escolher a Sindieventos!<br><br>Gostaríamos de expressar nosso agradecimento pelo interesse da filiação de sua empresa. Seu cadastro foi pré-aprovado e está em processo de confirmação por um de nossos colaboradores.  Aguarde a liberação através do contato pelo e-mail cadastrado por sua empresa em nosso sistema.<br><br>Clique <div style='cursor: pointer; display: inline;'><a href='../modulo_Principal/logout.php'><u>AQUI</u></a></div>&nbsp;para sair.","","standardinfo",1) ?>
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