<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página - cod_credencial
//$intCodPedido   = request("var_cod_pedido");   // Código do pedido, caso exista
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = (request("var_populate") == "") ? "yes" : request("var_populate");   // popular o session ou não
$strAcao   	  = request("var_acao");      // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

//$intCodDado = 45455;

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA");

//Cores linhas
$strBGColor = CL_CORLINHA_2;

//Inicia objeto para manipulação do banco
$objConn = abreDBConn(CFG_DB);

// Só é feito a busca e exibição dos dados 
// seja enviado como parametro para este script.
if($intCodDado == ""){
	$strErro = "Carteirinha inválida. Tente novamente.";
	mensagem("err_sql_titulo","err_sql_desc_card",$strErro,"","aviso",1);
	die();
}
else {
	// Verifica a existencia de pedidos abertos de carteirinhas para 
	// a PJ corrente - Busca usando como parâmetro o cod_pedido 
	// tbm enviado para esta pag. - uma das situações
	try{
		$strSQL = "SELECT	sd_credencial.cod_credencial
						,	sd_credencial.cod_pf
						,	sd_credencial.cod_pj
						,	sd_credencial.cod_pedido
						,	sd_credencial.pf_nome
						,	sd_credencial.pf_empresa
						,	sd_credencial.pf_rg
						,	sd_credencial.pf_cpf
						,	sd_credencial.pf_funcao
						,	sd_credencial.pf_matricula
						,	sd_credencial.dt_validade
						,	sd_credencial.qtde_impresso
						FROM
							sd_credencial
						WHERE
							sd_credencial.cod_credencial = " . $intCodDado;
						$objResult = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc","","","erro",1);
		die();
	}
	//fetch dos dados da credencial
	$objRS 			  = $objResult->fetch();
	$intCodCredencial = getValue($objRS,"cod_credencial");
	$strNomePF 		  = getValue($objRS,"pf_nome");
	$strRazaoSocial   = getValue($objRS,"pf_empresa");
	$intRG 			  = getValue($objRS,"pf_rg");
	$intCPF 		  = getValue($objRS,"pf_cpf");
	$intCodPF 		  = getValue($objRS,"cod_pf");
	$intCodPJ    	  = getValue($objRS,"cod_pj");
	$strFuncao 		  = getValue($objRS,"pf_funcao");
	$intNumMatricula  = getValue($objRS,"pf_matricula");
	$intCodPedido 	  = getValue($objRS,"cod_pedido");
	$intQtdeImpr 	  = getValue($objRS,"qtde_impresso");
	$dtValidade 	  =	dDate(CFG_LANG, getValue($objRS,"dt_validade"), false);
	
}
// seleciona o valor do produto 'carteirinha' 
// corrente para sugestão no campo input
// pega a mais recente
try{
	// OLD, NÃO PEGA O VALOR MAIS DO PRODUTO CARTEIRINHA, 
	// MAS PEGA O VALOR DO PEDIDO DA CREDENCIAL QUE ESTÁ SENDO REEMITIDA
	// $strSQL = " SELECT valor FROM prd_produto WHERE tipo = 'card' AND dtt_inativo IS NULL AND CURRENT_TIMESTAMP <= dt_fim_val_produto AND visualizacao = 'publico' ORDER BY dt_fim_val_produto DESC ";
	$strSQL = "SELECT valor FROM prd_pedido INNER JOIN sd_credencial ON (prd_pedido.cod_pedido = sd_credencial.cod_pedido AND sd_credencial.cod_credencial = ".$intCodCredencial.")";
	$objResult = $objConn->query($strSQL);	
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
$objRS = $objResult->fetch();
$intValorProdCard = (getValue($objRS,"valor") == "") ? "" : (getValue($objRS,"valor")); 

?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
		<!--
			//****** Funções de ação dos botões - Início ******
			var strLocation = null;
			function ok() {
				document.formstatic.submit();
			}

			function cancelar() {
				//location.href="../modulo_PainelAdmin/STindex.php";
				window.close();
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
	<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
		<tr>
   			<td align="center" valign="top">
			<?php athBeginFloatingBox("600","none","GERAR CREDENCIAL (para Pessoa Física)",CL_CORBAR_GLASS_1); ?>
    			<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    			<form name="formstatic" action="STGeraCardexec.php" method="post">
					<input type="hidden" name="var_action" value="">
					<input type="hidden" name="var_cod_pf" value="<?php echo($intCodPF);?>">
					<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ);?>">
					<input type="hidden" name="var_cod_credencial" value="<?php echo($intCodCredencial);?>">
					<input type="hidden" name="var_qtde_impr" value="<?php echo($intQtdeImpr);?>">
						<tr>
							<td height="12" style="padding:20px 0px 0px 20px;">
								<strong>
									<?php echo(getTText("confirmacao_emitir_card",C_NONE)); ?>
								</strong>
							</td>
						</tr>
						<tr>
							<td align="center" valign="top" style="padding:20px 50px 10px 50px;" width="1%">
								<table cellpadding="4" cellspacing="0" border="0" width="100%">
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right" width="35%"> 
											<strong>
												<?php echo(getTText("cod_credencial",C_NONE)); ?>
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCodCredencial); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
										<td align="right" width="35%"> 
											<strong>
												<?php echo(getTText("qtde_impresso",C_NONE)); ?>
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intQtdeImpr); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right" width="35%"> 
											<strong>
												<?php echo(getTText("cod_pedido",C_NONE)); ?>
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCodPedido); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("pf_empresa",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strRazaoSocial); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("pf_nome",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strNomePF); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("pf_rg",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intRG); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("pf_cpf",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCPF); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("pf_funcao",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strFuncao); ?>	</td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("num_matricula",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intNumMatricula); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("dt_validade",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($dtValidade); ?></td>
									</tr>
									<tr>
										<td height="1" colspan="2" bgcolor="#DBDBDB">
										</td>
									</tr>
								</table>
					<?php if($intQtdeImpr == 0) {?>	
								<tr>
								<td style="padding: 0px 0px 20px 0px;">
								<table cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
									<tr>
										<td align="right" style="padding: 0px 0px 00px 70px;">
											<img src="../img/mensagem_info.gif">
										</td>
										<td align="left" style="padding: 0px 0px 0px 10px;">
											<?php echo(getTText("aviso_impr_card1",C_NONE))?>
										</td>
								<?php 
							} 
							else
							{
								?>
									<tr>
										<td style="padding: 0px 0px 20px 110px;">
											<table cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td>
														<input type="radio" class="inputclean" name="var_cobr" id="var_cobr" value="cobr_none" style="border:none; background-color:#FFFFFF">
													</td>
													<td nowrap="nowrap">
														<?php echo(getTText("gera_cobranca_none",C_NONE))?>
													</td>
												</tr>
												<tr>
													<td>
														<input type="radio" class="inputclean" name="var_cobr" id="var_cobr" value="cobr_novo" style="border:none;">
													</td>
													<td nowrap="nowrap">
														<?php echo(getTText("gera_cobranca_normal",C_NONE))?>&nbsp;<input type="text" name="var_valor" id="var_valor" dir="rtl" size="15" onKeyPress="Javascript:return( validateFloatKeyNew(this, event));" value="<?php echo($intValorProdCard)?>">
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td style="padding: 0px 0px 20px 0px;">
											<table cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
												<tr>
													<td align="right" style="padding: 0px 0px 00px 70px;">
														<img src="../img/mensagem_info.gif">
													</td>
													<td align="left" style="padding: 0px 0px 0px 10px;">
														<?php echo(getTText("aviso_impr_card2",C_NONE))?>
													</td>
								<?php }?>
										<td width="1%" align="left" style="padding:10px 50px 10px 10px;" nowrap>
											<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
											<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
										</td>
									</tr>
								</table>	
							</tr>
						</td>
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