<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");       // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

// Popula o session para fazer a abertura dos ítens do módulo
if($strPopulate == "yes"){ initModuloParams(basename(getcwd())); }

// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA");
$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

//Cores linhas
$strColor = CL_CORLINHA_2;

// função para cores de linhas
function getLineColor(&$prColor){
	$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
	echo($prColor);
}

//Inicia objeto para manipulação do banco
$objConn  = abreDBConn(CFG_DB);

$nameArq  = $_POST['uploadArquivo'];

$intCodConta       = getVarEntidade($objConn, "fin_cod_conta_default");
$intCodPlanoConta  = getVarEntidade($objConn, "import_cod_plano_conta_padrao");
$intCodCentroCusto = getVarEntidade($objConn, "import_cod_centro_custo_padrao");
$intCodCentroCusto = getVarEntidade($objConn, "import_cod_job_padrao");
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
	//****** Funções de ação dos botões - Início ******
	var strLocation = null;
	function ok() {
		document.formImpor.submit();
	}
	function cancelar() {
		location.href="../modulo_PainelAdmin/STindex.php";
	}
	//****** Funções de ação dos botões - Fim ******
</script>
<style type="text/css">
	.borda{
		border:none;
		background:none;
	}
</style>
</head>
<body style="margin:20px 20px 10px 20px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
	<table width="100%" border="0" cellpadding="0" cellspacing="1" style="border:0px solid #A6A6A6;">
  		<tr>
    		<td align="center" valign="top">
    		<?php athBeginFloatingBox("570","none","VALIDANDO ARQUIVO - SITCS - Sindicais",CL_CORBAR_GLASS_1); ?>
    			<table id="dialog" width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
					<form name="formImpor" action="STimportacaoDatabaseSITCS.php" method="post">
					<input type="hidden" name="uploadArquivo" value="<?php echo $nameArq; ?>">
					<tr>
				      	<td colspan="2"  style="padding:1px 50px 1px 50px;" ><br />
				        <!-- DIALOG 'SICOB - SINDICAIS' EM VERDE E NEGRITO-->
							<span style="color:#009966;float:right; font-size:18px; font-weight:bold" >SITCS - Sindicais</span>
							<span style="float:left;" class="destaque_gde"><strong>Dados do Arquivo</strong></span>
						</td>
      				</tr>
	  				<tr>
						<td>
							<table border="0" width="85%" align="center">
      							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
							</table>
						</td>
					</tr>
					<tr>
       					<td align="center" valign="top" style="padding:20px 50px 10px 50px;" width="1%">
							<table cellpadding="4" cellspacing="0" border="0" width="100%">
							<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
								<td align="right" width="35%"><strong>Cód. do Banco:</strong></td>
								<td>&nbsp;<?php echo $_SESSION['ArqValida_codBancoHA']; ?></td>
							</tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
								<td align="right"><strong>Nome do Banco:</strong></td>
								<td>&nbsp;<?php echo $_SESSION['ArqValida_AFAnomBanHA']; ?></td>
							</tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
								<td align="right"><strong>Nº de Inscrição da Empresa:</strong></td>
								<td>&nbsp;<?php echo $_SESSION['ArqValida_numIncEmpHA']; ?></td>
							</tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
								<td align="right"><strong>Nome da Empresa:</strong></td>
								<td>&nbsp;<?php echo $_SESSION['ArqValida_AFAnomEmpHA']; ?></td>
							</tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
								<td align="right"><strong>Conta:</strong> </td>
								<td>&nbsp;
								<select name="var_cod_conta" id="var_cod_conta" style="width:250px;" size="1">
									<option value="" selected="selected">Manter Conta do Título Vinculado...</option>
									<?php echo(montaCombo($objConn,"SELECT cod_conta, cod_conta||' - '||nome AS nome FROM fin_conta ORDER BY ordem, nome ","cod_conta","nome",getVarEntidade($objConn, "sitcs_conta"))); ?>
								</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
								<td align="right"><strong>Plano de Conta:</strong></td>
								<td>&nbsp;
								<select name="var_cod_plano_conta" id="var_cod_plano_conta" style="width:250px;" size="1">
									<option value="" selected="selected">Manter Plano de Contas do Título Vinculado...</option>
									<?php echo(montaCombo($objConn,"SELECT cod_plano_conta, nome FROM fin_plano_conta ORDER BY ordem, nome ","cod_plano_conta","nome",getVarEntidade($objConn, "sitcs_plconta")));?>
								</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
								<td align="right"><strong>Centro de Custo:</strong></td>
								<td>&nbsp;
								<select name="var_cod_centro_custo" id="var_cod_centro_custo" style="width:250px;" size="1">
									<option value="" selected="selected">Manter Centro de Custo do Título Vinculado...</option>
									<?php echo(montaCombo($objConn,"SELECT cod_centro_custo, nome FROM fin_centro_custo ORDER BY ordem, nome ","cod_centro_custo","nome",getVarEntidade($objConn, "sitcs_ccusto")));?>
								</select>
								</td>
							</tr>
                            <tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
								<td align="right"><strong>Job:</strong></td>
								<td>&nbsp;
								<select name="var_cod_job" id="var_cod_job" style="width:250px;" size="1">
									<option value="" selected="selected">Manter Job do Título Vinculado...</option>
									<?php echo(montaCombo($objConn,"SELECT cod_job, nome FROM fin_job ORDER BY ordem, nome " , "cod_job" , "nome",getVarEntidade($objConn, "sitcs_job")));?>
								</select>
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>DEFINIÇÕES DE BAIXA</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
								<td align="right"><input type="radio" name="var_opcao_baixa" id="var_opcao_baixa1" value="usar_ocorrencia" class="borda"></td>							  		
								<td>Usar a Data de OCORRÊNCIA para efetuar a baixa.</td>
							</tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
								<td align="right"><input type="radio" name="var_opcao_baixa" id="var_opcao_baixa2" value="usar_credito" class="borda" checked="checked"></td>
								<td>Usar a Data de EFETIVAÇÃO do CRÉDITO para efetuar a baixa</td>
							</tr>
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>DEFINIÇÕES DE VENCIMENTO</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
								<td align="right"><input type="radio" name="var_opcao_vcto" id="var_opcao_vcto1" value="manter_datas" class="borda" checked="checked" onFocus="javascript:document.getElementById('var_dias').value='';"></td>
								<td>Manter a data de VENCIMENTO e PAGAMENTO que são recebidos no arquivo</td>
							</tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_2)?>">
								<td align="right"><input type="radio" name="var_opcao_vcto" id="var_opcao_vcto2" value="datas_iguais" class="borda" onFocus="javascript:document.getElementById('var_dias').value='';"></td>
								<td>Fazer com que a data de VENCIMENTO seja igual a data de PAGAMENTO</td>
							</tr>
							<tr bgcolor="<?php echo(CL_CORLINHA_1)?>">
								<td align="right"><input type="radio" name="var_opcao_vcto" id="var_opcao_vcto3" value="dias_antes" class="borda" onFocus="javascript:document.getElementById('var_dias').focus();"></td>
								<td>Dias de vencimento antes da data de PGTO&nbsp;<input type="text" name="var_dias" id="var_dias" size="5"></td>
							</tr>
	          				</table>
						</td>
					</tr>
	 			  	<tr>
						<td style="padding: 0px 0px 20px 0px;">
							<table cellpadding="4" cellspacing="0" border="0" style="padding: 0px 0px 0px 0px;" align="center" width="80%">
								<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>			
								<tr>
									<td width="1%" align="right" style="padding:10px 50px 10px 10px;" nowrap>
										<button onClick="ok(); return false;"><?php echo(getTText("importar_arquivo",C_UCWORDS));?></button>
										<button onClick="cancelar(); return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
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
<?php $objConn = NULL; ?>
