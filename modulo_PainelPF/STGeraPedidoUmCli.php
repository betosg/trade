<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

//$intCodPJ = request("var_chavereg");
$strTipo = request("var_tipo");

$intCodPJ = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
$strNomePJ = getsession(CFG_SYSTEM_NAME . "_pj_selec_nome");

/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
$strMsg = '';

if($strMsg != ""){  
	mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
	die();
}

$objConn = abreDBConn(CFG_DB);

?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--

function verifica() { }

function ok(){
	var var_msg = "";
	
	if (document.formeditor.var_cod_pj.value == "") var_msg += "\nEmpresa";
	if (document.formeditor.var_cod_produto.value == "") var_msg += "\nProduto";
	
	if (var_msg == "") {
		document.formeditor.var_retorno.value = "../modulo_PainelPJ/STindex.php";
		document.formeditor.submit();
	}	
	else {
		alert("Favor verificar campos:\n" + var_msg);
	}
}

function aplicar(){
	var var_msg = "";
	
	if (document.formeditor.var_cod_pj.value == "") var_msg += "\nEmpresa";
	if (document.formeditor.var_cod_produto.value == "") var_msg += "\nProduto";
	
	if (var_msg == "") {
		document.formeditor.var_retorno.value = "../modulo_PainelPJ/STGeraPedidoUmCli.php";
		document.formeditor.submit();
	}	
	else {
		alert("Favor verificar campos:\n" + var_msg);
	}
}

function cancelar(){
	//window.location = "../modulo_PainelPJ/";
	window.history.back(-2);
}

//-->
</script>
</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","PEDIDO ( Geração )",CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="705" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #777;">
	<form name="formeditor" action="STGeraPedidoUmCliexec.php" method="post"><br>
		<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ); ?>">
		<input type="hidden" name="var_tipo_produto" value="<?php echo($strTipo); ?>">
		<input type="hidden" name="var_retorno" value="">
		<tr>
			<td align="center" valign="top" style="padding:20px 80px 10px 80px;" width="1%">
			<table cellpadding="4" cellspacing="0" border="0" width="100%">
				<tr><td colspan="2" height="22" style="padding:10px"><b><?php echo(getTText("rotulo_dialog",C_NONE)); ?></b></td></tr>
				<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right" width="25%"><strong><?php echo(getTText("entidade",C_NONE)); ?>:</strong></label></td>
					<td><?php echo($intCodPJ." - ".$strNomePJ); ?></td>
				</tr>
				<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right" valign="top" nowrap style="padding-right:5px;">
					<label for="var_cod_produto"><strong>*<?php echo(getTText("produto",C_NONE)); ?>:</strong></label></td>
					<td nowrap align="left">
					<select name="var_cod_produto" id="var_cod_produto" style="width:320px" size="1" title="<?php echo(getTText("produto",C_NONE)); ?>" onChange="buscadadosproduto(this.value, 'complemento')">
					<option value=""></option>
					<?php
					try{
						$strSQL  = " SELECT cod_produto, rotulo, valor, descricao, max_qtde_parc ";
						$strSQL .= " FROM prd_produto ";
						$strSQL .= " WHERE dtt_inativo IS NULL ";
						if ($strTipo != "") {
							$strSQL .= " AND (tipo ILIKE '".$strTipo."') ";
						}
						else {
							$strSQL .= " AND ((tipo IS NULL) OR (tipo <> 'CARD' AND tipo <> 'homo')) ";
						}
						$strSQL .= " AND CURRENT_DATE BETWEEN dt_ini_val_produto AND dt_fim_val_produto ";
						$strSQL .= " ORDER BY rotulo ";
						
						$objResult = $objConn->query($strSQL);
					} catch(PDOException $e){
						mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
						die();
					}
					
					if($objResult->rowCount() > 0)
						foreach($objResult as $objRS){
							echo("<option value='".getValue($objRS,"cod_produto")."'>");
							echo(getValue($objRS,"rotulo")." - ".getValue($objRS,"descricao")."</option>");
						}
					$objResult->closeCursor();
					?>
					</select>
					<br><span id="complemento" class="comment_med"></span>
					</td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><label for="var_obs"><strong><?php echo(getTText("obs",C_NONE)); ?>:</strong></label></td>
					<td><textarea name="var_obs" id="var_obs" cols="60" rows="5"></textarea></td>
				</tr>
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right"><label for="var_qtde_parc"><strong><?php echo(getTText("qtde_parc",C_NONE)); ?>:</strong></label></td>
					<td>
						<input type="text" name="var_qtde_parc" id="var_qtde_parc" style="width:40px;" value="" title="<?php echo getTText("qtde_parc",C_NONE); ?>" onKeyPress="return validateNumKey(event);">
						<br><span class="comment_med"><?php echo getTText("msg_qtde_parc",C_NONE); ?></span>
					</td>
				</tr>
				<?php 
				//Controle para quando lermos qtde max parc do produto
				//Se campo "gerar_parc_dentro_ano_apenas" estiver marcado então calcula número 
				//de parcelas possível e restringe combo
				if (false) { 
				?>
				<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					<td align="right"><label for="var_qtde_parc"><strong></strong></label></td>
					<td>
					<?php
					$intQtdeMaxParc = 12;
					$intMesAtual = date("m");
					//num parc max possivel
					$intNumParcPossivel = (12 - $intMesAtual) + 1;
					?>
					</td>
				</tr>
				<?php } ?>
				<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
					<td align="right"><label for="var_arquivo"><strong><?php echo(getTText("arquivo",C_NONE)); ?>:</strong></label></td>
					<td>
						<input type="text" name="var_arquivo" id="var_arquivo" style="width:210px;" value="" readonly="true" title="<?php echo getTText("arquivo",C_NONE); ?>">
						<input type="button" name="btn_uploader" value="Upload" class="inputclean" onClick="callUploader('formeditor','var_arquivo','\\<?php echo getsession(CFG_SYSTEM_NAME."_dir_cliente") . "/upload/"; ?>','','');">&nbsp;<a href="javascript:;" onClick="document.getElementById('var_arquivo').value='';" style="cursor:pointer;"><img src="../img/icon_wrong.gif" border="0" align="absmiddle" alt="<?php echo getTText("limpar",C_NONE); ?>"></a>
						<br><span class="comment_med"><?php echo getTText("msg_upload_arquivo",C_NONE); ?></span>
					</td>
				</tr>
				<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><span class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></span></td></tr>
				<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
				<tr>
					<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
							<tr>
								<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
									<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
									<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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
<?php 
$objConn = NULL;
?>
