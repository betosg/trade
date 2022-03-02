<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athsendmail.php");
include_once("../_class/securimage/securimage.php");

$objConn = abreDBConn(CFG_DB);

$strMsg 	 = "";

/*** RECEBE PARAMETROS ***/
$strDestino = request("var_destino");
$intCodDado = request("var_chavereg");
$strMsgEmail = getVarEntidade($objConn, "msg_email_conselho");

$strMsgEmail = str_replace("[cod_candidato]",  $intCodDado, $strMsgEmail);

?>
<html>
	<head>
	  <title><?php echo(CFG_SYSTEM_TITLE);?></title>
	  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	  <link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	  <script>
		function submeterForm() {
			document.formemail.var_chavereg.value = <?php echo($intCofPF);?>;
			if(document.formemail.var_chavereg.value != "") {				
				document.formemail.submit();
			} else {
				alert("Preencha a caixa de texto com os caracteres da imagem.");
			}
		}
	  </script>
	</head>
	<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
	<table width="450" height="100%" border="0" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td align="center" valign="middle">
				Mensagem a ser enviada:<br>
					<table border="0" bgcolor="#FFFFFF" align="center" cellspacing="0" cellpadding="0" width="100%" style="border:1px #A6A6A6 solid;">
						<tr><td colspan="2" height="15"></td></tr>
						<tr>
							<td width="230">
							  <form name="formemail" action="STenviaEmailAvaliacaoexec.php" method="post">
							    <input type="hidden" id="var_chavereg"     name="var_chavereg"     value="<?php echo($intCofPF);?>" 		 />
								<input type="hidden" id="var_destino"     name="var_destino"     value="<?php echo($strDestino);?>" 		 />
								<table border="0" cellpadding="0" cellspacing="0" width="365">								
									<tr><td style="padding-left:10px;" colspan="2"></td></tr>
									<tr><td height="20"></td></tr>								
									<tr><td style="padding-left:10px;"><?php echo($strMsgEmail); ?></td></tr>									
									<tr><td height="20"></td></tr>                                    
								</table>
							  </form>
							</td>
						</tr>
						<tr><td colspan="2" height="5"></td></tr>
						<tr><td colspan="2" height="1" bgcolor="#DBDBDB"></td></tr>
						<tr><td colspan="2" height="5"></td></tr>
						<tr>
							<td colspan="2" align="right" style="padding-bottom:5px;">
								<button onClick="submeterForm();"><?php echo("enviar"); ?></button>
								<button onClick="window.close();"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
							</td>
						</tr>
					</table>
				<?php athEndFloatingBox(); ?>
			</td>
		</tr>	
	</table>	
	</body>
</html>