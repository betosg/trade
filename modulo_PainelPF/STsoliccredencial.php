<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$chave 			= request("chave");
$intCodDado 	= request("var_chavereg"); // cod pf
$intCodPJ 		= request("var_cod_pj");
$intCodProduto  = request("var_cod_produto"); // cod_PRODUTO
$strOperacao 	= request("var_oper"); // Operação a ser realizada
$strExec 		= request("var_exec"); // Executor externo (fora do kernel)
$strPopulate 	= request("var_populate"); // Flag para necessidade de popular o session ou não
$strAcao 		= request("var_acao"); // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "HOMO");

if($chave==1){?>
	<script language="javascript">
		window.location = "../modulo_PainelPJ/STsolicTodosCard.php?var_cod_pj=<?php echo $intCodPJ; ?>";
	</script>
<?php	
}else{
//Inicia objeto para manipulação do banco
$objConn = abreDBConn(CFG_DB);

// LOCALIZA O TIPO DE COLABORADOR
if(($intCodDado != "") && ($intCodPJ != "")){
	try{
		$strSQL = "SELECT tipo FROM relac_pj_pf WHERE cod_pj = ".$intCodPJ." AND cod_pf = ".$intCodDado;
		$objResultRELAC = $objConn->query($strSQL);
		// echo($objResultSIND->rowCount());
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
}

// LOCALIZA CONTA A PAGAR DO ANO VIGENTE OU ANTERIOR QUE ESTEJA ABERTA E QUE SEJA SINDICAL
// Reativando(??? já deveria estar ativo) código de verificação dessa condição. A idéia é que a PJ não pode pedir 
// credenciais se tiver um título de sindical em aberto. Isso foi feito uma vez, depois alterado em final de 
// novembro pelo Gabriel, rediscutido por email com Patrícia e agora está assim a pedido do Alexandre depois da 
// conversa dele com Aless no dia de hoje
//
// by Clv - 09/01/2012

$intTotalSindEmAberto = 0;
if((getsession(CFG_SYSTEM_NAME."_grp_user") != "ADMIN") && (getsession(CFG_SYSTEM_NAME."_grp_user") != "SU")){
	try{
		$strSQL = " SELECT cod_conta_pagar_receber FROM fin_conta_pagar_receber 
					WHERE situacao ILIKE 'aberto' 
					AND (historico ILIKE '%sindical%' OR historico ILIKE '%sind%' OR historico ILIKE '%GRCS%') 
					AND codigo = ".$intCodPJ." AND ano_vcto <= ".date("Y");
		$objResultSIND = $objConn->query($strSQL);
		$intTotalSindEmAberto = $objResultSIND->rowCount();
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	if ($intTotalSindEmAberto == "") $intTotalSindEmAberto = 0;
	
	if($intTotalSindEmAberto > 0){
		mensagem("err_sql_titulo","err_sql_desc_card",getTText("contrib_sindical_nao_paga",C_NONE),"../modulo_PainelPJ/STindex.php","aviso",1);
		die();
	}
}

// busca o produto do tipo card de maior valor
// e validade corrente para exibicao dos dados
// em tela - confirmação para a PJ logada
// AND CURRENT_DATE < dt_fim_val_produto
try{
	$strSQL = "
			SELECT
				rotulo,
				descricao, 
				cod_produto, 
				valor
    		FROM prd_produto
		    WHERE cod_produto = ".$intCodProduto;
	$objResult = $objConn->query($strSQL);	
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
$objRS = $objResult->fetch();
$intCodProduto   = getValue($objRS,"cod_produto");
$strRotuloProd 	 = getValue($objRS,"rotulo");
$strDescProd     = getValue($objRS,"descricao");
$dblValorProduto = getValue($objRS,"valor");

// busca a PF corrente com base no cod_pf
// enviado e cod_pj tbm. Para exibição dos
// dados em tela - confirmação da PJ
try{
	$strSQL = "
			SELECT 
				cad_pf.cod_pf,
				cad_pj.razao_social,
				cad_pj.cod_pj,
				cad_pj.cnpj,
				cad_pf.nome,
				cad_pf.cpf,
				cad_pf.rg,
				cad_pf.email,
				cad_pf.matricula,
				relac_pj_pf.funcao
			FROM cad_pf, relac_pj_pf, cad_pj, sd_credencial 
			WHERE cad_pf.cod_pf = '".$intCodDado."'
			AND relac_pj_pf.cod_pf = cad_pf.cod_pf
			AND	relac_pj_pf.cod_pj = '".$intCodPJ."'
			AND cad_pj.cod_pj = '".$intCodPJ."'";
	$objResult = $objConn->query($strSQL);	
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
$objRS = $objResult->fetch();
$intCodPF 		= getValue($objRS,"cod_pf");
$intRG			= getValue($objRS,"rg");
$intCPF			= getValue($objRS,"cpf");
$strNome		= getValue($objRS,"nome");
$strEmail		= getValue($objRS,"email");
$intMatricula	= getValue($objRS,"matricula");
$strFuncao		= getValue($objRS,"funcao");
$strRazaoSocial	= getValue($objRS,"razao_social");
$intCNPJ		= getValue($objRS,"cnpj");
$intCodPJ		= getValue($objRS,"cod_pj");

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
				location.href="../modulo_PainelPJ/STindex.php";
				//window.history.back();
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("solic_card",C_NONE),CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	<form name="formstatic" action="STsoliccardexec.php" method="post">
		<input type="hidden" name="var_cod_produto" value="<?php echo($intCodProduto);?>" />
		<input type="hidden" name="var_cod_pf" value="<?php echo($intCodPF);?>" />
		<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ);?>" />
		<input type="hidden" name="var_opcao" value="uma_empresa" />
		<input type="hidden" name="var_valor" value="<?php echo($dblValorProduto);?>" />
		<tr>
			<td height="12" style="padding:20px 0px 0px 20px;">
				<strong><?php echo(getTText("solicitacao_card",C_NONE)); ?></strong>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" style="padding:20px 50px 10px 50px;" width="1%">
				<table cellpadding="4" cellspacing="0" border="0" width="100%">
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS PESSOA FÍSICA</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("cod_pf",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intCodPF); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("int_matricula",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intMatricula); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("nome",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNome); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("rg",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intRG); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("cpf",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intCPF); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("email",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strEmail); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("funcao",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strFuncao); ?></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS PESSOA JURÍDICA</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("cod_pj",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intCodPJ); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("razao_social",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strRazaoSocial); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("cnpj",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($intCNPJ); ?></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>OBSERVAÇÕES DA SOLICITAÇÃO</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("rotulo",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strRotuloProd); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("descricao",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strDescProd); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("valor",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo(number_format((double) $dblValorProduto,2,",",".")); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("obs",C_NONE)); ?>:</strong>
						</td>
						<td><textarea id="var_obs" name="var_obs" rows="5" cols="55"></textarea></td>
					</tr>				
					<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
								<tr>
									<td align="right" width="1%" style="padding: 0px 0px 0px 0px;"><img src="../img/mensagem_info.gif"></td>
									<td align="left" width="98%" style="padding: 0px 0px 0px 10px;"><?php echo(getTText("aviso_solic_card",C_NONE))?></td>
									<td width="1%" align="left" style="padding:10px 10px 10px 10px;" nowrap>
										<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
										<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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
	<script type="text/javascript">
	  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php 
	$objConn = NULL; 
	}
?>