<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSituacao = request("var_situacao");
$intCodSegmento = request("var_cod_segmento");
$strCategoria = request("var_categoria");
$intCodCategoriaExtra = request("var_cod_categoria_extra");
$dateDtVctoIni = request("var_dt_vcto_ini");
$dateDtVctoFim = request("var_dt_vcto_fim");
$strTipoDocumento = request("var_tipo_documento");
$dateDtPgto = request("var_dt_pgto");
$strOpcaoPgto = request("var_opcao_pgto");
$dblVlrPgto = request("var_vlr_pgto");
$strObs = request("var_obs");

$strMSG = "";
if ($strSituacao == "") $strMSG .= "Informe situação<br>";
if ($intCodSegmento == "") $strMSG .= "Informe segmento<br>";
if ($strCategoria == "") $strMSG .= "Informe categoria<br>";
if ($intCodCategoriaExtra == "") $strMSG .= "Informe categoria extra<br>";
if (($dateDtVctoIni == "") || ($dateDtVctoFim == "")) $strMSG .= "Informe período de vencimento<br>";
if ($strOpcaoPgto == "") $strMSG .= "Selecione opção de pagamento<br>";
if (($strOpcaoPgto == "valor_digitado") && ($dblVlrPgto == "")) $strMSG .= "Informe valor se escolheu pagar com o valor digitado<br>";

if ($strMSG != "") {
	mensagem("err_sql_titulo","err_sql_desc",$strMSG,"","erro",1,"");
	die();
}

$objConn = abreDBConn(CFG_DB); // ABERTURA DO BANCO DE DADOS

try{
	$strSQL = " SELECT t1.cod_conta_pagar_receber, t1.dt_emissao, t1.dt_vcto, t1.vlr_saldo, t1.num_documento, t1.historico, t2.cod_pj, t2.razao_social
				FROM fin_conta_pagar_receber t1, cad_pj t2
				WHERE t1.codigo = t2.cod_pj
				AND t1.tipo = 'cad_pj'
				AND t1.situacao ILIKE '".$strSituacao."'
				AND t2.categoria ILIKE '".$strCategoria."'
				AND t2.cod_segmento = ".$intCodSegmento."
				AND t2.cod_categoria = ".$intCodCategoriaExtra."
				AND t1.dt_vcto BETWEEN TO_DATE('".$dateDtVctoIni."','DD/MM/YYYY') AND TO_DATE('".$dateDtVctoFim."','DD/MM/YYYY') ";
	if ($strTipoDocumento != "") $strSQL .= " AND t1.tipo_documento ILIKE '".$strTipoDocumento."' ";
	$strSQL .= " ORDER BY t2.razao_social, t1.dt_emissao ";
	
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}

?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../_scripts/tablesort.js"></script>
<style>
    .menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
    body{ margin: 0px; background-color:#FFFFFF; } 
    ul{ margin-top: 0px; margin-bottom: 0px; }
    li{ margin-left: 0px; }
</style>
<script language="javascript" type="text/javascript">
function ok(){
	var var_msg = "";
	
	if (document.formeditor.var_situacao.value == '') var_msg += "Informe situação\n";
	if (document.formeditor.var_segmento.value == '') var_msg += "Informe segmento\n";
	if (document.formeditor.var_categoria.value == '') var_msg += "Informe categoria\n";
	if (document.formeditor.var_categoria_extra.value == '') var_msg += "Informe categoria extra\n";
	if ((document.formeditor.var_dt_vcto_ini.value == '') || (document.formeditor.var_dt_vcto_fim.value == '')) var_msg += "Informe período de vencimento\n";
	
	if (var_msg != '') {
		alert(var_msg);
	}
	else {
		document.formeditor.submit();
	}
}

function cancelar() {
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
        <form name="formeditor" action="STlctoOrdinarioLotePasso2exec.php" method="post">
        <input type="hidden" name="var_situacao" value="<?php echo $strSituacao; ?>" />
        <input type="hidden" name="var_cod_segmento" value="<?php echo $intCodSegmento; ?>" />
        <input type="hidden" name="var_categoria" value="<?php echo $strCategoria; ?>" />
        <input type="hidden" name="var_cod_categoria_extra" value="<?php echo $intCodCategoriaExtra; ?>" />
        <input type="hidden" name="var_dt_vcto_ini" value="<?php echo $dateDtVctoIni; ?>" />
        <input type="hidden" name="var_dt_vcto_fim" value="<?php echo $dateDtVctoFim; ?>" />
        <input type="hidden" name="var_tipo_documento" value="<?php echo $strTipoDocumento; ?>" />
        <input type="hidden" name="var_dt_pgto" value="<?php echo $dateDtPgto; ?>" />
        <input type="hidden" name="var_opcao_pgto" value="<?php echo $strOpcaoPgto; ?>" />
        <input type="hidden" name="var_vlr_pgto" value="<?php echo $dblVlrPgto; ?>" />
        <input type="hidden" name="var_obs" value="<?php echo $strObs; ?>" />
		<tr><td height="22" colspan="2"></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="550" border="0" cellspacing="0" cellpadding="4">
					<tr>
						<td align="left" style="padding-left:5px;" colspan="2"><img src="../img/lcto_ordinario_lote_passos02.png"></td>
					</tr>
					<tr>
						<td align="left" style="padding-left:5px;" colspan="2"><?php echo(getTText("preparacao_lcto_ordinario_lote_passo02",C_NONE)); ?></td>
					</tr>
					<tr><td height="10" colspan="2"></td></tr>
					<tr>
						<td colspan="2">
                        <table align="center" cellpadding="0" cellspacing="1" style="width:97%;" class="tablesort">
                        	<thead>
                            <tr>
                                <th width="05%" class="sortable" nowrap="nowrap"><?php echo getTText("cod",C_NONE); ?></td>
                                <th width="30%" class="sortable"><?php echo getTText("razao_social",C_NONE); ?></td>
                                <th width="10%" class="sortable-date-dmy" nowrap="nowrap"><?php echo getTText("emissao",C_NONE); ?></td>
                                <th width="10%" class="sortable-date-dmy" nowrap="nowrap"><?php echo getTText("vcto",C_NONE); ?></td>
                                <th width="15%" class="sortable-numeric" nowrap="nowrap"><?php echo getTText("saldo",C_NONE); ?></td>
                                <th width="30%" class="sortable"><?php echo getTText("historico",C_NONE); ?></td>
                            </th>
                            </thead>
                            <tbody>
                        	<?php
							foreach($objResult as $objRS){ 
								?>
                                <tr>
	                                <td align="left"><?php echo getValue($objRS,"cod_pj"); ?></td>
                            	    <td align="left"><?php echo getValue($objRS,"razao_social"); ?></td>
        	                        <td align="center"><?php echo dDate(CFG_LANG,getValue($objRS,"dt_emissao"),false); ?></td>
            	                    <td align="center"><?php echo dDate(CFG_LANG,getValue($objRS,"dt_vcto"),false); ?></td>
                    	            <td align="right"><?php echo FloatToMoeda(getValue($objRS,"vlr_saldo")); ?></td>
                        	        <td align="left"><?php echo getValue($objRS,"historico"); ?></td>
                                </tr>
								<?php
							}
							?>
                            </tbody>
                        </table>
						</td>
					</tr>
					<tr><td height="10" colspan="2"></td></tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
					<tr>
						<td colspan="2">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
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
$objResult->closeCursor();
$objConn = NULL;
?>