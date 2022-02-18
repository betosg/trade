<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página
$intCodPedido   = request("var_cod_pedido");   // Código do pedido, caso exista
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");      // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

//$intCodDado = 45455;

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA");

//Cores linhas
$strBGColor = CL_CORLINHA_2;

//Inicia objeto para manipulação do banco
$objConn = abreDBConn(CFG_DB);

//Idependente se cod_pedido for enviado ou não para no-
//sso script, podemos buscar os dados da table superior 
//somente com o cod_pf. Este é obrigatório.
if($intCodDado == ""){
	$strErro = "Erro em busca dos dados. Tente novamente.";
	mensagem("err_sql_titulo","err_sql_desc_card",$strErro,"../modulo_PainelAdmin/STindex.php","aviso",1);
	die();
}
else {
	//Verifica se existe uma relação válida para a PF
	try{
		$strSQL = "
						SELECT 
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
	//busca dados da PF - Certeza de que a PF é relacionada.
	try{
		$strSQL = "
						SELECT
							cad_pf.cod_pf,
							cad_pf.cpf,
							cad_pf.nome,
							cad_pf.email,
							cad_pj.cod_pj,
							cad_pj.razao_social,
							cad_pj.cnpj,
							relac_pj_pf.funcao,
							sd_credencial.qtde_impresso
						FROM
							cad_pf, cad_pj, relac_pj_pf
						LEFT JOIN 
							sd_credencial ON sd_credencial.cod_pf = relac_pj_pf.cod_pf
						WHERE
							cad_pf.cod_pf = '" . $intCodDado . "'
						AND
							relac_pj_pf.cod_pf = '" . $intCodDado . "'	
						AND
							relac_pj_pf.cod_pj = cad_pj.cod_pj";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc","","../modulo_PainelAdmin/STindex.php","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	//fetch da consulta para exibição dos dados na tabela ao final
	$intQtdeImp =  (getValue($objRS,"qtde_impresso") == "") ? "Nenhuma impressão"  : getValue($objRS,"qtde_impresso") ;
	$intNumPedido = (getValue($objRS,"cod_pedido") == "") ? "" : getValue($objRS,"cod_pedido");
	$intCodPJ = (getValue($objRS,"cod_pj") == "") ? "" : getValue($objRS,"cod_pj");
	$strRazaoSocial = (getValue($objRS,"razao_social") == "") ? "" : getValue($objRS,"razao_social");
	$intCnpj = (getValue($objRS,"cnpj") == "") ? "" : getValue($objRS,"cnpj");
	$intCodPF = (getValue($objRS,"cod_pf") == "") ? "" : getValue($objRS,"cod_pf");
	$strNome = (getValue($objRS,"nome") == "") ? "" : getValue($objRS,"nome");
	$strFuncao = (getValue($objRS,"funcao") == "") ? "" : getValue($objRS,"funcao");
	$intCpf = (getValue($objRS,"cpf") == "") ? "" : getValue($objRS,"cpf");
	$strEmail = (getValue($objRS,"email") == "") ? "" : getValue($objRS,"email");
}

// até o momento, dados PF ok - Busca de pedidos: caso
// o numero do ped seja enviado a este script, buscar di-
// retamente por ele, caso contrário, buscar possíveis pe-
// didos abertos e de carteirinha. Isto vai afetar as ações
// finais dos radios
if($intCodPedido != ""){
	// Verifica a existencia de pedidos abertos de carteirinhas para 
	// a PJ corrente - Busca usando como parâmetro o cod_pedido 
	// tbm enviado para esta pag. - uma das situações
	try{
		$strSQL = "SELECT
							prd_pedido.cod_pedido,
							prd_pedido.valor,
							prd_pedido.obs,
							prd_produto.rotulo,
							prd_produto.descricao
						FROM
							prd_pedido,
							prd_produto
						WHERE
							prd_pedido.cod_pedido = '" . $intCodPedido . "'
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
	$objRS = $objResult->fetch();
	// fetch dos dados do pedido
	$intNumPedido = (getValue($objRS,"cod_pedido") == "") ? "" : getValue($objRS,"cod_pedido");
	$intValorPed = (getValue($objRS,"valor") == "") ? "" : getValue($objRS,"valor");
	$strObsPed = (getValue($objRS,"obs") == "") ? "" : getValue($objRS,"obs");
	$strRotuloProd = (getValue($objRS,"rotulo") == "") ? "" : getValue($objRS,"rotulo");
	$strDescProd = (getValue($objRS,"descricao") == "") ? "" : getValue($objRS,"descricao");
}
else {
	//verifica a existência do último pedido aberto de card
	//com base no cod de pf que foi enviado a pagina
	try{
		$strSQL = "
						SELECT 
							prd_pedido.cod_pedido,
							prd_pedido.valor,
							prd_pedido.obs,
							prd_produto.rotulo,
							prd_produto.descricao
						FROM
							prd_pedido, prd_produto
						WHERE
							prd_pedido.it_cod_pf = '" . $intCodDado . "'
						AND
							prd_produto.cod_produto = prd_pedido.it_cod_produto 
						AND
							prd_pedido.situacao ILIKE '%aberto%'
						AND
							prd_produto.tipo ILIKE '%card%'
						ORDER BY prd_pedido.sys_dtt_ins DESC";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc","","../modulo_PainelAdmin/STindex.php","erro",1);
		die();
	}
	//verificação de quantidade de linhas retornadas.
	// 0 - nenhum pedido encontrado / 1 - mais de um pedido encontrado, ultimo será exibido.
	$intQtdePed = (($objResult->rowCount) > 0) ? 1 : 0;
	if(($objResult->rowCount()) > 0) {
		//fetch dos dados do pedido encontrado
		$objRS = $objResult->fetch();
		$intNumPedido = (getValue($objRS,"cod_pedido") == "") ? "" : getValue($objRS,"cod_pedido");
		$intValorPed = (getValue($objRS,"valor") == "") ? "" : getValue($objRS,"valor");
		$strObsPed = (getValue($objRS,"obs") == "") ? "" : getValue($objRS,"obs");
		$strRotuloProd = (getValue($objRS,"rotulo") == "") ? "" : getValue($objRS,"rotulo");
		$strDescProd = (getValue($objRS,"descricao") == "") ? "" : getValue($objRS,"descricao");
	}
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
				document.formstatic.submit();
			}

			function cancelar() {
				location.href="../modulo_PainelAdmin/STindex.php";
			}
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:0px solid #A6A6A6;">
		<tr>
   			<td align="center" valign="top">
			<?php athBeginFloatingBox("600","none","(GERAR CREDENCIAL) - Pessoa Física",CL_CORBAR_GLASS_1); ?>
    			<table id="dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    			<form name="formstatic" action="STgeracarteirinhaexec.php" method="post">
					<input type="hidden" name="var_action" value="">
					<input type="hidden" name="var_chavereg" value="<?php echo($intCodPF);?>">
					<input type="hidden" name="var_cod_pedido" value="<?php echo($intNumPedido);?>">
						<tr>
							<td height="12" style="padding:20px 0px 0px 20px;">
								<strong>
									<?php echo(getTText("confirmacao_emitir",C_NONE)); ?>
								</strong>
							</td>
						</tr>
						<tr>
							<td align="center" valign="top" style="padding:20px 50px 10px 50px;" width="1%">
								<table cellpadding="6" cellspacing="0" border="0" width="100%">
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right"> 
											<strong>
												<?php echo(getTText("qtde_impresso",C_NONE)); ?>
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intQtdeImp); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("cod_pj",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCodPJ); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("razao_social",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strRazaoSocial); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("cnpj",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCnpj); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("cod_pf",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCodPF); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("nome",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strNome); ?>	</td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("cpf",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($intCpf); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
										<td align="right"> 
											<strong>
												<?php echo(getTText("email",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strEmail); ?></td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("funcao",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;<?php echo ($strFuncao); ?></td>
									</tr>
									<tr>
										<td height="1" colspan="2" bgcolor="#DBDBDB">
										</td>
									</tr>
								</table>
								<?php if(($intCodDado != "") && ($intCodPedido != "")) { ?>
								<tr>
									<td style="padding: 0px 0px 20px 80px;">
										<table cellpadding="0" cellspacing="0" border="0">
											<tr>
												<td>
													<input type="radio" name="var_cobr" id="var_cobr" value="cobr_normal" style="border:none;">
												</td>
												<td nowrap="nowrap">
													<?php echo(getTText("gera_cobranca_normal",C_NONE))?>
												</td>
											</tr>
											<tr>
												<td>
													<input type="radio" name="var_cobr" id="var_cobr" value="cobr_novo" style="border:none;">
												</td>
												<td nowrap="nowrap">
													<?php echo(getTText("gera_cobranca_pedido",C_NONE)." <strong>". $intNumPedido . "</strong> ".getTText("gera_cobranca_valor",C_NONE))?>
												</td>
												<td>
													<input type="text" name="var_valor_novo" id="var_valor_novo" size="15" onkeypress="Javascript: return validateNumKey(event);">
												</td>
											</tr>
											<tr>
												<td>
													<input type="radio" name="var_cobr" id="var_cobr" value="cobr_impr" style="border:none;">
												</td>
												<td nowrap="nowrap">
													<?php echo(getTText("gera_cobranca_impr",C_NONE))?>
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
											<?php echo(getTText("aviso_cobr",C_NONE))?>
										</td>
										<?php }?>
										<td width="1%" align="left" style="padding:10px 50px 10px 10px;" nowrap>
											<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?>											</button>
											<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?>
											</button>
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