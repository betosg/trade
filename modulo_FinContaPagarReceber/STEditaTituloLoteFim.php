<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

//REQUESTS
	$data_antes	  		 		= request("var_data_antes");
	$data_depois 		 		= request("var_data_depois");
	$vlr_titulo_antes    		= request("var_vlr_titulo_antes");
	$vlr_titulo_depois   		= request("var_vlr_titulo_depois");
	$plano_conta_antes  		= request("var_cod_conta_antes");
	$plano_conta_depois  		= request("var_cod_conta_depois");
	$centro_custo_antes  		= request("var_cod_centro_antes");
	$centro_custo_depois 		= request("var_cod_centro_depois");
	$job_antes 					= request("var_job_antes");
	$job_depois 				= request("var_job_depois");
	$observacao_antes 			= request("var_observacao_antes");
	$observacao_depois			= request("var_observacao_depois");
	$cfg_boleto_antes 			= request("var_cfg_boleto_antes");
    $cfg_boleto_depois 			= request("var_cfg_boleto_depois");

//requests debug
/*echo "<br>".	$data_antes	  		 		= request("var_data_antes");
echo "<br>".	$data_depois 		 		= request("var_data_depois");
echo "<br>".	$vlr_titulo_antes    		= request("var_vlr_titulo_antes");
echo "<br>".	$vlr_titulo_depois   		= request("var_vlr_titulo_depois");
echo "<br>".	$plano_conta_antes  		= request("var_cod_conta_antes");
echo "<br>".	$plano_conta_depois  		= request("var_cod_conta_depois");
echo "<br>".	$centro_custo_antes  		= request("var_cod_centro_antes");
echo "<br>".	$centro_custo_depois 		= request("var_cod_centro_depois");
echo "<br>".	$job_antes 					= request("var_job_antes");
echo "<br>".	$job_depois 				= request("var_job_depois");
echo "<br>".	$cfg_boleto_antes 			= request("var_cfg_boleto_antes");
echo "<br>".	$cfg_boleto_depois 			= request("var_cfg_boleto_depois");

die();*/


?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF"  background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("750","none","<b>".getTText("edicao_titulo_lote",C_NONE)."</b>",CL_CORBAR_GLASS_1); ?>
      <table id="var_dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6; display:block;">
		<tr><td height="22" colspan="2"></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="600" border="0" cellspacing="0" cellpadding="4">
					<tr><td width="30%"></td><td width="70%"></td></tr>
					<tr><td align="left" style="padding-left:5px;" colspan="2"><img src="../img/titulo_lote_passo03.gif"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2);?>"><td align="left" style="padding-left:5px;" colspan="2"><span style="float:right;padding-right:3px;cursor:pointer;" onClick="displayArea('<?php echo(CFG_SYSTEM_NAME);?>_detailiframe')"><img src="../img/icon_tree_minus.gif" border="0" id="img_collapse" /></span>Listagem dos Títulos editados em lote</strong></td></tr>
					<tr><td height="165" colspan="2"><iframe name="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe" id="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe" width="100%" height="100%" src="STincludeEditaTituloLote.php?<?php echo("var_sqlcomando=segundo&var_data_antes=".$data_antes."&var_data_depois=".$data_depois."&var_vlr_titulo_antes=".$vlr_titulo_antes."&var_vlr_titulo_depois=".$vlr_titulo_depois."&var_cod_conta_antes=".$plano_conta_antes."&var_cod_conta_depois=".$plano_conta_depois."&var_cod_centro_antes=".$centro_custo_antes."&var_cod_centro_depois=".$centro_custo_depois."&var_job_antes=".$job_antes."&var_job_depois=".$job_depois."&var_observacao_antes=".$observacao_antes."&var_observacao_depois=".$observacao_depois."&var_cfg_boleto_antes=".$cfg_boleto_antes."&var_cfg_boleto_depois=".$cfg_boleto_depois);?>" frameborder="0" scrolling="yes"></iframe></td>
					</tr>
					<tr><td height="30" colspan="2">&nbsp;</td></tr>
					<tr><td height="10" colspan="2"></td></tr>
					<tr><td height="10" colspan="2"></td></tr>
					<tr align="left">
						<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px;"></td>
					</tr>
					<tr><td colspan="2" class="linedialog"></td></tr>
					<tr>
						<td colspan="2">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
										
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
