<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");      // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.
$intCodPF   = request("var_cod_pf");  // requests STs - Código da PF, caso exista
$intCodPJ   = request("var_cod_pj");   // request Cod_PJ, enviado pelo script pai  
$strDescProd   = request("var_desc_prod");   // request Cod_PJ, enviado pelo script pai  
$strAcaoRadio = request("var_cobr"); // verifica que acao tomar referente a opcao selecionada na gerac. de carteirinha

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA");

//Cores linhas
$strBGColor = CL_CORLINHA_2;

//Inicia objeto para manipulação do banco
$objConn = abreDBConn(CFG_DB);

//caso nenhuma opção tenha sido selecionada,
//printa em tela erro. a interface trava a falta de
//seleção
if($strAcaoRadio == ""){
	$strErro = "Nenhuma opção selecionada.";
	mensagem("err_sql_titulo","err_sql_desc_card",$strErro,"../modulo_CadPF/STgeracarteirinha.php?var_chavereg=".$intCodDado,"aviso",1);
	die();
}
else{
	// Busca códigos default para conta bancária, 
	// plano de conta e centro de custo
	$intCodConta = "";
	$intCodPlanoConta = "";
	$intCodCentroCusto = "";
	
	$strSQL1 = " SELECT valor FROM sys_var_entidade WHERE id_var = 'fin_cod_conta_default' ";
	$strSQL2 = " SELECT valor FROM sys_var_entidade WHERE id_var = 'fin_cod_plano_conta_default' ";
	$strSQL3 = " SELECT valor FROM sys_var_entidade WHERE id_var = 'fin_cod_centro_custo_default' ";
	
	$objResult1 = $objConn->query($strSQL1);
	$objResult2 = $objConn->query($strSQL2);
	$objResult3 = $objConn->query($strSQL3);
	
	if ($objResult1->rowCount() > 0) {
		$objRS1 = $objResult1->fetch();
		$intCodConta = getValue($objRS1, "valor");
	}
	if ($objResult2->rowCount() > 0) {
		$objRS2 = $objResult2->fetch();
		$intCodPlanoConta = getValue($objRS2, "valor");
	}
	if ($objResult3->rowCount() > 0) {
		$objRS3 = $objResult3->fetch();
		$intCodCentroCusto = getValue($objRS3, "valor");
	}
	
	if ($intCodConta == "") $intCodConta = "NULL";
	if ($intCodPlanoConta == "") $intCodPlanoConta = "NULL";
	if ($intCodCentroCusto == "") $intCodCentroCusto = "NULL";
	
	$objResult1->closeCursor();
	$objResult2->closeCursor();
	$objResult3->closeCursor();
	
	/*
	//busca dados do pedido para inserção de novo titulo
	try{
		$strSQL = "SELECT
							prd_pedido.cod_pedido,
							prd_pedido.valor,
							prd_produto.rotulo,
							prd_produto.descricao,
							prd_pedido.cod_pj,
							prd_pedido.it_cod_pf
						FROM
							prd_pedido,
							prd_produto
						WHERE
							prd_pedido.cod_pedido = '" . $intCodDado . "'
						AND
							prd_produto.cod_produto = prd_pedido.it_cod_produto
						AND
							prd_produto.tipo ILIKE '%card%'
						AND
							prd_pedido.situacao ILIKE '%aberto%'";
		$objResult = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc","","../modulo_PainelAdmin/STindex.php","erro",1);
		die();
	}
	if(($objResult->rowCount()) > 0) {
		//fetch dos dados do pedido
		$objRS = $objResult->fetch();
		$intCodPF =  (getValue($objRS,"it_cod_pf") == "") ? ""  : getValue($objRS,"it_cod_pf");
		$intCodPJ =  (getValue($objRS,"cod_pj") == "") ? ""  : getValue($objRS,"cod_pj");
		$intCodPedido =  (getValue($objRS,"cod_pedido") == "") ? ""  : getValue($objRS,"cod_pedido");
		$intValorPed = (getValue($objRS,"valor") == "") ? "" : getValue($objRS,"valor");
		$strRotuloProd = (getValue($objRS,"rotulo") == "") ? "" : getValue($objRS,"rotulo");
		$strDescProd = (getValue($objRS,"descricao") == "") ? "" : getValue($objRS,"descricao");
	}*/
	
	//verificacao da acao do botão radio
	if($strAcaoRadio == "cobr_normal"){
		try
	}
		
	}
	if($strAcaoRadio == "cobr_normal"){
		echo("normal");
	}
	if($strAcaoRadio == "cobr_impr"){
		echo("impr");
	}
}


