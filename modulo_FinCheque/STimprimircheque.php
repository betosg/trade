<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// Verificação de ACESSO
	// Carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// Verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"IMP");

	// REQUESTS
	// Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente
	$intCodDado   = request("var_chavereg");	// CODIGO DO CHEQUE
		
	if($intCodDado == ""){
		mensagem("err_sql_desc_cod_miss","err_impr_cheque",getTText("cheque_cod_null",C_NONE),'','erro','1');
		die();
	}

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// Busca os dados do CHEQUE para exibição em tela
	try{
		$strSQL = "
			SELECT 
				  fin_cheques.cod_cheques 
				, fin_cheques.idcheque
				, fin_cheques.nrocheque
				, fin_cheques.valorcheque
				, fin_cheques.datacheque
				, fin_cheques.referencia
				, fin_cheques.cedente
				, fin_cheques.qtde_impresso
				, fin_banco.nome
				, fin_banco.modelo_cheque_link
				, fin_banco.modelo_cheque_img
			FROM  fin_cheques
			INNER JOIN fin_banco ON (fin_banco.num_banco = fin_cheques.idbanco)
			WHERE fin_cheques.cod_cheques = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	// Fetch dos dados localizados
	$objRS = $objResult->fetch();
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE." - ".getTText("impr_cheques",C_NONE)); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css">
</style>
		<script type="text/javascript">
			var strLocation = null;
			function ok(){ 
			<?php if(getValue($objRS,"modelo_cheque_link") == ""){?>
				alert("Este Banco NÃO possui um modelo de cheque cadastrado!");
				window.close();
			<?php }?>
				submeterForm(); 
			}
			function cancelar()     { window.close(); }
			function submeterForm() { document.formstatic.submit();	}
		</script>
	</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("605","",getTText("impressao_cheque_modelos",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<form name="formstatic" action="../modulo_FinCheque/<?php echo(getValue($objRS,"modelo_cheque_link"))?>" method="post">
	<input type="hidden" name="var_chavereg"    value="<?php echo($intCodDado);?>" />
	<input type="hidden" name="var_valorcheque" value="<?php echo(getValue($objRS,"valorcheque"));?>" />
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="585" bgcolor="#FFFFFF" class="table_master" style="border:1px solid #BBB;">
		<tr><td align="left" valign="top" style="padding:15px 0px 0px 15px;"><strong><?php echo(getTText("confirmar_dados_cheque",C_NONE));?>:</strong></td></tr>
		<tr>
			<td align="left" valign="top" style="padding:10px 30px 10px 30px;">
				<table cellspacing="2" cellpadding="4" border="0" width="100%">
					<!-- DADOS CHEQUE -->
					<tr><td></td><td align="left" class="destaque_gde" colspan="2"><strong><?php echo(getTText("dados_cheque",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right"><strong><?php echo(getTText("cod_cheques",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<?php echo(getValue($objRS,"cod_cheques"));?>&nbsp;&nbsp;&nbsp;&nbsp;
							<strong><?php echo(getTText("idcheque",C_UCWORDS));?>:</strong>
							<?php echo(getValue($objRS,"idcheque"));?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right"><strong><?php echo(getTText("nrocheque",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<?php echo(getValue($objRS,"nrocheque"));?>&nbsp;&nbsp;&nbsp;&nbsp;
							<strong><?php echo(getTText("valorcheque",C_UCWORDS));?>:</strong>
							<?php echo(number_format((double) getValue($objRS,"valorcheque"),2,',','.'));?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right"><strong><?php echo(getTText("datacheque",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"datacheque"),false));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right"><strong><?php echo(getTText("cedente",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><?php echo(getValue($objRS,"cedente"));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right"><strong><?php echo(getTText("referencia",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><?php echo(getValue($objRS,"referencia"));?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right"><strong><?php echo(getTText("nomebanco",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<?php echo(getValue($objRS,"nome"));?>
							<span style="padding-left:15px;">
								<img src="../img/icon_anexo_cheque.gif" width="14" border="0" onMouseOver="showCheque(true,'img_modelo_cheque');" onMouseOut="showCheque(false,'img_modelo_cheque');" />
								<div style="display:none;padding:15px;border:1px solid #777;background-color:#FFF;position:absolute;bottom:62%;right:1%;" id="img_modelo_cheque"><img id="img_cheque" src="../img/<?php echo(getValue($objRS,"modelo_cheque_img"))?>" width="210" /></div>
							</span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right"><strong><?php echo(getTText("qtde_impresso",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><?php echo(getValue($objRS,"qtde_impresso"));?></td>
					</tr>
					<!-- DADOS CHEQUE -->
										
					<tr><td colspan="2">&nbsp;</td></tr>
									
					<!-- OPÇÕES MODELO -->
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("opcoes_de_cheque",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("tipo_cheque",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<table cellpadding="0" cellspacing="0" border="0" width="100%" style="align:left;">
								<tr>
									<td>
										<input type="checkbox" name="var_cruzado" id="var_cheque_cruzado" value="1" class="inputclean" checked="checked" /><?php echo(getTText("cheque_cruzado",C_NONE));?>
										<br />
										<input type="checkbox" name="var_nominal" id="var_cheque_nominal" value="1" class="inputclean" checked="checked" /><?php echo(getTText("cheque_nominal",C_NONE));?>
										<span class="comment_peq"><?php echo(getTText("cheque_nominal_obs",C_NONE));?></span>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<!-- OPÇÕES MODELO -->
					
					<tr><td colspan="2">&nbsp;</td></tr>
					
					<tr><td colspan="2" style="border-bottom:1px solid #CCC;text-align:left"><span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span></td></tr>
					<tr>
						<td colspan="2">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<td width="10%" align="right"><img src="../img/mensagem_aviso.gif" /></td>
									<td width="55%" align="left" style="padding-left:10px;"><?php echo(getTText("aviso_impr_cheque_txt",C_NONE));?></td>
									<td width="35%" align="right">
										<button onClick="ok();return false;"><?php echo(getTText("ok",C_NONE));?></button>
										<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_NONE));?></button>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>			
			</td>
		</tr>
	</table>
</form>
<?php athEndFloatingBox();?>
</center>
</body>
</html>