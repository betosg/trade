<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athsendmail.php");
include_once("../_class/securimage/securimage.php");


$strUserName  = request("var_username");
$strCode      = request("var_code");
$strDBSelect =  request("var_dbselect");




$arrAux = explode("_",$strDBSelect);
//SETA A CONSTANTE COM O DIRETÓRIO DO CLIENTE
setsession(CFG_SYSTEM_NAME . "_dir_cliente",$arrAux[1]);

$objConn    = abreDBConn($strDBSelect);
$objConnUpd = abreDBConn($strDBSelect);
$objCaptcha = new securimage();

if (!$objCaptcha->check($strCode)){
	$strmsg = "Os caracteres informados não correspondem ao da imagem";
	mensagem("err_dados_titulo","err_dados_obj_desc",$strmsg,"javascript:history.back()","erro",1);
	die();
}

try{
	if (strpos($strDBSelect,"abfm")>0){
    //Busca pelo ID ou CPF  -0 conforme o que foi digitado (um OU outro serve)
		$strSQL  = " SELECT DISTINCT sys_usuario.cod_usuario, sys_usuario.id_usuario, case when cad_pf.email is null then sys_usuario.email  else cad_pf.email end as email, cad_pf.nome";
		$strSQL .= "   FROM sys_usuario INNER JOIN cad_pf ON codigo = cad_pf.cod_pf AND sys_usuario.tipo = 'cad_pf'";
		$strSQL .= "  WHERE sys_usuario.id_usuario = '" . $strUserName . "'";
	}else{
		$strSQL  = " SELECT DISTINCT sys_usuario.cod_usuario, sys_usuario.id_usuario, cad_pf.email ";
		$strSQL .= "   FROM sys_usuario ";
		$strSQL .= "  WHERE sys_usuario.id_usuario = '" . $strUserName . "'";
	}
	//echo $strSQL;
	//die();
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

$strID    = "";
$strCOD   = "";
$strEMAIL = "";
$strPASS  = "";
ini_set("sendmail_from", CFG_EMAIL_SENDER);
ini_set("SMTP", CFG_SMTP_SERVER);
$strEmailsEnviados = "";
$strEmailsNAOEnviados = "";
$strNaoEncontrado = "";
if ($objResult->rowCount() > 0){
		foreach($objResult as $objRS) {
			try {
				$strID    = getValue($objRS, "id_usuario");
				$strNome  = getValue($objRS, "nome");
				$strEMAIL = getValue($objRS, "email");
				//$strPASS  = $strCOD . "@" . substr($strID,0,6);
                
				if(trim($strEMAIL) != "") {
					//$objConnUpd->beginTransaction();
					//	
					//// gera uma senha nova para o usuario --- esta é uma senha teste... $strNovaSenha = ct09
					//$strSQL  = "UPDATE sys_usuario SET senha = '" . md5($strPASS) . "'";
					//$strSQL .= " WHERE cod_usuario = " . $strCOD;
					//$objConnUpd->query($strSQL);
					//	
					//$objConnUpd->commit();
					
                    $link = "https://tradeunion.proevento.com.br/_tradeunion/modulo_PainelPF/STtrocarsenha.php/?xy=".base64_encode($strID.":".$strDBSelect);
                    $strCorpoEmail = getVarEntidade($objConn, "email_troca_senha");
                    $strCorpoEmail = str_replace("[link]"      , $link    , $strCorpoEmail);
                    $strCorpoEmail = str_replace("[nome]"      , $strNome , $strCorpoEmail);
                    $strCorpoEmail = str_replace("[id_usuario]",$strID    , $strCorpoEmail);

					// ENVIA EMAIL PARA O EMAIL CADASTRADO NO BANCO
					$strCorpo =$strCorpoEmail;
					emailNotify($strCorpo, getTText("esqueceu_senha_tit",C_NONE), $strEMAIL, CFG_EMAIL_SENDER);
					
					$strEmailsEnviados .= $strEMAIL . "<br>";
				} else {
					$strEmailsNAOEnviados .= $strID . "<br>";
				}
			} catch(PDOException $e) {
				if(trim($strEMAIL) != "") $objConnUpd->rollBack();
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();
			}
		}
}
		$strNaoEncontrado = "Este usuário: <strong>".$strUserName."</strong> não foi localizado e/ou não possui e-mail cadastrado. <br>Por favor entre em contato com nossa administração.";

 $objResult->closeCursor();
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
				<?php athBeginFloatingBox("415","",getTText("esqueceu_senha_tit",C_NONE),CL_CORBAR_GLASS_1); ?>
					<table border="0" bgcolor="#FFFFFF" align="center" cellspacing="0" cellpadding="0" width="390" style="border:1px #A6A6A6 solid;">
						<tr><td colspan="2" height="15"></td></tr>
						<tr>
							<td width="230">
								<?php if($strEmailsEnviados != "") { ?>
									<table>
										<tr><td><div style="padding-left:10px;"><?php echo(getTText("esqueceu_senha_conteudo_ini",C_NONE)); ?></div></td></tr>
										<tr><td><div class="destaque_med" style="padding-left:25px;"><?php echo($strEmailsEnviados); ?></div></td></tr>
										<tr><td><div style="padding-left:10px;"><?php echo(getTText("esqueceu_senha_conteudo_fim",C_NONE)); ?></div></td></tr>
									</table>
									<?php }  elseif($strNaoEncontrado != "") { ?>
									<table>
										<!--tr><td><div style="padding-left:10px;"><?php echo(getTText("esqueceu_senha_conteudo_ini",C_NONE)); ?></div></td></tr-->
										<tr><td <div class="destaque_med_red" style="padding-left:10px;"><?php echo($strNaoEncontrado); ?></div></td></tr>
										<!--tr><td><div style="padding-left:10px;"><?php echo(getTText("esqueceu_senha_conteudo_ini",C_NONE)); ?></div></td></tr-->
										<!--tr><td><div class="destaque_med_red" style="padding-left:25px;"><?php echo($strNaoEncontrado); ?></div></td></tr-->
									</table>							
									<?php } //elseif($strEmailsNAOEnviados != "") { ?>
									<!--table>
										<tr><td><div style="padding-left:10px;">O(s) email(s) do(s) usu&aacute;rio(s) abaixo n&atilde;o n&atilde;o est&atilde;o cadastrados:</div></td></tr>
										<tr><td><div class="destaque_med_red" style="padding-left:25px;"><?php echo($strEmailsNAOEnviados); ?></div></td></tr>
									</table-->
									<?php //} ?>
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