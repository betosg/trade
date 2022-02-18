<?php
/***          DEFINIÇÃO DE CABEÇALHOS HTTP         ***/
/*****************************************************/
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

/***              DEFINIÇÃO DE INCLUDES            ***/
/*****************************************************/
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

/***             LEITURA DOS PARAMETROS            ***/
/*****************************************************/

$strSituacao = request("var_situacao");
$intCodPlanoConta = request("var_cod_plano_conta");
$dateDtVctoIni = request("var_dt_vcto_ini");
$dateDtVctoFim = request("var_dt_vcto_fim");
$strTipoDocumento = request("var_tipo_documento");
$dateDtPgto = request("var_dt_pgto");
$strOpcaoPgto = request("var_opcao_pgto");
$dblVlrPgto = request("var_vlr_pgto");
$strObs = request("var_obs");

$strMSG = "";
if ($strSituacao == "") $strMSG .= "Informe situação<br>";

if (($dateDtVctoIni == "") || ($dateDtVctoFim == "")) $strMSG .= "Informe período de vencimento<br>";
if ($strOpcaoPgto == "") $strMSG .= "Selecione opção de pagamento<br>";
if (($strOpcaoPgto == "valor_digitado") && ($dblVlrPgto == "")) $strMSG .= "Informe valor se escolheu pagar com o valor digitado<br>";

if ($strMSG != "") {
	mensagem("err_sql_titulo","err_sql_desc",$strMSG,"","erro",1,"");
	die();
}

if ($dblVlrPgto == "")
	$dblVlrPgto = "NULL";
else
	$dblVlrPgto = MoedaToFloat($dblVlrPgto);

/***           ABERTURA DO BANCO DE DADOS          ***/
/*****************************************************/
$objConn = abreDBConn(CFG_DB);

$objConn->beginTransaction();
try {
	 $strSQL = "	SELECT out_cod_pj, out_cod_conta_pagar_receber
				FROM sp_efetua_lcto_ordinario_em_lote('".$strSituacao."','".$dateDtVctoIni."','".$dateDtVctoFim."','".$strTipoDocumento."','".$dateDtPgto."','".$strOpcaoPgto."',".$dblVlrPgto.",'".$strObs."','".getsession(CFG_SYSTEM_NAME."_id_usuario")."',".$intCodPlanoConta.") ";
	
	$objResult = $objConn->query($strSQL);
	
	$objConn->commit();
}catch(PDOException $e) {
	$objConn->rollBack();
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}

?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function ok() {
	document.location.href = "index.php";	
}

</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" >
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("725","none","<b>".getTText("titulo_lcto_ordinario_lote",C_NONE)."</b>",CL_CORBAR_GLASS_1); ?>
      <table id="var_dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6; display:block;">
		<tr><td height="22" colspan="2"></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="550" border="0" cellspacing="0" cellpadding="4">
					<tr>
						<td align="left" style="padding-left:5px;" colspan="2"><img src="../img/lcto_ordinario_lote_passos03.png"></td>
					</tr>
					<tr><td height="10" colspan="2"></td></tr>
					<tr>
						<td align="left" style="padding-left:5px;" colspan="2"><?php echo(getTText("preparacao_lcto_ordinario_lote_passo03",C_NONE)); ?></td>
					</tr>
					<tr><td height="40" colspan="2"></td></tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
					<tr>
						<td colspan="2">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap="nowrap">
									<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
                                </td>
                            </tr>
							</table>
						</td>
					</tr> 
				</table>
			</td>
		</tr>
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