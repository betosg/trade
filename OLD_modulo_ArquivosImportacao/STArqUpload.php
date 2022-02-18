<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");       // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

initModuloParams(basename(getcwd()));

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "VIE");
$_SESSION['ValidaErro'] = "";
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function abreJanelaPageLocal(pr_link, pr_extra){
	var auxStrToChange, rExp, auxNewExtra, auxNewValue;
	if (pr_extra != ""){
		rExp = /:/gi;
		auxNewExtra = pr_extra
		if(pr_extra.search(rExp) != -1){
		    auxStrToChange = pr_extra.split(":");
		    auxStrToChange = auxStrToChange[1];
		    rExp = eval("/:" + auxStrToChange + ":/gi");
		    auxNewValue = eval("document.formeditor." + auxStrToChange + ".value");
		    auxNewExtra = pr_extra.replace(rExp, auxNewValue);
		}
		pr_link = pr_link + auxNewExtra;
	}
	
	AbreJanelaPAGE(pr_link, "800", "600");
}

function callUploader(prFormName, prFieldName, prDir){
	strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir;
	AbreJanelaPAGE(strLink, "570", "270");
}

function setFormField(formname, fieldname, valor){
	if ((formname != "") && (fieldname != "") && (valor != "")){
    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  	}
}

function aplicar(){
	campo = document.formarquivo.uploadArquivo.value;
	if(campo==''){
		alert('Por favor especifique o caminho do arquivo');
	}else{
		document.formarquivo.submit();
	}
}
</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","IMPORTAÇÃO DE ARQUIVO (Upload)",CL_CORBAR_GLASS_1); ?>
      <table id="dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
        <form name="formarquivo" action="STimportacaoDatabase.php" method="post">
		<tr><td height="22" style="padding:10px"><strong>Preencha os campos abaixo</strong></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="550" border="0" cellspacing="0" cellpadding="4">
					<tr bgcolor="#FFFFFF">
						<td width="1%" align="right" valign="top"><strong></strong>&nbsp;</td>
						<td><?php echo(getTText("msg_arquivo_validacao",C_NONE)); ?></td>
					</tr>
					<tr bgcolor="#FAFAFA">
						<td width="1%" align="right" valign="top"><strong><?php echo(getTText("arquivo",C_NONE)); ?>:</strong>&nbsp;</td>
						<td><input type="text" size="50" name="uploadArquivo" ><input type="button" class="inputclean	" onClick="callUploader('formarquivo','uploadArquivo','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/arqbanco/');" value="Upload"></td>
					</tr>
					<tr align="left">
						<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px;"><?php echo(getTText("campos_obrig",C_NONE)); ?></td>
					</tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
					<tr>
						<td colspan="2">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
										<button onClick="aplicar(); return false;"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
									</td>
								</tr>
							</table>
						</td>
					</tr> 
				</table>
			</td>
		</tr>
        </form>
      </table>
      <?php athEndFloatingBox(); ?>
   </td>
  </tr>
</table>
</body>
</html>
