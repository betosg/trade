<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strPopulate = request("var_populate");                             //Flag de verificação se necessita popular o session ou não
if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));            	  //Carrega o prefixo das sessions
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app")); //Verificação de acesso do usuário corrente

$objConn   = abreDBConn(CFG_DB);

function cleanField($prValue){
	return(trim(strtolower(preg_replace("/( +AS +[[:alnum:]_\"\(\)\.]+)|([[:alnum:]_\"]+\.)|\n/i","",$prValue))));
}

if(getsession($strSesPfx . "_tabelas") != ""){
	$strColumn = (strpos(getsession($strSesPfx . "_tabelas"),",") !== false) ? "(table_name || '.' || column_name || ' AS ' || table_name || '__' || column_name)" : "column_name";

	$arrAux    = explode(" FROM ",getsession($strSesPfx . "_select"));
	$arrCampos = explode(",",preg_replace("/, */",",",str_replace("SELECT ","",$arrAux[0])));
}
else{
	mensagem("err_dados_titulo","err_dados_obj_desc","","","erro",1);
	die();
}
?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function submeterForm(){
	var strCodigos = "";
	var intI = 0;
	
	while (eval("document.forms[0].msguid_" + intI) != null){
	    if (eval("document.forms[0].msguid_" + intI) != null){
			if (eval("document.forms[0].msguid_" + intI).checked){
			    if (strCodigos != ""){
			      strCodigos = strCodigos + ", " + eval("document.forms[0].msguid_" + intI).value;
			    }
			    else {
			      strCodigos = eval("document.forms[0].msguid_" + intI).value;
			    }
			}
		}
	  intI++;
	}
	
	
	document.formconf.var_campos.value = strCodigos;
	document.formconf.submit();
}
//-->

	<?php
		if(getsession($strSesPfx . "_field_detail") != ''){
	?>
			window.onload = function(){
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = 0;
				window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = document.body.scrollHeight + 15;
			}
	<?php 
		}
	?>
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText(getsession($strSesPfx . "_titulo"),C_TOUPPER) . " - " . getTText("conf_grade",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formconf" action="editconfigexec.php" method="post" onSubmit="submeterForm();">
		   <input type="hidden" name="var_campos" value="">
			<tr>
				<td align="center" valign="top">
					<table width="550" border="0" cellspacing="0" cellpadding="4">
						<?php
						    $intI = 0;
							$intJ = 0;
							$intMaxItensColuna = ((count($arrCampos)/3) != intval(count($arrCampos)/3)) ? intval(count($arrCampos)/3) + 1 : intval(count($arrCampos)/3);
							echo("
								<tr><td colspan=\"3\" height=\"15\"></td></tr>
								<tr><td colspan=\"3\" align=\"left\" valign=\"top\" class=\"destaque_gde\" style=\"padding-left:100px\"><strong>" . getTText("campos_atuais",C_UCWORDS) . "</strong></td></tr>
								<tr><td colspan=\"3\" height=\"2\" align=\"center\" background=\"../img/line_dialog.jpg\"></td></tr>
								<tr><td colspan=\"3\" height=\"5\"></td></tr>
								<tr>");
							foreach($arrCampos as $strCampo){
								if($intI % $intMaxItensColuna == 0){
									echo(" 
													<td align=\"left\" valign=\"top\">
														<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
										");
								}
								
								$strCampo = trim($strCampo);
								$strCampoLabel = preg_replace("/\n|^(.*)__|((([[:alnum:]_\"]+\.)*[[:alnum:]_\"]+) AS)|([[:alnum:]_\"]+\.|(.|\n)* AS)/i","",$strCampo);
								$strCampoLabel = (getTText(trim($strCampoLabel),C_UCWORDS) == " ") ? $strCampoLabel : getTText(trim($strCampoLabel),C_UCWORDS);
								
								echo("
															<tr>
																<td width=\"150\" align=\"right\" title=\"" . $strCampo . "\">" . $strCampoLabel . "</td>
																<td><input type=\"checkbox\" name=\"msguid_" . $intJ . "\" id=\"msguid_" . $intJ . "\" value=\"" . $strCampo . "\" checked " . (($intI == 0) ? "disabled" : "") . " class=\"inputclean\" title=\"" . $strCampo . "\">
																</td>
															</tr>
									");
									
								$intI++;
								$intJ++;
								
								if($intI % $intMaxItensColuna == 0){
									echo("
														</table>
													</td>
										");
								}
							}
							
							if($intI % $intMaxItensColuna != 0){
									echo("
														</table>
													</td>
										");
							}
							echo("</tr><tr><td colspan=\"3\" height=\"10\"></td></tr>");
							
							$arrTables      = explode(",",getsession($strSesPfx . "_tabelas"));
							$intCountTables = count($arrTables);
							$arrCampos      = array_map("cleanField",$arrCampos);
							
							foreach($arrTables as $strTable){
								try{								
									$strSQL    = " SELECT 
													  column_name AS column 
													, table_name
													, ordinal_position 
												   FROM 
													  information_schema.columns
												   WHERE 
													     table_name = '" . $strTable . "'
													 AND ordinal_position <> 1
												   ORDER BY 
													  ordinal_position ";
									$objResult = $objConn->query($strSQL);
								}
								
								catch(PDOException $e){
									mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
									die();
								}
								
								$intNumRound = (($objResult->rowCount()/3) != intval($objResult->rowCount()/3)) ? intval($objResult->rowCount()/3) + 1 : intval($objResult->rowCount()/3);
								$intMaxItensColuna = $intNumRound;
								
								$intI = 0;
								
								echo("
								<tr><td colspan=\"3\" align=\"left\" valign=\"top\" class=\"destaque_gde\" style=\"padding-left:100px\"><strong>" . $strTable . "</strong></td></tr>
								<tr><td colspan=\"3\" height=\"2\" align=\"center\" background=\"../img/line_dialog.jpg\"></td></tr>
								<tr><td colspan=\"3\" height=\"5\"></td></tr>
								<tr>");
								foreach($objResult as $objRS){
									if($intI % $intMaxItensColuna == 0){
										echo(" 
														<td align=\"left\" valign=\"top\">
															<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
											");
									}
									
									$strColumnLocal = getValue($objRS,"column");
									
									if(is_numeric(array_search($strColumnLocal,$arrCampos))) {
										$strColumnValue = getValue($objRS,"table_name") . "." . getValue($objRS,"column") . " AS " . getValue($objRS,"table_name") . "__" . getValue($objRS,"column");
									}
									else{
										$strColumnValue = ($intCountTables > 1) ? getValue($objRS,"table_name") . "." . getValue($objRS,"column") : getValue($objRS,"column");
									}
									
									echo("
																<tr>
																	<td width=\"150\" align=\"right\" title=\"" . $strColumnValue . "\">" . getTText($strColumnLocal,C_UCWORDS) . "</td>
																	<td><input type=\"checkbox\" name=\"msguid_" . $intJ . "\" id=\"msguid_" . $intJ . "\" value=\"" . $strColumnValue . "\" class=\"inputclean\" title=\"" . $strColumnValue . "\">
																	</td>
																</tr>
										");
										
									$intI++;
									$intJ++;
									
									array_push($arrCampos, getValue($objRS,"column"));
									
									if($intI % $intMaxItensColuna == 0){
										echo("
															</table>
														</td>
											");
									}
								}
								
								if($intI % $intMaxItensColuna != 0){
										echo("
															</table>
														</td>
											");
								}
									echo("
								</tr>
								<tr><td colspan=\"3\" height=\"10\"></td></tr>");
								
								$objResult->closeCursor();
							}
							
							$objConn = NULL;
						?>	
							<tr>
								<td colspan="3">
									<table width="100%" cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td width="100"><b><?php echo(getTText("numero_itens",C_UCWORDS)); ?>:&nbsp;</b><td>
											<td><input type="text" name="var_itens_grade" size="5" value="<?php echo(getsession($strSesPfx . "_num_per_page")); ?>"><td>
										</tr>
									</table>
								</td>
							</tr>
							<tr><td height="5" colspan="3"></td></tr>
							<tr><td colspan="3" class="linedialog"></td></tr>
							<tr>
								<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
									<button onClick="submeterForm();"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
									<button onClick="location.href='<?php echo(getsession($strSesPfx . "_grid_default")); ?>';"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
								</td>
							</tr>
						</table>
					</div>
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