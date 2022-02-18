<?php
// INCLUDES
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "INS_FAST");

// ABERTURA DE CONEXÃO NO BANCO
$objConn = abreDBConn(CFG_DB);
?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE);?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript">

var strLocation = null;

function ok() {
    var strMSG = "";
    if((document.getElementById("var_cpf").value == "") || (document.getElementById("var_cnpj").value == "")){ strMSG += "\n\nVERIFICAÇÃO DOS DADOS:"; }
    if(document.getElementById("var_cnpj").value == "") { strMSG += "\nCNPJ da Empresa"; }
    if(document.getElementById("var_cpf").value == "") { strMSG += "\nCPF do Colaborador"; }
    if(document.getElementById("var_cpf").value != ""){
        if (!checkCPF(document.getElementById("var_cpf").value,false)) strMSG += "\nCPF Inválido";
    }
    if(document.getElementById("var_cnpj").value != ""){
        if (!checkCNPJ(document.getElementById("var_cnpj").value,false)) strMSG += "\nCNPJ Inválido";
    }
    
    if(strMSG != ""){
        alert("Verifique os Campos obrigatórios:" + strMSG);
    } else{
        strLocation = "<?php 
                       if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) 
                         echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); 
                       else 
                         echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); 
                     ?>";
        document.formverifica.submit();
    }
}

function cancelar() {          
    location.href = "<?php 
                       if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) 
                         echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); 
                       else 
                         echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); 
                     ?>";
}
</script>
</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center">
	<tr>
	<form name="formverifica" action="STverifydocsexec.php" method="post">
   	<td align="center" valign="top">
		<?php athBeginFloatingBox("630","none",getTText("colaborador",C_NONE)." - (".getTText("homologacao_rapida",C_NONE).")",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;" cellspacing="0" cellpadding="4">
	   		<tr><td height="22" style="padding:10px">&nbsp;</td></tr>
			<tr> 
		  		<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
						<tr>
							<td></td>
							<td align="left" valign="top" class="destaque_gde"><strong><?php echo(getTText("verificacao_de_dados",C_TOUPPER));?></strong></td>
						</tr>
						<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
						<tr bgcolor="#FAFAFA">
							<td width="25%" align="right" valign="top" nowrap><strong>*<?php echo(getTText("cnpj",C_NONE));?>:</strong></td>
							<td width="75%" nowrap align="left">
								<input name="var_cnpj" id="var_cnpj" value="" type="text" size="30" maxlength="14" title="<?php echo(getTText("cnpj",C_NONE));?>" onKeyPress="javascript:return validateNumKey(event);">
								&nbsp;<span class="comment_med"><?php echo(getTText("obs_somente_numeros",C_NONE));?></span>
							</td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td width="25%" align="right" valign="top" nowrap style="padding-left:35px;"><strong>*<?php echo(getTText("cpf",C_NONE));?>:</strong></td>
							<td width="75%" nowrap align="left">
								<input name="var_cpf" id="var_cpf" value="" type="text" size="30" maxlength="11" title="<?php echo(getTText("cpf",C_NONE));?>" onKeyPress="javascript:return validateNumKey(event);">
								&nbsp;<span class="comment_med"><?php echo(getTText("obs_somente_numeros",C_NONE));?></span>
							</td>
						</tr>
						<tr><td colspan="2" height="10">&nbsp;</td></tr>
						<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>		
						<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
					</table>
				</td>
			</tr>
			<tr>
				<td style="padding:10px 30px 20px 30px;">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                        <td width="5%" align="right" valign="top" nowrap style="padding-left:15px;padding-right:10px;"><img src="../img/mensagem_info.gif" alt="INFORMAÇÃO" title="INFORMAÇÃO" /></td>
                        <td align="left" width="40%"><?php echo(getTText("aviso_verificacao_dados",C_NONE));?></td>
                        <td align="right" width="10%" style="padding:10px 0px 10px 10px;"><button onClick="ok();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button></td>
                        <td align="right" width="10%" style="padding:10px 0px 10px 10px;"><button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button></td>
                    </tr>
					</table>
				</td>								
			</tr>					
		</table>
		<?php athEndFloatingBox();?>
	</td>
	</form>
	</tr>
</table>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>
<?php $objConn = NULL; ?>