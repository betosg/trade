<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);
	
	$strPopulate  = "yes";
	if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session
	
	/***           DEFINIÇÃO DE PARÂMETROS            ***/
	/****************************************************/
	$intCodDado = request("var_codnfpai");   //CODIGO PAI
	
	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	
	if(!verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS","not die")){
		mensagem("err_acesso_titulo","err_acesso_desc","Ação a ser realizada:&nbsp;INS","","erro",1,"not html");
		$strScript  = "";
		$strScript .= "<script type=\"text/javascript\">";
		$strScript .= "/* usado para redimensionar o IFRAME */";
		$strScript .= "resizeIframeParent('" . CFG_SYSTEM_NAME . "_detailiframe_" . $var_codresize ."',05)";
		$strScript .=" </script>";
		echo($strScript);die();
	}		
	
	/***         FUNÇÕES AUXILIARES - OPCIONAL        ***/
	/****************************************************/
	$strColor = CL_CORLINHA_2; 				// inicializa variavel para pintar linha
	function getLineColor(&$prColor){ 	// função para cores de linhas
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE);?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<style type="text/css">
	/* suas adaptações css aqui */
</style>
<script language="javascript" type="text/javascript">
	var strLocation = null;						
	
	function calcula(){
		var quant 	 = MoedaToFloat(document.formstatic.DBVAR_MOEDA_QUANTIDADE.value);
		var precouni = MoedaToFloat(document.formstatic.DBVAR_MOEDA_VLR_UNITARIO.value);

		if(quant == ""){
			quant = 0;
		}
		if (precouni ==""){
			precouni = 0;	
		}
		result = quant * precouni;		
		formstatic.DBVAR_MOEDA_VLR_TOTAL.value = FloatToMoeda( RoundNumber(result, 2) );
	}			
	
	function ok() {			
		calcula();
		strLocation = "../modulo_FinNfEntradaItem/STitemNF.php?var_codnfpai=<?php echo $intCodDado; ?>";
		submeterForm();
	}

	function cancelar() {
		document.location.href = "../modulo_FinNfEntradaItem/STitemNF.php?var_codnfpai=<?php echo $intCodDado; ?>";
	}

	function aplicar() {
		calcula();
		strLocation = "../modulo_FinNfEntradaItem/STinsItemNF.php?var_codnfpai=<?php echo $intCodDado; ?>";
		submeterForm();
	}

	function submeterForm() {
		document.formstatic.DEFAULT_LOCATION.value = strLocation;				
		document.formstatic.submit();
	}
