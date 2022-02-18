<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athsendmail.php");
include_once("../_class/securimage/securimage.php");

$strDBSelect  = request("var_dbselect");
$strUserName  = request("var_username");

if ( 
//($strUserName == "") || 
($strDBSelect == "") ){
	$strmsg = "Selecione o banco da dados e preencha o campo usuario com o seu ID (user) ou CPF cadastrado.";
	mensagem("err_dados_titulo","err_dados_obj_desc",$strmsg,"javascript:history.back()","erro",1);
	die();
}

?>
<html>
	<head>
	  <title><?php echo(CFG_SYSTEM_TITLE);?></title>
	  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	  <link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	  <script>
		function submeterForm() {
			if(document.formsenha.var_code.value != "") {
				document.formsenha.submit();
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
				<?php athBeginFloatingBox("415","",getTText("esqueceu_senha_tit",C_NONE),CL_CORBAR_GLASS_1); ?>
					<table border="0" bgcolor="#FFFFFF" align="center" cellspacing="0" cellpadding="0" width="100%" style="border:1px #A6A6A6 solid;">
						<tr><td colspan="2" height="15"></td></tr>
						<tr>
							<td width="230">
							  <form name="formsenha" action="esquecisenhaexecABFM.php" method="post">
								<input type="hidden" name="var_dbselect" value="<?php echo($strDBSelect)?>">
								<input type="hidden" name="var_username" value="<?php echo($strUserName)?>">
								<table border="0" cellpadding="0" cellspacing="0" width="365">
									
									<tr>
										<td style="padding-left:10px;">Informe o user/CPF:							
                                    
											<input type="text" name="var_username">
										</td>
									</tr>
									<tr><td height="20"></td></tr>
                                    <tr><td style="padding-left:10px;" colspan="2"><?php echo(getTText("esqueceu_senha_form_info",C_NONE)); ?></td></tr>
									<tr><td height="20"></td></tr>
									<tr>
										<td style="padding-left:10px;">
											<img src="../_class/securimage/securimage_show.php?sid=<?php echo md5(uniqid(time())); ?>">&nbsp;&nbsp;
                                            <input type="text" name="var_code">
										</td>
									</tr>
								</table>
							  </form>
							</td>
						</tr>
						<tr><td colspan="2" height="5"></td></tr>
						<tr><td colspan="2" height="1" bgcolor="#DBDBDB"></td></tr>
						<tr><td colspan="2" height="5"></td></tr>
						<tr>
							<td colspan="2" align="right" style="padding-bottom:5px;">
								<button onClick="submeterForm();"><?php echo(getTText("ok",C_NONE)); ?></button>
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