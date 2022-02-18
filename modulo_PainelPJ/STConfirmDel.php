<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intCodPF = request("var_chavereg"); // Código do PF
$intCodPJ = request("cod_emp"); // Código do PF
if(($intCodPF == "")or ($intCodPJ == "")){
	$strErro = "Código do Colaborador ou Empresa inválido.";
	mensagem("err_sql_titulo","err_sql_desc_card",$strErro,"","aviso",1);
	die();
}

$objConn = abreDBConn(CFG_DB);

if(@$strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));






// Verifica a existencia de pedidos abertos de carteirinhas para 
// a PJ corrente - Busca usando como parâmetro o cod_pedido 
// tbm enviado para esta pag. - uma das situações
try{
	$strSQL = "
			SELECT	
				pf.cod_pf
			,	pf.nome
			,	pf.apelido
			,	pf.nome_pai
			,	pf.nome_mae
			,	pf.cpf
			,	pf.rg
			,	pf.ctps
			
			,	pj.cod_pj
			,	pj.razao_social
			,	pj.nome_fantasia
			,	pj.cnpj
			,	pj.endprin_cidade
			,	pj.endprin_estado
			
			,	relac.tipo
			,	relac.dt_admissao
			,	relac.funcao
			FROM relac_pj_pf relac, cad_pf pf, cad_pj pj
			WHERE relac.cod_pf = " .$intCodPF. "
			AND relac.cod_pj = pj.cod_pj
			AND relac.cod_pf = pf.cod_pf
			AND pj.cod_pj = ".$intCodPJ;
	
	$objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

if ($objResult->rowCount() == 0){
	$msg = "Por Favor verifique a sua lista de colaboradores";
	mensagem("info_busca_dados","info_colab_nao_encontrado_desc",$msg,"javascript:window.parent.reload(); window.close();","info",1);
	//mensagem("info_busca_dados","info_colab_nao_encontrado_desc",$msg,
	//			"../modulo_PainelPJ/STColabAtivos.php","info",1);
	die();
}

//fetch dos dados
$objRS = $objResult->fetch();

$intCodPF = (getValue($objRS,"cod_pf") == "") ? "" : getValue($objRS,"cod_pf");
$strNome = (getValue($objRS,"nome") == "") ? "" : getValue($objRS,"nome");
$strApelido = (getValue($objRS,"apelido") == "") ? "" : getValue($objRS,"apelido");
$strNomePai = (getValue($objRS,"nome_pai") == "") ? "" : getValue($objRS,"nome_pai");
$strNomeMae = (getValue($objRS,"nome_mae") == "") ? "" : getValue($objRS,"nome_mae");
$strRG = (getValue($objRS,"rg") == "") ? "" : getValue($objRS,"rg");
$strCPF = (getValue($objRS,"cpf") == "") ? "" : getValue($objRS,"cpf");
$strCTPS = (getValue($objRS,"ctps") == "") ? "" : getValue($objRS,"ctps");

$intCodPJ = (getValue($objRS,"cod_pj") == "") ? "" : getValue($objRS,"cod_pj");
$strRazaoSocial = (getValue($objRS,"razao_social") == "") ? "" : getValue($objRS,"razao_social");
$strNomeFantasia = (getValue($objRS,"nome_fantasia") == "") ? "" : getValue($objRS,"nome_fantasia");
$strCNPJ = (getValue($objRS,"cnpj") == "") ? "" : getValue($objRS,"cnpj");
$strCidade = (getValue($objRS,"endprin_cidade") == "") ? "" : getValue($objRS,"endprin_cidade");
$strEstado = (getValue($objRS,"endprin_estado") == "") ? "" : getValue($objRS,"endprin_estado");

$strFuncao = (getValue($objRS,"funcao") == "") ? "" : getValue($objRS,"funcao");
$strTipo = (getValue($objRS,"tipo") == "") ? "" : getValue($objRS,"tipo");
$dtAdmissao = (getValue($objRS,"dt_admissao") == "") ? "" : dDate(CFG_LANG,getValue($objRS,"dt_admissao"),false);

?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
		<!--
			window.onload = function(){
				window.resizeTo(680,800);
			}
			
			//****** Funções de ação dos botões - Início ******
			var strLocation = null;
			function ok() {
				document.formstatic.submit();
			}

			function cancelar() {
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
	<?php athBeginFloatingBox("600","none",getTText("gera_exclusao",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	<form name="formstatic" action="../modulo_CadPF/STDelpf.php" method="post">
		<input type="hidden" name="var_chavereg" value="<?php echo($intCodPF); ?>">
		<tr>
			<td height="12" style="padding:20px 0px 0px 20px;">
				<strong><?php echo(getTText("confirmacao_homo",C_NONE)); ?></strong>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" style="padding:20px 50px 10px 50px;" width="1%">
				<table cellpadding="4" cellspacing="0" border="0" width="100%">
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS DA EMPRESA</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("razao_social",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strRazaoSocial); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right" width="35%"> 
							<strong><?php echo(getTText("nome_fantasia",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNomeFantasia); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("cnpj",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCNPJ); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("cidade",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCidade); ?> / <?php echo($strEstado); ?></td>
					</tr>
					<tr>
						<td></td>
						<td align="left" valign="top" class="destaque_gde"><strong>DADOS DO COLABORADOR</strong></td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("nome",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNome); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("nome_pai",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNomePai); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("nome_mae",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strNomeMae); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("rg",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strRG); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("cpf",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCPF); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("ctps",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strCTPS); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("tipo",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strTipo); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("funcao",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($strFuncao); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("dt_admissao",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo($dtAdmissao); ?></td>
					</tr>
					<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
					<tr>
						<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
								<tr>
									<td align="right" width="1%" style="padding: 0px 0px 0px 0px;"><img src="../img/mensagem_aviso.gif"></td>
									<td align="left" width="98%" style="padding: 0px 0px 0px 10px;"><?php echo(getTText("aviso_exclusao",C_NONE))?></td>
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
</html>
<?php 
	$objConn = NULL; 
	//"<a href='?var_chavereg=".getValue($objRS,"cod_pf")."'>
?>