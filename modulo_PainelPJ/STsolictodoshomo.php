<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$chave 			= request("chave");
$intCodDado 	= request("var_chavereg"); // cod pf
$intCodPJ 		= request("var_cod_pj");
$strOperacao 	= request("var_oper"); // Operação a ser realizada
$strExec 		= request("var_exec"); // Executor externo (fora do kernel)
$strPopulate 	= request("var_populate"); // Flag para necessidade de popular o session ou não
$strAcao 		= request("var_acao"); // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "HOMO");


//Inicia objeto para manipulação do banco
$objConn = abreDBConn(CFG_DB);

// busca o produto do tipo card de maior valor
// e validade corrente para exibicao dos dados
// em tela - confirmação para a PJ logada
try{
	$strSQL = "
			SELECT
				rotulo,
				descricao, 
				cod_produto, 
				valor
    		FROM 
				prd_produto
		    WHERE tipo = 'homo'
		    AND dtt_inativo IS NULL
    		AND CURRENT_DATE BETWEEN dt_ini_val_produto AND dt_fim_val_produto
		    ORDER BY valor";
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
	$strSQL2 = "
			SELECT 	t2.cod_pf, t2.nome, t2.cpf, t1.cod_pj, t1.cnpj,t1.razao_social,
					count(t5.cod_credencial) AS qtde_credencial,
					count(t6.cod_pedido)     AS qtde_ped_homo,
        			(CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' AS mais_de_uma_hora 
			FROM cad_pj t1 
			INNER JOIN relac_pj_pf t3 ON (t1.cod_pj = t3.cod_pj AND t3.dt_demissao IS NULL) 
			INNER JOIN cad_pf t2 ON (t2.cod_pf = t3.cod_pf) 
			LEFT OUTER JOIN cad_cargo t4 ON (t3.cod_cargo = t4.cod_cargo) 
			LEFT OUTER JOIN sd_credencial t5 ON (t5.dtt_inativo is NULL AND t5.cod_pf = t2.cod_pf 
									 AND CURRENT_DATE <= dt_validade) 
			LEFT OUTER JOIN prd_pedido t6 ON (t6.situacao <> 'cancelado' AND t6.it_tipo = 'homo' 
										AND t6.it_cod_pf = t2.cod_pf AND t6.cod_pj = t3.cod_pj
										AND t6.it_cod_pf = t3.cod_pf AND t3.dt_demissao IS NULL
										AND t6.dtt_inativo IS NULL) 
			WHERE t1.cod_pj =".$intCodPJ." 
			GROUP BY t1.cod_pj, t2.cod_pf, t2.nome, t2.cpf, t1.cod_pj, t1.cnpj, t1.razao_social,
					(CURRENT_TIMESTAMP - t3.sys_dtt_ins) > '1 hour' 
			ORDER BY t2.cod_pf";
	$objResult2 = $objConn->query($strSQL2);	
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
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
				location.href="STColabAtivos.php";
				//window.history.back(-1);
			}
			
			//****** Funções de ação dos botões - Fim ******
		//-->
		</script>
	</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:0px solid #A6A6A6;">
<tr>
	<td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText("solic_homo",C_NONE),CL_CORBAR_GLASS_1); ?>
	<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	<form name="formstatic" action="STsolictodoshomoexec.php" method="post">
		<input type="hidden" name="var_cod_produto" value="<?php echo($intCodProduto);?>" />
		<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ);?>" />
		<input type="hidden" name="var_opcao" value="uma_empresa" />
		<tr>
			<td height="12" style="padding:20px 0px 0px 20px;">
				<strong><?php echo(getTText("solicitacao_homo",C_NONE)); ?></strong>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" style="padding:20px 50px 10px 50px;" width="1%">
				<table cellpadding="4" cellspacing="0" border="0" width="100%">
					<tr>
						<td></td>
						<td align="left" valign="top" colspan="2" class="destaque_gde">
							<strong>DADOS DOS COLABORADORES</strong></td>
					</tr>
					<tr><td colspan="3" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="3" height="2" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
					     <td align="left" width="18%" >
						 	<strong><?php echo(getTText("cod_pf",C_NONE)); ?></strong></td>
						 <td align="left" width="22%" > 
							<strong><?php echo(getTText("cpf",C_NONE)); ?></strong></td>
						 <td align="left" width="60%"> 
							<strong><?php echo(getTText("nome",C_NONE)); ?></strong></td>
					</tr>
  					<?php $cont = 0; foreach ($objResult2 as $objRS){ 	
										if((getValue($objRS,"qtde_ped_homo") < 1)){
					?>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="left">&nbsp;<?php echo  getValue($objRS,"cod_pf"); ?></td>
						<td align="left">&nbsp;<?php echo  getValue($objRS,"cpf"); ?></td>
						<td align="left">&nbsp;<?php echo  getValue($objRS,"nome"); ?></td>
					</tr>
					<?php $cont++; }  } ?>
					<tr><td>&nbsp;</td></tr>
					</table>
					<table cellpadding="4" cellspacing="0" border="0" width="100%"> 
					<tr>
						<td></td>
						
						<td align="left" valign="top" class="destaque_gde">
							<strong>DADOS PESSOA JURÍDICA</strong>
						</td>
					</tr>
					<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
					<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("cod_pj",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo(getValue($objRS,"cod_pj")); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("razao_social",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo(getValue($objRS,"razao_social")); ?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("cnpj",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo(getValue($objRS,"cnpj")); ?></td>
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
							<strong><?php echo(getTText("produto_rotulo",C_NONE)); ?>:</strong>
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
							<strong><?php echo(getTText("valor_unit",C_NONE)); ?>:</strong>
						</td>
						<td>&nbsp;<?php echo (number_format((double) ($dblValorProduto),2,",","."));?></td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
						<td align="right">
							<strong><?php echo(getTText("valor_tot",C_NONE)); ?>:</strong>
						</td>
						<td>
						<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td>
							&nbsp;<?php echo(number_format((double) ($cont * $dblValorProduto),2,",",".")); ?>
							</td>
							<td>
							<span class="comment_peq">&nbsp;&nbsp;<?php echo(getTText("obs_valor_homo",C_NONE));?></span>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
						<td align="right">
							<strong><?php echo(getTText("obs",C_NONE)); ?>:</strong>
						</td>
						<td>
							<textarea id="var_obs" name="var_obs" rows="5" cols="55"></textarea><br/>
							<span class="comment_peq"><?php echo(getTText("obs_obs_homo",C_NONE))?></span>
						</td>
					</tr>				
					<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td style="padding:10px 0px 10px 10px;" align="right" colspan="2">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;">
								<tr>
									<td align="right" width="1%" style="padding: 0px 0px 0px 0px;"><img src="../img/mensagem_info.gif"></td>
									<td align="left" width="98%" style="padding: 0px 0px 0px 10px;"><?php echo(getTText("aviso_solic_homo",C_NONE))?></td>
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
?>