</script>
</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 0px 0px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
	<td align="center">
	<?php athBeginFloatingBox("520","none",getTText("contatos_inserir_title",C_NONE),CL_CORBAR_GLASS_1); ?>
	<form name="formstatic" action="../_database/athinserttodb.php" method="POST" />
	<input type="hidden" name="DEFAULT_TABLE"              value="fin_nf_entrada_item" />
	<input type="hidden" name="DEFAULT_DB"                 value="<?php echo(CFG_DB);?>" />
	<input type="hidden" name="FIELD_PREFIX"               value="DBVAR_" />
	<input type="hidden" name="RECORD_KEY_NAME"            value="cod_nf_entrada_item" />
	<input type="hidden" name="DEFAULT_LOCATION"           value=""/>
	<input type="hidden" name="DBVAR_INT_COD_NF_ENTRADA"   value="<?php echo $intCodDado; ?>">
	<input type="hidden" name="DBVAR_STR_SYS_USR_INS"      value="<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>" />
	<input type="hidden" name="DBVAR_AUTODATE_SYS_DTT_INS" value="false" />	
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="500" bgcolor="#FFFFFF" style="background-color:#FFFFFF; border:1px solid #CCCCCC;">
		<tr>
			<td align="left" valign="top" style="padding:15px 0px 0px 15px;">
				<strong><?php echo(getTText("preencha_campos",C_NONE));?></strong>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" style="padding:10px 50px 0px 50px;">
				<table cellspacing="2" cellpadding="3" border="0" width="100%">
					<tr bgcolor="#FFFFFF">
						<td width="13%" align="right">&nbsp;</td>
						<td align="left" class="destaque_gde" colspan="3">
							<strong><?php echo(getTText("dados_contato_ins",C_TOUPPER));?></strong>						</td>
					</tr>
					<tr><td colspan="4" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("codnf",C_UCWORDS));?>:</strong></td>
						<td align="left" colspan="3">
							<?php echo $intCodDado;?>
							<span class="comment_peq"><?php echo(getTText("obs_ins_nf_item",C_NONE));?></span>						</td>
					</tr>    				
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("desc_prod",C_UCWORDS));?>:</strong></td>
						<td align="left" colspan="3">
							<input class="campos" name="DBVAR_STR_DESCR_PRODUTO" id="DBVAR_STR_descr_produto" style="width:100%" type="text" value="" maxlength="255"   >	
						</td>
					</tr>
    				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("plano_conta",C_UCWORDS));?>:</strong></td>
						<td align="left" colspan="3">
						<select name="DBVAR_STR_COD_PLANO_CONTA" id="DBVAR_STR_COD_PLANO_CONTA" style="width:200px" class="campos" >
						<option value=""></option>
							<?php echo montaCombo($objConn, "SELECT 
															  cod_plano_conta,
															  cod_reduzido || ' ' || nome AS descr_plano
															FROM fin_plano_conta 
															WHERE dtt_inativo IS NULL AND (cod_reduzido || ' ' || nome) IS NOT NULL
															ORDER BY nivel, ordem, nome ", "cod_plano_conta","descr_plano",''); ?>
						</select>
						</td>
					</tr>
					<!--
					//Deixar desativado porque pessoal do Sindiprom/Ubrafe não vai usar
					//by Clv/GS - 04/01/2012
    				<tr bgcolor="<?php //echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php //echo(getTText("centro_custo",C_UCWORDS));?>:</strong></td>
						<td align="left" colspan="3">
						<select name="DBVAR_STR_COD_CENTRO_CUSTO" id="DBVAR_STR_COD_CENTRO_CUSTO" style="width:200px" class="campos" >
						<option value=""></option>
							<?php //echo montaCombo($objConn, "SELECT cod_centro_custo, cod_reduzido || ' ' || nome AS descr_plano FROM fin_centro_custo WHERE dtt_inativo IS NULL AND (cod_reduzido || ' ' || nome) IS NOT NULL ORDER BY nivel, ordem, nome ", "cod_centro_custo","descr_plano",''); ?>
						</select>
						</td>
					</tr>
					-->
    				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("job",C_UCWORDS));?>:</strong></td>
						<td align="left" colspan="3">
						<select name="DBVAR_STR_COD_JOB" id="DBVAR_STR_COD_JOB" style="width:200px" class="campos" >
						<option value=""></option>
							<?php echo montaCombo($objConn, "SELECT cod_job, nome AS descr_plano
															 FROM fin_job 
															 WHERE dtt_inativo IS NULL 
															 ORDER BY ordem, nome ", "cod_job","descr_plano",''); ?>
						</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("quantidade",C_UCWORDS));?>:</strong></td>
						<td width="25%" align="left"> <input type="text" name="DBVAR_MOEDA_QUANTIDADE" id="DBVAR_MOEDA_QUANTIDADE" size="7" maxlength="7" onKeyPress="Javascript:return validateFloatKeyNew(this, event);" onBlur="calcula();" /></td>
						<td width="21%" align="right"><strong><?php echo(getTText("unidade",C_UCWORDS));?>:</strong></td>
						<td width="41%" align="left"> <input type="text" name="DBVAR_STR_UNIDADE" id="DBVAR_STR_UNIDADE"  size="15" maxlength="2" /></td>						
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("ipi",C_UCWORDS));?>:</strong></td>
						<td align="left"><input type="text" name="DBVAR_MOEDA_VLR_IPI" id="DBVAR_MOEDA_VLR_IPI" onKeyPress="Javascript:return validateFloatKeyNew(this, event);" size="15" maxlength="11" /></td>
					    <td align="right"><strong><?php echo(getTText("icms",C_UCWORDS));?>:</strong></td>
					    <td align="left"><input type="text" name="DBVAR_MOEDA_VLR_ICMS" id="DBVAR_MOEDA_VLR_ICMS" onKeyPress="Javascript:return validateFloatKeyNew(this, event);" size="15" maxlength="11" /></td>
					</tr>					
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("preco_final",C_UCWORDS));?>:</strong></td>
					    <td width="25%" align="left"><input type="text" name="DBVAR_MOEDA_VLR_UNITARIO" id="DBVAR_MOEDA_VLR_UNITARIO" onKeyPress="Javascript:return validateFloatKeyNew(this, event, 'yes')" onBlur="calcula();"  size="15" maxlength="19" /></td>
						<td width="21%" align="right"><strong><?php echo(getTText("sub_total",C_UCWORDS));?>:</strong></td>
						<td width="41%" align="left"><strong><input type="text" name="DBVAR_MOEDA_VLR_TOTAL" id="DBVAR_MOEDA_VLR_TOTAL" size="20" value="" readonly="true" style="border:none; text-decoration:blink" /></strong></td>
					  <!-- FIM CAMPOS -->
					<tr><td colspan="4" class="destaque_med"></td></tr>
					<tr><td colspan="4" class="linedialog"></td></tr>
				</table>			
			</td>
		</tr>
		<!-- LINHA DOS BUTTONS E AVISO -->
		<tr>
			<td colspan="3" style="padding:10px 50px 0px 50px;">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td width="70%">
						</td>
						<td width="10%" align="left">
							<button onClick="ok();return false;"><?php echo(getTText("ok",C_UCWORDS));?></button>
						</td>
						<td width="10%" align="left">
							<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
						</td>
						<td width="10%" align="left">
							<button onClick="aplicar();return false;"><?php echo(getTText("aplicar",C_UCWORDS));?></button>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>	
	</table>
	</form>
	<?php athEndFloatingBox();?>
	</td>
	</tr>
</table>
</body>
	<script type="text/javascript">
		resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo $intCodDado; ?>',20);
	</script>
</html>
<?php $objConn = NULL; ?>