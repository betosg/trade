<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");

$strOperacao  = request("var_oper");       // Opera��o a ser realizada
$intCodDado   = request("var_chavereg");   // C�digo chave da p�gina
//$intCodPedido   = request("var_cod_pedido");   // C�digo do pedido, caso exista
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou n�o
$strAcao   	  = request("var_acao");      // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade � exibida normalmente.

//$intCodDado = 45455;

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos �tens do m�dulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA");

//Cores linhas
$strBGColor = CL_CORLINHA_2;

//Inicia objeto para manipula��o do banco
$objConn = abreDBConn(CFG_DB);

// S� � feito a busca e exibi��o dos dados caso o n�mero do pedido
// seja enviado como parametro para este script.
if($intCodDado == ""){
	$strErro = "Erro em busca dos dados. Tente novamente.";
	mensagem("err_sql_titulo","err_sql_desc_card",$strErro,"../modulo_PainelAdmin/STindex.php","aviso",1);
	die();
}
else {
	// Verifica a existencia de pedidos abertos de carteirinhas para 
	// a PJ corrente - Busca usando como par�metro o cod_pedido 
	// tbm enviado para esta pag. - uma das situa��es
	try{
		$strSQL = "SELECT
							prd_pedido.cod_pedido,
							prd_pedido.valor,
							prd_pedido.obs,
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
	}
	else {
		$strErro = "N�o foi poss�vel processar os dados.";
		mensagem("err_sql_titulo","err_sql_desc",$strErro,"../modulo_PainelAdmin/STindex.php","erro",1);
		die();
	}
	
	//busca dados da PF - Atrav�s do cod_pf relacionado (it_cod_pf)
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
							cad_pf.cod_pf = '" . $intCodPF . "'
						AND
							relac_pj_pf.cod_pf = '" . $intCodPF . "'	
						AND
							relac_pj_pf.cod_pj = cad_pj.cod_pj";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc","","../modulo_PainelAdmin/STindex.php","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	//fetch da consulta para exibi��o dos dados na tabela ao final
	$intQtdeImp =  (getValue($objRS,"qtde_impresso") == "") ? "Nenhuma impress�o"  : getValue($objRS,"qtde_impresso") ;
	$intCodPJ = (getValue($objRS,"cod_pj") == "") ? "" : getValue($objRS,"cod_pj");
	$strRazaoSocial = (getValue($objRS,"razao_social") == "") ? "" : getValue($objRS,"razao_social");
	$intCnpj = (getValue($objRS,"cnpj") == "") ? "" : getValue($objRS,"cnpj");
	$intCodPF = (getValue($objRS,"cod_pf") == "") ? "" : getValue($objRS,"cod_pf");
	$strNome = (getValue($objRS,"nome") == "") ? "" : getValue($objRS,"nome");
	$strFuncao = (getValue($objRS,"funcao") == "") ? "" : getValue($objRS,"funcao");
	$intCpf = (getValue($objRS,"cpf") == "") ? "" : getValue($objRS,"cpf");
	$strEmail = (getValue($objRS,"email") == "") ? "" : getValue($objRS,"email");
}
/*else {
	//verifica a exist�ncia do �ltimo pedido aberto de card
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
	//verifica��o de quantidade de linhas retornadas.
	// 0 - nenhum pedido encontrado / 1 - mais de um pedido encontrado, ultimo ser� exibido.
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
}*/


?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
		<!--
			//****** Fun��es de a��o dos bot�es - In�cio ******
			var strLocation = null;
			function ok() {
				document.formstatic.submit();
			}

			function cancelar() {
				location.href="../modulo_PainelAdmin/STindex.php";
			}
			//****** Fun��es de a��o dos bot�es - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
	<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
		<tr>
   			<td align="center" valign="top">
			<?php athBeginFloatingBox("600","none","(GERAR CREDENCIAL) - Pessoa F�sica",CL_CORBAR_GLASS_1); ?>
    			<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	    			<form name="formstatic" action="STgeracarteirinhaexec.php" method="post">
					<input type="hidden" name="var_action" value="">
					<input type="hidden" name="var_chavereg" value="<?php echo($intCodPedido);?>">
					<input type="hidden" name="var_cod_pf" value="<?php echo($intCodPF);?>">
					<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ);?>">
					<input type="hidden" name="var_desc_prod" value="<?php echo($strDescProd);?>">
						<tr>
							<td height="12" style="padding:20px 0px 0px 20px;">
								<strong>
									<?php echo(getTText("confirmacao_emitir",C_NONE)); ?>
								</strong>
							</td>
						</tr>
						<tr>
							<td align="center" valign="top" style="padding:20px 50px 10px 50px;" width="1%">
								<table cellpadding="4" cellspacing="0" border="0" width="100%">
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right" width="35%"> 
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
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("conta",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;
										<select name="">
											<option value="">2345435234</option>
											<option value="">2345435234</option>
											<option value="">2345435234</option>
											<option value="">2345435234</option>
										</select>
										</td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("plano de conta",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;
										<select name="">
											<option value="">2345435234</option>
											<option value="">2345435234</option>
											<option value="">2345435234</option>
											<option value="">2345435234</option>
										</select>
										</td>
									</tr>
									<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
										<td align="right">
											<strong>
												<?php echo(getTText("centro de custo",C_NONE)); ?>:
											</strong>
										</td>
										<td>&nbsp;
										<select name="">
											<option value="">2345435234</option>
											<option value="">2345435234</option>
											<option value="">2345435234</option>
											<option value="">2345435234</option>
										</select>
										</td>
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
													<?php echo(getTText("gera_cobranca_pedido",C_NONE)." <strong>". $intCodPedido . "</strong> ".getTText("gera_cobranca_valor",C_NONE))?>
												</td>
												<td>
													<input type="text" name="var_valor_novo" id="var_valor_novo" dir="rtl" size="15" onKeyPress="Javascript: return validateNumKey(event);">
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