//Esta é uma das situações possíveis, o código do pedido
//e da PF serem enviadas para este script, significa que o
//o pedido já foi feito e devemos apenas confirmar a co-
//brança de um titulo para a carteirinha.
if(($intCodDado != "") && ($intCodPedido != "")){
	//Verifica se existe uma relação válida para a PF
	try{
		$strSQL = "SELECT 
							relac_pj_pf.cod_pj
						FROM
							relac_pj_pf
						WHERE
							relac_pj_pf.cod_pf = '" . $intCodDado . "'";
	$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		$strErro = "Não existe uma relação válida para a Pessoa Física solicitada.";
		mensagem("err_sql_titulo","err_sql_desc",$strErro,"../modulo_PainelAdmin/STindex.php","aviso",1);
		die();
	}
	
	//Verifica a existencia de pedidos abertos de carteirinhas para a PJ corrente
	//Busca usando como parâmetro o cod_pedido tbm enviado para esta pag.
	try{
		$strSQL = "SELECT
							prd_pedido.cod_pedido,
							prd_pedido.cod_pj,
							prd_pedido.it_cod_pf,
							prd_pedido.valor,
							prd_pedido.obs,
							prd_produto.rotulo,
							cad_pf.nome,
							cad_pf.cpf,
							cad_pf.email,
							relac_pj_pf.funcao,
							cad_pj.razao_social,
							cad_pj.cnpj
						FROM
							prd_pedido,
							cad_pj,
							cad_pf,
							prd_produto,
							relac_pj_pf
						WHERE
							prd_pedido.cod_pj = cad_pj.cod_pj
						AND
							prd_pedido.it_cod_pf = '" . $intCodDado . "'
						AND
							prd_pedido.cod_pedido = '" . $intCodPedido . "'
						AND
							prd_produto.cod_produto = prd_pedido.it_cod_produto
						AND
							prd_pedido.it_cod_pf = cad_pf.cod_pf
						AND
							prd_produto.tipo ILIKE '%card%'
						AND
							prd_pedido.situacao ILIKE '%aberto%'
						AND
							relac_pj_pf.cod_pf = '" . $intCodDado . "'";
		$objResult = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc","","../modulo_PainelAdmin/STindex.php","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	$intNumPedido = (getValue($objRS,"cod_pedido") == "") ? "" : getValue($objRS,"cod_pedido");
	$intCodPJ = (getValue($objRS,"cod_pj") == "") ? "" : getValue($objRS,"cod_pj");
	$strRazaoSocial = (getValue($objRS,"razao_social") == "") ? "" : getValue($objRS,"razao_social");
	$intCnpj = (getValue($objRS,"cnpj") == "") ? "" : getValue($objRS,"cnpj");
	$intCodPF = (getValue($objRS,"it_cod_pf") == "") ? "" : getValue($objRS,"it_cod_pf");
	$strNome = (getValue($objRS,"nome") == "") ? "" : getValue($objRS,"nome");
	$strFuncao = (getValue($objRS,"funcao") == "") ? "" : getValue($objRS,"funcao");
	$intCpf = (getValue($objRS,"cpf") == "") ? "" : getValue($objRS,"cpf");
	$strEmail = (getValue($objRS,"email") == "") ? "" : getValue($objRS,"email");
	$intValorPed = (getValue($objRS,"valor") == "") ? "" : getValue($objRS,"valor");
	$strRotuloProd = (getValue($objRS,"rotulo") == "") ? "" : getValue($objRS,"rotulo");
	$strObsPed = (getValue($objRS,"obs") == "") ? "Nenhuma obs." : getValue($objRS,"obs");
	
	//Busca número de impressoes da Carteirinha
	try{
		$strSQL = "SELECT 
							sd_credencial.qtde_impresso
						FROM
							sd_credencial
						WHERE
							sd_credencial.cod_pf = '" . $intCodDado . "'";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	$intQtdeImp = (getValue($objRS,"qtde_impresso") == "") ? "Nenhuma impressão" : getValue($objRS,"qtde_impresso");
}

//Neste segundo caso, recebemos o cod_pf porém
//sem pedido ainda feito ou já feito. Os possíveis
//pedidos de carteirinha serão buscados e questio-
//nados quanto a cobrança que deverá ser feita.
if(($intCodDado != "") && ($intCodPedido == "")){
	//Verifica se existe uma relação válida para a PF
	try{
		$strSQL = "SELECT 
							relac_pj_pf.cod_pj,
							relac_pj_pf.funcao,
							relac_pj_pf.departamento,
							relac_pj_pf.dt_admissao
						FROM
							relac_pj_pf
						WHERE
							relac_pj_pf.cod_pf = '" . $intCodDado . "'";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		$strErro = "Não existe uma relação válida para a Pessoa Física solicitada.";
		mensagem("err_sql_titulo","err_sql_desc",$strErro,"../modulo_PainelAdmin/STindex.php","aviso",1);
		die();
	}
	$objRS = $objResult->fetch();
	$intCodPJ = (getValue($objRS,"cod_pj") == "") ? 0 : getValue($objRS,"cod_pj");
	$strFuncao = (getValue($objRS,"funcao") == "") ? "" : getValue($objRS,"funcao");
	$strDepartamento = (getValue($objRS,"departamento") == "") ? "" : getValue($objRS,"departamento");
	$strDataAdm = (getValue($objRS,"dt_admissao") == "") ? "" : getValue($objRS,"dt_admissao");

	//Busca número de impressoes da Carteirinha
	try{
		$strSQL = "SELECT 
							sd_credencial.qtde_impresso
						FROM
							sd_credencial
						WHERE
							sd_credencial.cod_pf = '" . $intCodDado . "'";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	$intQtdeImp = (getValue($objRS,"qtde_impresso") == "") ? "Nenhuma impressão" : getValue($objRS,"qtde_impresso");
	
	//Verificando existência de pedidos abertos e de carteirinha para a PF selecionada
	//Caso exista algum pedido em aberto, será mostrado em tela para confirmação de
	//cobrança (geração de título)
	try{ 
		$strSQL = "SELECT
							prd_pedido.cod_pedido,
							prd_pedido.cod_pj,
							prd_pedido.it_cod_pf,
							prd_pedido.valor,
							prd_pedido.obs,
							prd_produto.rotulo,
							cad_pj.razao_social,
							prd_pedido.sys_dtt_ins,
						FROM
							prd_pedido, prd_produto, cad_pj
						WHERE
							prd_pedido.cod_pj = '" . $intCodPJ . "'
						AND
							prd_pedido.it_cod_pf = '" . $intCodDado . "'
						AND prd_produto.cod_produto = prd_pedido.it_cod_produto 
						AND prd_pedido.situacao ILIKE '%aberto%'
						AND prd_produto.tipo ILIKE '%card%' ORDER BY sys_dtt_ins DESC";
	$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	$intNumPedido = (getValue($objRS,"cod_pedido") == "") ? 0 : getValue($objRS,"cod_pedido");
	$intNumPedido = (getValue($objRS,"cod_pedido") == "") ? 0 : getValue($objRS,"cod_pedido");
}	
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
				strLocation = "STgeracarteirinhaexec.php";
				submeterForm();
			}

			function cancelar() {
				location.href="../modulo_PainelAdmin/STindex.php";
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<?php 
if(($intCodDado != "") && ($intCodPedido != "")) { ?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
		<tr>
   			<td align="center" valign="top">
			<?php athBeginFloatingBox("600","none","(GERAR CREDENCIAL) - Pessoa Física",CL_CORBAR_GLASS_1); ?>
    			<table id="dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    			<form name="formstatic" action="STgeracarteirinhaexec.php" method="post">
					<input type="hidden" name="var_action" value="">
						<tr>
							<td height="12" style="padding:10px 0px 10px 10px;">
								<strong>
									<?php echo(getTText("confirmacao_emitir",C_NONE)); ?>
								</strong>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td height="12" style="padding:20px 50px 10px 50px;" align="center" valign="top">
								<table cellpadding="6" cellspacing="0" border="0" width="100%">
									<tr>
										<td align="right" bgcolor="<?php echo(CL_CORLINHA_1)?>">
											<strong>
												<?php echo(getTText("qtde_impresso",C_NONE)); ?>
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intQtdeImp); ?></td>
									</tr>
									<tr>
										<td align="right" bgcolor="<?php echo(CL_CORLINHA_2)?>">
											<strong>
												<?php echo(getTText("cod_pj",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCodPJ); ?></td>
									</tr>
									<tr>
										<td align="right"  bgcolor="<?php echo(CL_CORLINHA_1)?>">
											<strong>
												<?php echo(getTText("razao_social",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strRazaoSocial); ?></td>
									</tr>
									<tr>
										<td align="right" bgcolor="<?php echo(CL_CORLINHA_2)?>">
											<strong>
												<?php echo(getTText("cnpj",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCnpj); ?></td>
									</tr>
									<tr>
										<td align="right" bgcolor="<?php echo(CL_CORLINHA_1)?>">
											<strong>
												<?php echo(getTText("cod_pf",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCodPF); ?></td>
									</tr>
									<tr>
										<td align="right" bgcolor="<?php echo(CL_CORLINHA_2)?>">
											<strong>
												<?php echo(getTText("nome",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strNome); ?>	</td>
									</tr>
									<tr>
										<td align="right" bgcolor="<?php echo(CL_CORLINHA_1)?>">
											<strong>
												<?php echo(getTText("cpf",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCpf); ?></td>
									</tr>
									<tr>
										<td align="right" bgcolor="<?php echo(CL_CORLINHA_2)?>">
											<strong>
												<?php echo(getTText("email",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strEmail); ?></td>
									</tr>
									<tr>
										<td align="right" bgcolor="<?php echo(CL_CORLINHA_1)?>">
											<strong>
												<?php echo(getTText("funcao",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strFuncao); ?></td>
									</tr>
								</table>
								<tr>
									<td style="padding: 0px 0px 20px 80px;">
										<table cellpadding="0" cellspacing="0" border="0">
											<tr>
												<td>
													<input type="radio" name="var_cobr" id="var_cobr" value="cobr_normal">
												</td>
												<td nowrap="nowrap">
													<?php echo(getTText("gera_cobranca_normal",C_NONE))?>
												</td>
											</tr>
											<tr>
												<td>
													<input type="radio" name="var_cobr" id="var_cobr" value="cobr_novo">
												</td>
												<td nowrap="nowrap">
													<?php echo(getTText("gera_cobranca_pedido",C_NONE)." ". $intNumPedido . " ".getTText("gera_cobranca_valor",C_NONE))?>
												</td>
												<td>
													<input type="text" name="var_valor_novo" id="var_valor_novo" size="15" onKeyPress="Javascript: return validateNumKey(event);">
												</td>
											</tr>
											<tr>
												<td>
													<input type="radio" name="var_cobr" id="var_cobr" value="cobr_impr">
												</td>
												<td nowrap="nowrap">
													<?php echo(getTText("gera_cobranca_impr",C_NONE))?>
												</td>
											</tr>
											<tr>
												<td height="1" colspan="2" bgcolor="#DBDBDB">
												</td>
											</tr>
										</table>
									</td>
								</tr>	
								<table cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
									<tr>
										<td align="right" style="padding: 0px 0px 0px 70px;">
											<img src="../img/mensagem_info.gif">
										</td>
										<td align="left" style="padding: 0px 0px 0px 10px;">
											<?php echo(getTText("aviso_cobr",C_NONE))?>
										</td>
										<td width="1%" align="left" style="padding:10px 50px 10px 10px;" nowrap>
											<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?>											</button>
											<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?>
											</button>
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
}
?>