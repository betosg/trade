<!-- DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" //-->
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$data_antes	  		 		= request("var_data_antes");
	$data_depois 		 		= request("var_data_depois");
	$vlr_titulo_antes    		= MoedaToFloat(request("var_vlr_titulo_antes"));
	$vlr_titulo_depois   		= MoedaToFloat(request("var_vlr_titulo_depois"));
	$plano_conta_antes  		= request("var_cod_conta_antes");
	$plano_conta_depois  		= request("var_cod_conta_depois");
	$centro_custo_antes  		= request("var_cod_centro_antes");
	$centro_custo_depois 		= request("var_cod_centro_depois");
	$job_antes 					= request("var_job_antes");
	$job_depois 				= request("var_job_depois");
	$observacao_antes 			= request("var_observacao_antes");
	$observacao_depois			= request("var_observacao_depois");
	$cfg_boleto_antes  			= request("var_cfg_boleto_antes");
	$cfg_boleto_depois          = request("var_cfg_boleto_depois");
	
	
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
echo "<br>".	$observacao_antes 			= request("var_observacao_antes");
echo "<br>".	$observacao_depois			= request("var_observacao_depois");
echo "<br>".	$cfg_boleto_antes 			= request("var_cfg_boleto_antes");
echo "<br>".	$cfg_boleto_depois 			= request("var_cfg_boleto_depois");
//die();*/

	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	/***           ABERTURA DO BANCO DE DADOS          ***/
	/*****************************************************/
	$objConn = abreDBConn(CFG_DB);
	
	
?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script language="javascript" type="text/javascript">
		function ok(){
			document.formfatura.submit();
		}
		
		function cancelar(){
			document.location.href = "STeditaTituloLotePasso1.php";
		}
		
		function displayArea(prIDArea){
			var objArea = document.getElementById(prIDArea);
			if(objArea == null){ return(null); }
			if(objArea.style.display == "none"){
				objArea.style.display = "block";
				document.getElementById("img_collapse").src = "../img/icon_tree_minus.gif";
			}else{
				objArea.style.display = "none";
				document.getElementById("img_collapse").src = "../img/icon_tree_plus.gif";
			}
		}	
	</script>
</head>
<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("750","none","<b>".getTText("edicao_titulo_lote",C_NONE)."</b>",CL_CORBAR_GLASS_1); ?>
      <table id="var_dialog" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6; display:block;">
        <form name="formfatura" action="STeditaTituloLotePasso2exec.php" method="post">
        	<input type="hidden" name="var_data_antes" id="var_data_antes" value="<?php echo($data_antes); ?>">
			<input type="hidden" name="var_data_depois" id="var_data_depois" value="<?php echo($data_depois); ?>">
			<input type="hidden" name="var_vlr_titulo_antes" id="var_vlr_titulo_antes" value="<?php echo($vlr_titulo_antes); ?>">
			<input type="hidden" name="var_vlr_titulo_depois" id="var_vlr_titulo_depois" value="<?php echo($vlr_titulo_depois); ?>">
			<input type="hidden" name="var_cod_conta_antes" id="var_cod_conta_antes" value="<?php echo($plano_conta_antes); ?>">
			<input type="hidden" name="var_cod_conta_depois" id="var_cod_conta_depois" value="<?php echo($plano_conta_depois); ?>">
			<input type="hidden" name="var_cod_centro_antes" id="var_cod_centro_antes" value="<?php echo($centro_custo_antes); ?>">
			<input type="hidden" name="var_cod_centro_depois" id="var_cod_centro_depois" value="<?php echo($centro_custo_depois); ?>">
			<input type="hidden" name="var_job_antes" id="var_job_antes" value="<?php echo($job_antes); ?>">
			<input type="hidden" name="var_job_depois" id="var_job_depois" value="<?php echo($job_depois); ?>">
   			<input type="hidden" name="var_observacao_antes" id="var_observacao_antes" value="<?php echo($observacao_antes); ?>">
			<input type="hidden" name="var_observacao_depois" id="var_observacao_depois" value="<?php echo($observacao_depois); ?>">
            <input type="hidden" name="var_cfg_boleto_antes" id="var_cfg_boleto_antes" value="<?php echo($cfg_boleto_antes); ?>">
			<input type="hidden" name="var_cfg_boleto_depois" id="var_cfg_boleto_depois" value="<?php echo($cfg_boleto_depois); ?>">
			
		<tr><td height="22" colspan="2"></td></tr>
		<tr> 
			<td align="center" valign="top">
				<table width="600" border="0" cellspacing="0" cellpadding="4">
					<tr><td width="30%"></td><td width="70%"></td></tr>
					<tr><td align="left" style="padding-left:5px;" colspan="2"><img src="../img/titulo_lote_passo02.gif"></td></tr>
					<tr bgcolor="<?php echo(CL_CORLINHA_2);?>"><td align="left" style="padding-left:5px;" colspan="2"><span style="float:right;padding-right:3px;cursor:pointer;" onClick="displayArea('<?php echo(CFG_SYSTEM_NAME);?>_detailiframe')"><img src="../img/icon_tree_minus.gif" border="0" id="img_collapse" /></span>Listagem dos Títulos a serem editados em lote</strong></td></tr>
					<tr>
                    	<td height="165" colspan="2">
                        	 <iframe name="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe" id="<?php echo(CFG_SYSTEM_NAME);?>_detailiframe" width="100%" height="100%" src="STincludeEditaTituloLote.php?<?php echo("var_sqlcomando=primeiro&var_data_antes=".$data_antes."&var_data_depois=".$data_depois."&var_vlr_titulo_antes=".$vlr_titulo_antes."&var_vlr_titulo_depois=".$vlr_titulo_depois."&var_cod_conta_antes=".$plano_conta_antes."&var_cod_conta_depois=".$plano_conta_depois."&var_cod_centro_antes=".$centro_custo_antes."&var_cod_centro_depois=".$centro_custo_depois."&var_job_antes=".$job_antes."&var_job_depois=".$job_depois."&var_observacao_antes=".$observacao_antes."&var_observacao_depois=".$observacao_depois."&var_cfg_boleto_antes=".$cfg_boleto_antes."&var_cfg_boleto_depois=".$cfg_boleto_depois);?>" frameborder="0" scrolling="yes"></iframe>
                        </td>
					</tr>
					<tr><td height="30" colspan="2">&nbsp;</td></tr>
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
								<button onClick="ok();"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
								<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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
