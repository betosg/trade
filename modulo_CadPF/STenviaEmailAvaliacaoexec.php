<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athsendmail.php");
include_once("../_class/securimage/securimage.php");


$strUserName  = request("var_username");
$strCode      = request("var_code");

$strDBSelect = abreDBConn(CFG_DB);
$objConn = abreDBConn(CFG_DB);
$objConnUpd = abreDBConn(CFG_DB);

$arrAux = explode("_",$strDBSelect);
//SETA A CONSTANTE COM O DIRETÓRIO DO CLIENTE
setsession(CFG_SYSTEM_NAME . "_dir_cliente",$arrAux[1]);


$strMsg 	 = "";

/*** RECEBE PARAMETROS ***/
$strDestino = request("var_destino");
$intCodDado = request("var_chavereg");
$strMsgEmail = getVarEntidade($objConn, "msg_email_conselho");
$strEMAIL = getVarEntidade($objConn, "email_conselho");
$strCorpoEmail = str_replace("[cod_candidato]",  $intCodDado, $strMsgEmail);

ini_set("sendmail_from", CFG_EMAIL_SENDER);
ini_set("SMTP", CFG_SMTP_SERVER);
$strEmailsEnviados = "";
$strEmailsNAOEnviados = "";
$strNaoEncontrado = "";
                
				if(trim($strEMAIL) != "") {                  

					// ENVIA EMAIL PARA O EMAIL CADASTRADO NO BANCO
					$strCorpo =$strCorpoEmail;
					emailNotify($strCorpo, "ABFM - Avaliação de candidato a sócio", $strEMAIL, CFG_EMAIL_SENDER);		
				
				} else {
					$strNaoEncontrado = "E-mail destinatário do conselho não foi localizado e/ou não possui e-mail cadastrado. <br>Por favor entre em contato com nosso suporte.";
				}
?>
<html>
	<head>
	  <title><?php echo(CFG_SYSTEM_TITLE);?></title>
	  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	  <link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	</head>
	<body bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
	<table width="450" height="100%" border="0" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td align="center" valign="middle">
				<?php athBeginFloatingBox("415","","E-MAIL ENVIADO",CL_CORBAR_GLASS_1); ?>
					<table border="0" bgcolor="#FFFFFF" align="center" cellspacing="0" cellpadding="0" width="390" style="border:1px #A6A6A6 solid;">
						<tr><td colspan="2" height="15"></td></tr>
						<tr>
							<td width="230">
								<?php if($strEmailsEnviados != "") { ?>
									<table>								
										<tr><td> <div class="destaque_med_red" style="padding-left:10px;">E-mail enviado com sucesso!</div></td></tr>								
									</table>		
									<?php }  elseif($strNaoEncontrado != "") { ?>
									<table>								
										<tr><td> <div class="destaque_med_red" style="padding-left:10px;"><?php echo($strNaoEncontrado); ?></div></td></tr>								
									</table>							
								
							</td>
							<td align="right" width="210" ><img src="../img/LogoMarca_ABFM.gif" border="0">&nbsp;&nbsp;</td>
						</tr>
						<tr><td colspan="2" height="5"></td></tr>
						<tr><td colspan="2" height="1" bgcolor="#DBDBDB"></td></tr>
						<tr><td colspan="2" height="5"></td></tr>
						<tr><td colspan="2" align="right" style="padding-bottom:5px;"><button onClick="window.close();"><?php echo(getTText("fechar",C_NONE)); ?></button></td>
						</tr>
					</table>
				<?php athEndFloatingBox(); ?>
			</td>
		</tr>	
	</table>	
	</body>
</html>
<?php 
	$objConn   = NULL;
	$objResult = NULL;
	setsession(CFG_SYSTEM_NAME . "_dir_cliente",NULL);
?>