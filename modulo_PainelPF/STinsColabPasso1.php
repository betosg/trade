<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// TRATAMENTO PARA CÓDIGO DA PJ
	$intCodPJ = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
	if ($intCodPJ == "") $intCodPJ = request("var_cod_pj");
	
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "VIE");
	
	// Só abre o conteúdo da janela caso o codigo da PJ 'SELECIONADA' esteja na sessão
	if(getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo") != ""){
		$objConn = abreDBConn(CFG_DB);
?> 
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" language="javascript">
		/* Funções JAVASCRIPT */
		function ok() {
			if (document.formverifica.var_cpf.value ==""){
				alert("Favor preencher o CPF.");
				document.formverifica.var_cpf.focus();
				return false;
			}
			if(checkCPF(document.formverifica.var_cpf.value, true)){
				document.formverifica.submit();
			}
		}
	
		function cancelar() {
			location.href='../modulo_PainelPJ/STindex.php';
		}
	</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center">
	<form name="formverifica" action="STinsColabPasso1exec.php" method="post">
	<input type="hidden" name="var_cod_pj" id="var_cod_pj" value="<?php echo($intCodPJ); ?>">
	<tr>
   		<td align="center" valign="top">
		<?php athBeginFloatingBox("630","none","CPF (Verificação)",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;" cellspacing="0" cellpadding="4">
	   		<tr><td height="22" style="padding:10px"><b>Preencha os campos abaixo</b></td></tr>
			<tr> 
		  		<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
						<tr>
							<td></td>
							<td align="left" valign="top" class="destaque_gde"><strong>DADOS</strong></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
						<tr bgcolor="#FAFAFA">
							<td width="1%" align="right" valign="top" nowrap style="padding-left:35px;"><strong>*CPF:</strong></td>
							<td nowrap align="left" width="99%">
								<input name="var_cpf" id="var_cpf" value="" type="text" size="30" maxlength="11" title="CPF" tabindex="1" onKeyPress="javascript:return validateNumKey(event);"><span class="comment_med">&nbsp;(somente números, sem pontos e/ou traços)</span>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td width="1%" align="right" valign="top" nowrap style="padding-left:35px;"><strong>*Tipo de inserção:</strong></td>
							<td nowrap align="left" width="99%">
								<input type="radio" name="var_tp_ins" id="var_tp_ins" value="NEW_COLABORADOR" class="inputclean" checked="checked">
								<span>Novo Colaborador (Solicitar Credencial)</span><br />
								<input type="radio" name="var_tp_ins" id="var_tp_ins" value="NEW_HOMOLOGACAO" class="inputclean">
								<span>Novo Colaborador (Solicitar Homologação)</span>
							</td>
						</tr>
						<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>		
						<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
					</table>
				</td>
			</tr>
			<tr>
				<td style="padding: 10px 30px 20px 30px;">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td width="5%" align="right" valign="top" nowrap style="padding-left:15px; padding-right: 10px;">
								<img src="../img/mensagem_info.gif" alt="INFORMAÇÃO" title="INFORMAÇÃO" />
							</td>
							<td align="left" width="40%" width="20%">
								<?php echo(getTText("aviso_verificacao_cpf_txt",C_NONE)); ?>
							</td>
							<td align="right" width="10%"  style="padding:10px 0px 10px 10px;">
								<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
							</td>
							<td align="right" width="10%" style="padding:10px 0px 10px 10px;">
								<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
							</td>
						</tr>
					</table>
				</td>								
			</tr>					
		</table>
		<?php athEndFloatingBox(); ?>
		</td>
	</tr>
	</form>
</table>
</body>
</html>
<?php 
	$objConn = NULL; }
	else { echo(mensagem("err_selec_empresa_titulo","err_selec_empresa_desc","","","erro",1)); }
?>
