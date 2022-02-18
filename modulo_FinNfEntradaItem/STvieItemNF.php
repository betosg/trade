<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	/***           DEFINIÇÃO DE PARÂMETROS            ***/
	/****************************************************/
	$intCodDado		 = request("var_chavereg");
 	$intCodNfPai	 = request("var_codnfpai");
	
	$strRedirect = request("var_redirect"); // redirect para qual página deve ir
	$strPopulate = (request("var_populate") == "") ? "yes" : request("var_populate");
	
	// abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);
	
	/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
	/****************************************************/
	if($strPopulate == "yes") { initModuloParams(basename(getcwd())); }
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	// Controle de acesso diferenciado por estar em nível IFRAME.
	// caso sua página não esteja em um IFRAME DETAIL, utilize a-
	// penas a linha abaixo:
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"UPD");
	// no caso abaixo, fazemos a verficarAcesso() retornar um valor
	// false caso o usuário nao tenha direito sobre a app, e com base
	// no true ou false manipulamos a mensagem para que funcione no
	// IFRAME DETAIL. Ainda assim, posicionamos esse trecho de codigo
	// aqui pq anteriormente não tínhamos o cod_fornec para que seja
	// feito resize do iframe.
	if(!verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS","not die")){
		mensagem("err_acesso_titulo","err_acesso_desc","Ação a ser realizada:&nbsp;INS","","erro",1,"not html");
		$strScript  = "";
		$strScript .= "<script type=\"text/javascript\">";
		$strScript .= "/* usado para redimensionar o IFRAME */";
		$strScript .= "resizeIframeParent('" . CFG_SYSTEM_NAME . "_detailiframe_" . $intCodInd . "',05)";
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

	try{
		 $strSQL = "SELECT 
					  t1.cod_nf_entrada_item,
					  t1.cod_nf_entrada,
					  t1.descr_produto,
					  t1.cod_cfop,
					  t1.cod_sit_trib,
					  t1.quantidade,
					  t1.unidade,
					  t1.vlr_unitario,
					  t1.vlr_total,
					  t1.vlr_icms_aliq,
					  t1.vlr_icms_base,
					  t1.vlr_icms,
					  t1.vlr_ipi_aliq,
					  t1.vlr_ipi,
					  t2.cod_reduzido || ' ' || t2.nome AS descr_plano_conta,
					  t3.cod_reduzido || ' ' || t3.nome AS descr_centro_custo,
					  t4.nome AS descr_job
					FROM fin_nf_entrada_item t1
					LEFT JOIN fin_plano_conta t2 ON (t1.cod_plano_conta = t2.cod_plano_conta)
					LEFT JOIN fin_centro_custo t3 ON (t1.cod_centro_custo = t3.cod_centro_custo)
					LEFT JOIN fin_job t4 ON (t1.cod_job = t4.cod_job)
					WHERE t1.cod_nf_entrada_item = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	$objRS = $objResult->fetch();
	//echo getValue($objRS,"descr_produto");


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
/* seu código javascript aqui */
/****** Funções de ação dos botões - Início ******/
var strLocation = null;

			function ok() {			
					document.location.href = "../modulo_FinNfEntradaItem/STitemNF.php?var_codnfpai=<?php echo $intCodNfPai; ?>";
			}
			

			function cancelar() {
				document.location.href = "../modulo_FinNfEntradaItem/STitemNF.php?var_codnfpai=<?php echo $intCodNfPai; ?>";
			}			

			function submeterForm() {
				document.formstatic.DEFAULT_LOCATION.value = strLocation;				
				document.formstatic.submit();
			}

	/****** Funções de ação dos botões - Fim ******/
</script>
	</head>
	
	<!-- UTILIZAMOS O BODY ABAIXO QUANDO ESTA PÁGINA NÃO É CHAMADA EM UMA IFRAME DETAIL -->
	<!--<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" 
	     background="../img/bgFrame_|?php echo(CFG_SYSTEM_THEME);?|_main.jpg">-->
	
<body bgcolor="#FFFFFF" style="margin:10px 0px 0px 0px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
	<td align="center">
	<?php athBeginFloatingBox("520","none",getTText("items_vizualizar_title",C_NONE),CL_CORBAR_GLASS_1); ?>
    <form name="formstatic" action="" method="POST">
	<input type="hidden" name="DEFAULT_TABLE" 				value="fin_nf_entrada_item" />
	<input type="hidden" name="DEFAULT_DB" 					value="<?php echo(CFG_DB);?>" />
	<input type="hidden" name="FIELD_PREFIX" 				value="DBVAR_" />
	<input type="hidden" name="RECORD_KEY_NAME" 			value="cod_nf_entrada_item" />
    <input type="hidden" name="RECORD_KEY_VALUE" 			value="<?php echo($intCodDado);?>" />
	<input type="hidden" name="DEFAULT_LOCATION"			value=""/>
	<input type="hidden" name="DBVAR_INT_COD_NF_ENTRADA"	value="<?php echo $intCodNfPai; ?>">
	<input type="hidden" name="DBVAR_STR_SYS_USR_UPD"		value="<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>" />
	<input type="hidden" name="DBVAR_AUTODATE_SYS_DTT_UPD"	value="false" />	
    
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
						<td align="left" class="destaque_gde" colspan="3"><strong><?php echo(getTText("dados_contato_ins",C_TOUPPER));?></strong></td>
					</tr>
					<tr><td colspan="4" height="2" background="../img/line_dialog.jpg"></td></tr>

					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("codnf",C_UCWORDS));?>:</strong></td>
						<td align="left" colspan="3"><?php echo $intCodDado;?></td>
					</tr>    				
					
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("desc_prod",C_UCWORDS));?>:</strong></td>
						<td align="left" colspan="3"><?php echo getValue($objRS,"descr_produto");?></td>
					</tr>
    				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("plano_conta",C_UCWORDS));?>:</strong></td>
						<td align="left" colspan="3"><?php echo getValue($objRS,"descr_plano_conta");?></td>
					</tr>
					<!--
					//Deixar desativado porque pessoal do Sindiprom/Ubrafe não vai usar
					//by Clv/GS - 04/01/2012
    				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php //echo(getTText("centro_custo",C_UCWORDS));?>:</strong></td>
						<td align="left" colspan="3"><?php //echo getValue($objRS,"descr_centro_custo");?></td>
					</tr>
					-->
    				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("job",C_UCWORDS));?>:</strong></td>
						<td align="left" colspan="3"><?php echo getValue($objRS,"descr_job");?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("quantidade",C_UCWORDS));?>:</strong></td>
						<td width="25%" align="left"><?php echo getValue($objRS,"quantidade");?></td>
						
						<td width="21%" align="right"><strong><?php echo(getTText("unidade",C_UCWORDS));?>:</strong></td>
						<td width="41%" align="left"><?php echo getValue($objRS,"unidade");?></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("ipi",C_UCWORDS));?>:</strong></td>
						<td align="left"><?php echo getValue($objRS,"vlr_ipi");?></td>
					    <td align="right"><strong><?php echo(getTText("icms",C_UCWORDS));?>:</strong></td>
					    <td align="left"><?php echo getValue($objRS,"vlr_icms");?></td>
					</tr>					
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="13%" align="right"><strong><?php echo(getTText("preco_final",C_UCWORDS));?>:</strong></td>
					  <td width="25%" align="left"><?php echo getValue($objRS,"vlr_unitario");?></td>
						
						<td width="21%" align="right"><strong><?php echo(getTText("sub_total",C_UCWORDS));?>:</strong></td>
						<td width="41%" align="left"><?php echo getValue($objRS,"vlr_total");?></td>	
					  <!-- FIM CAMPOS -->
					<tr><td colspan="4" class="destaque_med"></td></tr>
					<tr><td colspan="4" class="linedialog"></td></tr>
				</table>			
			</td>
		</tr>
		<!-- LINHA DOS BUTTONS E AVISO -->
		<tr>
			<td colspan="4" style="padding:10px 50px 0px 50px;">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td width="70%">
							
							<!-- MENSAGEM DE AVISO VAI AQUI, PARA DIALOG DE DELEÇÃO -->
							<!-- CASO VOCÊ QUEIRA INFORMAR UMA MENSAGEM, ALTERE O ICONE
							 	 E O LANG UTILIZADO PARA A MENSAGEM --->
							<table cellspacing="0" cellpadding="0" border="0" width="100%">
								<tr>
									<td align="right" width="23%">
										<!--<img src="../img/mensagem_aviso.gif" />-->
									</td>
									<td align="left"  width="77%" style="padding-left:5px;">
										<?php //echo(getTText("aviso_del_txt",C_NONE));?>
									</td>
								</tr>
							</table>
							<!-- BLOCO PARA MENSAGEM . FIM -->
							
						</td>
						<!-- goNext() -->
						<td width="20%" align="left">
							<button onClick="ok();">
								<?php echo(getTText("ok",C_UCWORDS));?>
							</button>
						</td>
						<td width="10%" align="left">
							<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>	
		<!-- LINHA ACIMA DOS BOTÕES -->
	</table>
	</form>
    
	<?php 
		athEndFloatingBox();?>
	</td>
	</tr>
</table>
</body>
	<script type="text/javascript">
	  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodNfPai); ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php $objConn = NULL; ?>