<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página
$strExec      = request("var_exec");       // Executor externo (fora do kernel)
$strPopulate  = request("var_populate");   // Flag para necessidade de popular o session ou não
$strAcao   	  = request("var_acao");      // Indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente.

if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), $strOperacao);
	
$objConn  = abreDBConn(CFG_DB);

/**************** Vefifica se esta no modo Exportação *****************/
if ($strOperacao !="") { setsession($strSesPfx . "_DiagOper", $strOperacao); setsession($strSesPfx . "_CodDado", $intCodDado); }
if ( VerificaModoExportacao($strAcao, getTText(getsession($strSesPfx . "_titulo"),C_NONE)) ) {
 //Pega da session a última Operaçao e chavereg utilizados
 $strOperacao = getsession($strSesPfx . "_DiagOper"); // Operação a ser realizada
 $intCodDado  = getsession($strSesPfx . "_CodDado");  // Código chave da página
}
	
/**************** Definição de título e paramêtros iniciais conforme a operação *****************/
$arrCfgDialog = initDialogParams($objConn, $strOperacao, $strExec, $intCodDado);

/**************** Grava no session o código do campo chave atual, para ser usado *****************/
setsession($strSesPfx . "_" . $arrCfgDialog["dlg_campo_chave"], $intCodDado); 

/***************** Campos da tabela para ser usado no descritor *****************/
try{
	$strSQL = " SELECT nome_tabela, dlg_grp 
					, cod_descr_campo, nome, rotulo, descricao, tipo, classe, obrigatorio, obs, rotulo_grp, nowrap
					, param_edit_type, param_edit_size, param_edit_maxlength 
			        , param_combo_nullable, param_combo_disabled, param_combo_select, param_combo_select_values, param_combo_select_captions, param_combo_values, param_combo_captions, param_combo_select_group,param_combo_width 
			        , param_memo_rows, param_memo_cols
			        , param_radio_values, param_radio_captions
			        , param_add_img, param_add_link, param_add_extra
					, search_query, search_dbcamporet, search_dbcampolabel
					, searchpad_modulo
					, file_dir_arquivos, file_prefix, file_flag_sufix
					, valor_padrao, valor_sistema
					, js_eventos, js_funcoes, bypass
				FROM sys_descritor_campos_edicao
				WHERE cod_app = " . getsession($strSesPfx . "_chave_app") . "
					AND (descritor_grp = '" . getsession($strSesPfx . "_descritor_grp") . "' OR descritor_grp IS NULL)
					AND dtt_inativo IS NULL 
					AND ((operacao = '" . $strOperacao . "') OR (operacao IS NULL))
					" . (($strOperacao == "INS") ? " AND dlg_grp = '000' " : "") . "
					AND classe <> 'CHAVE'
			    ORDER BY dlg_grp, ordem ";
		$objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

$strBGColor = CL_CORLINHA_2; // Definição da cor inicial
$strRotuloGRP = "";          // Inicialização da variavel de nomes de grupo

if($objResult->rowCount() > 0){
	
	$strRotulo = getsession($strSesPfx . "_titulo");
	$objRS2    = "";
	
	if($strOperacao != "INS"){ 
		if($intCodDado != ""){
			try{
				$strSQL = " SELECT * 
						    FROM  " . $arrCfgDialog["dlg_nome_tabela"] .
						  " WHERE " . $arrCfgDialog["dlg_campo_chave"] . " = " . $intCodDado;
				$objResult2 = $objConn->query($strSQL);
				
				$objRS2  = $objResult2->fetch();
			}
			catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();
			}
		}
		else{
			mensagem("err_dados_titulo","err_dados_obj_desc","","","erro",1);
			die();
		}
	}
	
	/* Aqui ele testa se o resultado do result set é um array vazio, ou seja, não 
	   encontrou nada na consulta, caso for qualquer operação menos inserção. 
	   No caso da inserção ele entra porque a variável que iria receber o result 
	   set foi inicializada com "". */
    if($objRS2 !== array()){ 
		include_once("../_scripts/scripts.js");
		include_once("../_scripts/STscripts.js");
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE . " - " . getTText($arrCfgDialog["dlg_titulo"],C_TOUPPER)); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript" type="text/javascript">
		<!--
		//********************************************************************************************************
		//******************* Funções de ação dos botões [formeditor_000 - prinicpal] - Início *******************
		//********************************************************************************************************
		function ok(prOperacao){
			if (validateRequestedFields("formeditor_000")==true) {
				if(prOperacao == "INS" || prOperacao == "UPD" || prOperacao == "DEL") {
					var strLocation = (window.opener == null || window.opener == "undefined") ? "<?php echo($arrCfgDialog["dlg_location_default"]); ?>" : "";
					
					document.formeditor_000.DEFAULT_LOCATION.value = strLocation;
					document.formeditor_000.submit();
					
					if(window.opener != null && window.opener != "undefined") { 
						window.opener.location.reload(); 
						window.close(); 
					}
				} else if(prOperacao == "VIE") {
					(window.opener == null || window.opener == "undefined") ? document.location.href = "<?php echo($arrCfgDialog["dlg_location_default"]); ?>" : window.close();
				}
			}
		}

		function cancelar(){
			(window.opener == null || window.opener == "undefined") ? document.location.href = "<?php echo($arrCfgDialog["dlg_location_default"]); ?>" : window.close();
		}

		function aplicar(operacao){
			if (validateRequestedFields("formeditor_000")==true) {
				document.formeditor_000.DEFAULT_LOCATION.value = "<?php echo($arrCfgDialog["dlg_location_aplicar"]); ?>";
				document.formeditor_000.submit();
			}
		}
		//********************************************************************************************************
		//******************** Funções de ação dos botões [formeditor_000 - prinicpal] - Fim *********************
		//********************************************************************************************************


		//********************************************************************************************************
		//************************************ Funções de formulário - Início ************************************
		//********************************************************************************************************
		function abreJanelaPageLocal(pr_link, pr_extra) {
			var auxStrToChange, rExp, auxNewExtra, auxNewValue;
			if (pr_extra != "") {
				rExp = /:/gi;
				auxNewExtra = pr_extra
				if(pr_extra.search(rExp) != -1) {
					auxStrToChange = pr_extra.split(":");
					auxStrToChange = auxStrToChange[1];
					rExp = eval("/:" + auxStrToChange + ":/gi");
					auxNewValue = eval("document.formeditor." + auxStrToChange + ".value");
					auxNewExtra = pr_extra.replace(rExp, auxNewValue);
				}
				pr_link = pr_link + auxNewExtra;
			}
			
			AbreJanelaPAGE(pr_link, "800", "600");
		}

		function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
			strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
			AbreJanelaPAGE(strLink, "570", "270");
		}

		function setFormField(formname, fieldname, valor){
			if ((formname != "") && (fieldname != "") && (valor != "")){
				eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
			}
		}

		function submeterFormRel(prGrpForm, prCodGrid){
			eval("document.formeditor_" + prGrpForm).target = "grid_dialog_" + prGrpForm;
			eval("document.formeditor_" + prGrpForm).action = "../_database/athinserttodb.php";
			eval("document.formeditor_" + prGrpForm).DEFAULT_LOCATION.value = "../<?php echo(getsession($strSesPfx . "_dir_modulo")); ?>/data.php?var_cod_pai=<?php echo($intCodDado); ?>&var_dlg_grp=" + prGrpForm + "&var_cod_dialog_grid=" + prCodGrid;
			eval("document.formeditor_" + prGrpForm).submit();
			eval("document.formeditor_" + prGrpForm).reset();
		}

		function collapseItem(prIndex) {
			bloco = document.getElementById("dialog_cont_" + prIndex);
			image = document.getElementById("image_" + prIndex);
			if(bloco.style.display == "" || bloco.style.display == "block") {
				bloco.style.display = "none";
				image.src = "../img/icon_tree_plus.gif"; 
			} else {
				bloco.style.display = "block";
				image.src = "../img/icon_tree_minus.gif";
			}
		}

		function resetSearchField(prFieldName,prFieldLabel){
			document.getElementById(prFieldName).value = "";
			document.getElementById(prFieldLabel).innerHTML = "<?php echo(getTText("selecione",C_NONE)."..."); ?>";
		}
		
			<?php if(getsession($strSesPfx . "_field_detail") != '') { ?>
					window.onload = function(){
						window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = 0;
						window.parent.window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo getsession($strSesPfx . "_value_detail")?>').style.height = document.body.scrollHeight + 15;
					}
			<?php } ?>
		
		function debugFields(prDialogGrp) {
			var objElement = null;
			if(document.getElementById("kernel_debug_" + prDialogGrp).style.display == "none") {
				document.getElementById("kernel_debug_" + prDialogGrp).innerHTML = "";
				document.getElementById("kernel_debug_" + prDialogGrp).style.display = "block";
				
				for(intI=0;document.forms["formeditor_" + prDialogGrp].elements[intI] != null;intI++) {
					objElement = document.forms["formeditor_" + prDialogGrp].elements[intI];
					document.getElementById("kernel_debug_" + prDialogGrp).innerHTML += intI + " - " + objElement.name + "(" + objElement.id + ") = " + objElement.value + "<br><br>";
				}
			} else {
				document.getElementById("kernel_debug_" + prDialogGrp).style.display = "none";
			}
		}

		//********************************************************************************************************
		//************************************ Funções do formulário - Fim ***************************************
		//********************************************************************************************************

		//-->
		</script>
	</head>
	<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" <?php if(getsession($strSesPfx . "_field_detail") == '') {?> background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" <?php } ?>>
	<table border="0" cellpadding="0" cellspacing="0" height="100%" align="center">
	<tr>
		<td valign="top">
				<?php
					$strDialogGrp = "";
					$objRS = $objResult->fetch();
					$boolContinuaRS = true;
					$boolMesmaLinha = false;
					$intI = 1;
					
					while($boolContinuaRS){
						if($strDialogGrp != getValue($objRS,"dlg_grp")) {
						
							$strRotuloGRP = ""; // Recebe vazio para não conflitar com as configurações da dialog anterior
						
							if(getValue($objRS,"dlg_grp") == "000"){ 
								$strHeader = getTText(getsession($strSesPfx . "_titulo"),C_TOUPPER) . " (" . getTText(strtolower($arrCfgDialog["dlg_titulo"]),C_NONE) . ")";
								$strCollapse = "";
							}
							else{
								$strTituloHeader = ($strOperacao == "UPD") ? getTText("insercao",C_NONE) : getTText(strtolower($arrCfgDialog["dlg_titulo"]),C_NONE);
								
								$strHeader = "<div style='float:left; padding-left:5px;' class='padrao_gde'>
												" . getTText(getValue($objRS,"nome_tabela"),C_TOUPPER) . " (" . $strTituloHeader . ")" . "
											  </div>
											  <div style='float:right; padding:2px 5px 0px 0px;'>
												<img src='../img/icon_tree_minus.gif' id='image_" . getValue($objRS,"dlg_grp") . "' onClick=\"collapseItem('" . getValue($objRS,"dlg_grp") . "');\">
											  </div>";
								$strCollapse = "display:block;";
							}

				if ((getsession($strSesPfx . "_titulo_app") <> "") and ($strOperacao == 'INS' or $strOperacao == 'UPD'))
							{
								mensagemStd(getTText(getsession($strSesPfx . "_titulo_app"),C_NONE)
										, getTText(getsession($strSesPfx . "_subtitulo_app"),C_NONE)
										, getsession($strSesPfx . "_descricao_dialog")											
										, 0
										, ""
										, CFG_DIALOG_WIDTH);			
							}
				
				
							athBeginFloatingBox(CFG_DIALOG_WIDTH,"none",$strHeader,CL_CORBAR_GLASS_1);	?>
			<table id="dialog_cont_<?php echo(getValue($objRS,"dlg_grp")); ?>" class="kernel_dialog" width="100%" cellpadding="0" cellspacing="0" style="<?php echo($strCollapse); ?>">
				<form name="formeditor_<?php echo(getValue($objRS,"dlg_grp")); ?>" id="formeditor_<?php echo(getValue($objRS,"dlg_grp")); ?>" action="<?php echo($arrCfgDialog["dlg_action"]); ?>" method="post">
				<input type="hidden" name="DEFAULT_TABLE" value="<?php echo(getValue($objRS,"nome_tabela")); ?>">
				<input type="hidden" name="FIELD_PREFIX" value="dbvar_">
				<input type="hidden" name="RECORD_KEY_NAME" value="<?php echo($arrCfgDialog["dlg_campo_chave"]); ?>">
				<input type="hidden" name="RECORD_KEY_VALUE" value="<?php echo($intCodDado); ?>">
				<input type="hidden" name="DEFAULT_LOCATION" value="">
				<tr><td height="22" style="padding:10px"><b><?php echo((getValue($objRS,"dlg_grp") == "000") ? $arrCfgDialog["dlg_aviso1"] : "<input type=\"hidden\" name=\"dbvar_num_" . $arrCfgDialog["dlg_campo_chave"] . "\" value=\"" . $intCodDado . "\">"); ?></b></td></tr>
				<tr> 
				  <td align="center" valign="top">
					<table width="<?php echo(CFG_DIALOG_CONTENT_WIDTH); ?>" border="0" cellspacing="0" cellpadding="4">
				<?php
							if($strOperacao != "INS" && getValue($objRS,"dlg_grp") == "000"){
								echo("
						<tr>
							<td width='1%'  align='right' class='coluna_label'><label><strong>*" . getTText($arrCfgDialog["dlg_campo_chave"],C_NONE) . ":</strong></label>&nbsp;</td>
							<td width='99%' align='left'  class='coluna_valor'>" . $intCodDado . "</td>
						</tr>
									");
							}
							
							$strDialogGrp = getValue($objRS,"dlg_grp");
						}
						
						/****************** Faz ou desfaz marcação de grupos de controles ******************/
						
						if($strRotuloGRP != getValue($objRS,"rotulo_grp")){
							$strRotuloGRP = getValue($objRS,"rotulo_grp");
							echo("
						<tr><td colspan='2' height='5' bgcolor='#FFFFFF'></td></tr>
						<tr>
							<td></td>
							<td align='left' valign='top' class='destaque_gde'><strong>" . $strRotuloGRP . "</strong></td>
						</tr>
						<tr><td colspan='2' class='group_divisor'></td></tr>
						<tr><td colspan='2' height='10' bgcolor='#FFFFFF'></td></tr>
								");
						}
						
						if(getValue($objRS,"bypass")){
							$strComponente = strtolower(getValue($objRS,"tipo")) . "_" . getValue($objRS,"nome");
						}else{
							$strComponente = "dbvar_" . strtolower(getValue($objRS,"tipo")) . "_" . getValue($objRS,"nome");
						}
						$strValor = "";
						$strMarca = "";
						$boolContinua = false;
						
						if($strOperacao != "INS" && !getValue($objRS,"valor_sistema") && $strDialogGrp == "000"){
							$strValor = getValue($objRS2,getValue($objRS,"nome")); //porque tenho que pegar apenas uma coluna por vez, foi feito um fetch no objRS2
							if(getValue($objRS,"param_add_link") != ""){
								$strValor = html_entity_decode($strValor . "");
							}
						}
						else{
							
							$strValor = (getValue($objRS,"valor_sistema")) ? getsession(getValue($objRS,"valor_padrao")) : getValue($objRS,"valor_padrao");
						}
						
						if(getValue($objRS,"obrigatorio") == "1"){
							$strComponente .= "ô"; 
							$strMarca = "*"; 
						}
						
						/****** Se for AUTO então usa um controle oculto ******/
						if( (!$boolContinua) && (strpos(getValue($objRS,"classe"), "EDIT") !== false) && (strpos(getValue($objRS,"param_edit_type"), "hidden") !== false)) {
							echo("<input name='" . $strComponente . "' id='" . $strComponente . "' type='hidden' value='" . $strValor . "'>");
							$boolContinua = true;
						}
						
						/********** Senão usa os visíveis **********/
						if(!$boolContinua && ($strDialogGrp == "000" || ($strOperacao != "VIE" && $strOperacao != "DEL"))){
							
							/******* Se a operação é view ou delete todos os campos viram labels na hora de exibir-los ******/
							($strOperacao == "VIE" || $strOperacao == "DEL") ? $strClasse = "LABEL" : $strClasse = getValue($objRS,"classe") ;
							
							if(!$boolMesmaLinha) { echo("<tr bgcolor='" . $strBGColor . "'>"); }
							
							$strRotulo    = getValue($objRS,"rotulo");
							$strNomeField = getValue($objRS,"nome");
							
							echo("  <td valign='top' class='coluna_label'>
										<label for='" . $strComponente . "_" . getValue($objRS,"dlg_grp") . "'>
											" . 
												(($strRotulo != "") ? $strMarca . getTText($strRotulo,C_NONE) . ":" : "")
											. "
										</label>
									</td>
								");
								
							if(getValue($objRS,"nowrap") && !$boolMesmaLinha) { 
								echo("	
									<td style='padding:0px;'>
										<table border='0' cellspacing='0' cellpadding='0' width=\"100%\">
											<tr>	
												<td class='coluna_valor' style='white-space:nowrap !important;'>"); 
								$boolMesmaLinha = true;
							} else {
								echo("
									<td class='coluna_valor'" . ((getValue($objRS,"nowrap")) ? " style='white-space:nowrap !important;'" : " width='99%'") . ">");
							}
							
							/********* Pega todos os eventos e ações e monta as chamadas *********/
							$strEventos = getValue($objRS,"js_eventos");
							$strFuncoes = getValue($objRS,"js_funcoes");
							
							$arrEventos = explode(";", $strEventos);
							$strChamadas = " ";
							
							foreach($arrEventos as $strEvento){
								$strEvento = trim($strEvento);
								$strFuncao = getTextBetweenTags($strFuncoes, "[", "]", $PosIni, $PosFim);
								if (($strEvento != "") && ($strFuncao != "")) {
									$strChamadas .= $strEvento . "=\"javascript:" . $strFuncao . "\" ";
									$strFuncoes = substr_replace($strFuncoes, "", $PosIni, $PosFim - $PosIni);
								}
							}
							
							switch($strClasse){								
								/****************** EDITs *******************/
								case "EDIT":
									echo("<input name='" . $strComponente . "' id='" . $strComponente . "_" . getValue($objRS,"dlg_grp") . "'");
									
									if($strValor != "" ) {
										if(getValue($objRS,"tipo") == "DATE"    ) { $strValor = dDate(CFG_LANG, $strValor, false);}
										if(getValue($objRS,"tipo") == "DATETIME") { $strValor = dDate(CFG_LANG, $strValor, true); }
										if(getValue($objRS,"tipo") == "AUTODATE") { $strValor = dDate(CFG_LANG, $strValor, true); }
										if(getValue($objRS,"tipo") == "MOEDA"   ) { 
											$strValor = number_format($strValor, 2);
											$strValor = str_replace(",", "", $strValor);
											$strValor = str_replace(".", ",", $strValor);
										}
										if(getValue($objRS,"tipo") == "MOEDA4CD") { 
											$strValor = number_format($strValor, 4);
											$strValor = str_replace(",", "", $strValor);
											$strValor = str_replace(".", ",", $strValor);
										}
									}
									echo(" value='".$strValor."' type='".getValue($objRS,"param_edit_type") . "' "); 
									echo(" maxlength='". getValue($objRS,"param_edit_maxlength")."' ");
									echo(" " . $strChamadas);
									echo(" title='".getTText($strNomeField,C_NONE)."' ");
									echo(" style='width:".getValue($objRS,"param_edit_size")."'>"); 
								break;//*/
								
								/******************* FILEs *******************/
								case "FILE":
									echo("<input type='text' name='".$strComponente."' id='".$strComponente."_".getValue($objRS,"dlg_grp")."' "); 
									echo(" value='".$strValor."' readonly='true' title='".getTText($strNomeField,C_NONE). "' ");
									echo(" style='width:80px;' >");
									echo("<input type='button' name='btn_uploader' value='Upload' class='inputclean' ");
									echo("onClick=\"callUploader('formeditor_" . getValue($objRS,"dlg_grp") . "','" . $strComponente ."','\\\\" . str_replace("\\","\\\\",replaceParametersSession(getValue($objRS,"file_dir_arquivos"))) . "\\\\','" . getValue($objRS,"file_prefix") . "','" . getValue($objRS,"file_flag_sufix") . "');\">");
								break;//*/
								
								/******************* COMBOs *******************/
								case "COMBO":	
									echo("<select name='" . $strComponente . "' id='" . $strComponente . "_" . getValue($objRS,"dlg_grp") . "'");
									echo(" " . $strChamadas);
									if(getValue($objRS,"param_combo_width") != 0){ echo("style='width:".getValue($objRS,"param_combo_width")."'"); }
									echo(" size='1' title='" . getTText($strNomeField,C_NONE) . "'");
									if(getValue($objRS,"param_combo_disabled") == "1" ) { echo(" disabled"); }
									//echo(" tabindex=\"" . $intI . "\">");
									echo(">");
								
									if(getValue($objRS,"param_combo_nullable") == "1" ) { 
										echo("<option value=\"\"");
										if( $strValor == "") echo "selected";
										echo ("></option>") ; 
									}
									
									if(getValue($objRS,"param_combo_select") != "") {
										echo(montaCombo($objConn, getValue($objRS,"param_combo_select"), getValue($objRS,"param_combo_select_values"), getValue($objRS,"param_combo_select_captions"), $strValor, getValue($objRS,"param_combo_select_group"))); 
									}
									if((getValue($objRS,"param_combo_values") != "") && (getValue($objRS,"param_combo_captions") != "")) {
										if(getValue($objRS,"param_combo_select") != "" && getValue($objRS,"param_combo_select_group") != "") {
											echo("<optgroup label=\"" . getTText("outros",C_TOUPPER) . "\">");
										}
										$arrValues   = explode(";", getValue($objRS,"param_combo_values"));
										$arrCaptions = explode(";", getValue($objRS,"param_combo_captions"));
										
										if(getValue($objRS,"tipo") == "STATUS") {
											if ($strOperacao != "INS") {
												if (trim(strval($strValor) . "") != "") $strValor = "I";
												if (trim(strval($strValor) . "") == "") $strValor = "A";
											}
										}
										
										$intI = 0;
										foreach($arrValues as $strArrValues){
											$strPattern = '{{system_name}}';
											$strArrValuesReplaced =  $strArrValues;
											$strArrValues = preg_replace($strPattern, CFG_SYSTEM_NAME, $strArrValuesReplaced);
											echo("<option value=\"" . trim($strArrValues) . "\"");
											if(trim(substr($strValor,0,6)) == '[text]'){
												if(trim(strtoupper(strip_tags(getTText(trim($arrCaptions[$intI]),C_UCWORDS)))) == trim(strtoupper(substr($strValor,6,strlen($strValor))))) echo(" selected"); 
											}
											else {
												if(getValue($objRS,"tipo") == "STATUS") { 
													if ((trim(strval($strArrValues) . "") == "I") && (trim(strval($strArrValues) . "") == trim(strval($strValor) . ""))) echo(" selected");
													if ((trim(strval($strArrValues) . "") == "A") && (trim(strval($strArrValues) . "") == trim(strval($strValor) . ""))) echo(" selected");
												}
												else {
													if(strval(trim($strValor) . "") == trim(strval($strArrValues) . "")) echo(" selected");
												}
											}
											echo(">" . getTText(trim($arrCaptions[$intI]),C_NONE) . "</option>");
											$intI++;
										}
									}
									echo("</select>");
								break;//*/
								
								/******************* MEMOs ********************/
								case "MEMO":
									echo("<textarea name='" . $strComponente . "' id='" . $strComponente . "_" . getValue($objRS,"dlg_grp") . "'"); 
									echo(" cols='" . getValue($objRS,"param_memo_cols") . "' rows='" . getValue($objRS,"param_memo_rows") . "'");
									echo(" " . $strChamadas);
									echo(" title='" . getTText($strNomeField,C_NONE) . "'>" . $strValor . "</textarea>");
								break;//*/
								
								/******************* LABELs *******************/
								case "LABEL":
									if($strValor != "") {
										if(getValue($objRS,"tipo") == "DATE"    ) { $strValor = dDate(CFG_LANG, $strValor, false);}
										if(getValue($objRS,"tipo") == "DATETIME") { $strValor = dDate(CFG_LANG, $strValor, true); }
										if(getValue($objRS,"tipo") == "AUTODATE") { $strValor = dDate(CFG_LANG, $strValor, true); }
										if(getValue($objRS,"tipo") == "EMAIL"   ) { $strValor = "<a href=\"mailto:" . $strValor . "\">" . $strValor . "</a>"; }
										if(getValue($objRS,"tipo") == "LINK"    ) { $strValor = "<a href=\"" . $strValor . "\" target=\"_blank\">" . $strValor . "</a>"; }
										if(getValue($objRS,"tipo") == "ARQUIVO" ) { $strValor = "<a href=\"../" . CFG_USR_DIR_UPLOAD_ARQ . "/" . $strValor . "\" target=\"_blank\">" . $strValor . "</a>"; }
										if(getValue($objRS,"tipo") == "MOEDA"   ) { 
											$strValor = number_format($strValor, 2);
											$strValor = str_replace(",", "", $strValor);
											$strValor = str_replace(".", ",", $strValor);
										}
										if(getValue($objRS,"tipo") == "MOEDA4CD") { 
											$strValor = number_format($strValor, 4);
											$strValor = str_replace(",", "", $strValor);
											$strValor = str_replace(".", ",", $strValor);										
										}
										if(getValue($objRS,"classe") == "COMBO") {
											if(getValue($objRS,"param_combo_select") != "") {
												$objResultLocal = $objConn->query(replaceParametersSession(getValue($objRS,"param_combo_select")));
												foreach($objResultLocal as $objRSLocal){
													if(getValue($objRSLocal,getValue($objRS,"param_combo_select_values")) == $strValor) {
														$strValor = $objRSLocal[getValue($objRS,"param_combo_select_captions")];
													} 
												}
											}
											
											$arrStrValues = explode(";",getValue($objRS,"param_combo_values"));
											foreach($arrStrValues as $strComboValue){
												if($strComboValue == $strValor){
													$strValor = getTText($strValor,C_NONE);
												}
											}
										}
										
										if(getValue($objRS,"classe") == "FILE") {
											$strAuxPath = strtolower(str_replace("\\","/",replaceParametersSession(getValue($objRS,"file_dir_arquivos"))));
											$strPath = findLogicalPath($strAuxPath) . "/" . $strValor;
											
											if(ereg("\.(jp[e]?g|gif|png)$",$strValor)) {
												/* Não podemos usar ONLINE a getimagesize, neste caso vamos forçar a largura da imagem em 150px
												$arrImageInfo = getimagesize($strPath); // Coloca num array algumas informações sobre o arquivo selecionado.
												$intTamanho   = $arrImageInfo[0]; // Largura em pixels da imagem
												
												$intTamanho = ($intTamanho < 150) ? $intTamanho : 150;
												*/
												$intTamanho = 150;
												$strValor = "<img src=\"" . $strPath . "\" border=\"0\" width=\"" . $intTamanho . "\">";
											}
											else{
												$strValor = "<b>" . $strValor . "</b>";
											}
											
											$strValor = "<a href=\"" . $strPath . "\">" . $strValor . "</a>";
										}
										
										
										if(getValue($objRS,"classe") == "SEARCH_SQL"){
											$strSQL = getValue($objRS,"search_query");
											$arrSQL = explode("WHERE",$strSQL);
											
											$arrSQL[0] .= " WHERE " . getValue($objRS,"search_dbcamporet") . " = " .  $strValor;
											$strSQL = preg_replace("/\[[[:punct:]]|\]|\"/","",$arrSQL[0]);
											
											$objResultSearch = $objConn->query($strSQL);
											
											foreach($objResultSearch as $objRSSearch){
												$strValor = getValue($objRSSearch,getValue($objRS,"search_dbcampolabel"));
											}
										}
									}
									
									$strValor = str_replace("\r\n","<br>",$strValor);
									$strValor = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$strValor);
									
									echo($strValor . "&nbsp;&nbsp;");
								break;//*/
								
								/******************* RADIOs *******************/
								case "RADIO": 
									$arrValues   = explode(";", getValue($objRS,"param_radio_values")."");
									$arrCaptions = explode(";", getValue($objRS,"param_radio_captions")."");
									$intI = 0;
									
									if(getValue($objRS,"tipo") == "STATUS") {
										if ($strOperacao != "INS") {
											if (trim(strval($strValor) . "") != "") $strValor = "I";
											if (trim(strval($strValor) . "") == "") $strValor = "A";
										}
									}
									
									foreach($arrValues as $strArrValues){
										echo("<input name='" . $strComponente . "' id='" . $strComponente . "_" . getValue($objRS,"dlg_grp") . "' type='radio' title='" . getTText($strNomeField,C_NONE) . "' value='" . trim($strArrValues) . "'");
										if(trim(substr($strValor,0,6)) == '[text]'){
											if(trim(strtoupper(strip_tags(getTText(trim($arrCaptions[$intI]),C_UCWORDS)))) == trim(strtoupper(substr($strValor,6,strlen($strValor))))) echo(" checked");
										}
										else{
											if(getValue($objRS,"tipo") == "STATUS") { 
												if ((trim(strval($strArrValues) . "") == "I") && (trim(strval($strArrValues) . "") == trim(strval($strValor) . ""))) echo(" checked");
												if ((trim(strval($strArrValues) . "") == "A") && (trim(strval($strArrValues) . "") == trim(strval($strValor) . ""))) echo(" checked");
											}
											else {
												if(trim(strval($strValor) . "") == trim(strval($strArrValues) . "")) echo(" checked");
											}
										}
										echo(" " . $strChamadas);
										echo(" class='inputclean'>" . getTText(trim($arrCaptions[$intI]),C_NONE));
										$intI++;
									}
								break;//*/
								
								/******************* CHECKs *******************/
								case "CHECK":
									echo("<input name='" . $strComponente . "' id='" . $strComponente . "_" . getValue($objRS,"dlg_grp") . "' type='radio' title='" . getTText($strNomeField,C_NONE) . "' value='true'");
									if($strValor == true || $strValor == "true") { echo(" checked"); }
									echo(" class='inputclean'>" . getTText("sim",C_NONE));
									
									echo("<input name='" . $strComponente . "' id='" . $strComponente . "_" . getValue($objRS,"dlg_grp") . "' type='radio' title='" . getTText($strNomeField,C_NONE) . "' value='false'");
									if($strValor == false || $strValor == "false" && !is_null($strValor)) { echo(" checked"); }
									echo(" class='inputclean'>" . getTText("nao",C_NONE));
								break;//*/
								
								/******************* SEARCH_SQL *******************/
								case "SEARCH_SQL":
									if($strOperacao != "INS" && $strValor != ""){
										$strSQL = getValue($objRS,"search_query");
										$arrSQL = explode("WHERE",$strSQL);
										
										$arrSQL[0] .= " WHERE " . getValue($objRS,"search_dbcamporet") . " = " .  $strValor;
										$strSQL = preg_replace("/\[[[:punct:]]|\]|\"/","",$arrSQL[0]);
										
										$objResultSearch = $objConn->query($strSQL);
										
										foreach($objResultSearch as $objRSSearch){
											$strValorLabel = getValue($objRSSearch,getValue($objRS,"search_dbcampolabel"));
										}
									}
									else{
										$strValorLabel = getTText("selecione",C_NONE) . "...";
									}
									
									echo("
										<input type='text' name='" . $strComponente . "' id='" . $strComponente . "_" . getValue($objRS,"dlg_grp") . "' value='" . $strValor . "' " . $strChamadas .">
										<a href=\"javascript:abreJanelaPageLocal('resultaslw.php?var_coditem=" .getValue($objRS,"cod_descr_campo")."&var_fieldname=" . $strComponente . "&var_dialog_grp=" . $strDialogGrp . "','');\">
											<img src='../img/icon_search.gif' border='0' hspace='1' align='absmiddle' title='" . getTText("pesquisar",C_NONE) . "'>
										</a>
										");
								
								break;//*/
								
							}
							
							/******************* ADD *******************/
							if(getValue($objRS,"param_add_link") != "" && ($strOperacao == "INS" || $strOperacao == "UPD")) {
								$strLink = getValue($objRS,"param_add_link");
																
								$strLink .= (strpos($strLink,"?")) ?  "&" : "?"; // verifica se já tem uma querystring no link e coloca o caracter adequado
								$strLink .= "var_chavereg=" . $intCodDado;                  // coloca o código do item corrente para que seja usado na proxima página
								
								echo("<a href=\"javascript:abreJanelaPageLocal('" . $strLink . "','" . getValue($objRS,"param_add_extra") . "');\"><img src='" . getValue($objRS,"param_add_img") . "' border=\"0\" hspace=\"5\" align=\"absmiddle\"></a>");
							}//*/
							
							/******************* SEARCH DEFAULT *******************/
							if(getValue($objRS,"searchpad_modulo") != "" && ($strOperacao == "INS" || $strOperacao == "UPD")){
								echo("<a href=\"javascript:abreJanelaPageLocal('../" . getValue($objRS,"searchpad_modulo") . "/?var_acao=single&var_fieldname=" . $strComponente . "_" . getValue($objRS,"dlg_grp") . "&var_formname=formeditor" . "_" . getValue($objRS,"dlg_grp") . "','');\"><img src=\"../img/icon_search.gif\" border=\"0\" hspace=\"5\" align=\"absmiddle\"></a>");
							}//*/
							
							/******************* CAPTCHA *******************/
							if(getValue($objRS,"tipo") == "CAPTCHA") {
									echo("&nbsp;&nbsp;<img src=\"../_class/securimage/securimage_show.php?sid=" . md5(uniqid(time())). "\">&nbsp;&nbsp;");								
							}//*/
							
							/*********** SHOW HELP (OBS e DESCRICAO) *************/
							if ($strOperacao == "INS" || $strOperacao == "UPD") {
								echo("<span class='comment_med'>"); 
								if (getValue($objRS,"obs") != "") {
								  echo("<a href=\"javascript:abreJanelaPageLocal('showhelp.php?var_chavereg=" . getValue($objRS,"cod_descr_campo") . "','');\"><img src=\"../img/icon_help2.gif\" border=\"0\" hspace=\"5\" align=\"absmiddle\"></a>");
								};
								echo("&nbsp;" . getValue($objRS,"descricao",false) . "</span>&nbsp;</td>\n");	
							}//*/
							
							if((!getValue($objRS,"nowrap"))) { 
								$strBGColor = ($strBGColor == CL_CORLINHA_2) ? CL_CORLINHA_1 : CL_CORLINHA_2; /*** Troca cor de fundo da linha ****/
								if($boolMesmaLinha){
									echo("</tr>
										</table>
									</td>
								</tr>"); 
									$boolMesmaLinha = false;
								}
								else{
									echo("</tr>");
								}
							}
							$boolContinua   = true;
							$intI++;
						}
						$boolContinuaRS = ($objRS = $objResult->fetch());
						
						if($strDialogGrp != getValue($objRS,"dlg_grp")) {
						
							$strSQL = " SELECT cod_dialog_grid, grid_height, grid_default
										 FROM sys_dialog_grid
										WHERE cod_app = " . getsession($strSesPfx . "_chave_app") . "
										  AND dlg_grp = '" . $strDialogGrp . "'";
							$objRSGrid = $objConn->query($strSQL)->fetch();
								
							echo(showButtonsArea($strDialogGrp, $strOperacao, getValue($objRSGrid,"cod_dialog_grid"), getsession($strSesPfx . "_num_filhos"), $arrCfgDialog["dlg_aviso2"])); 
							
							if($strDialogGrp != "000") {
								if(getValue($objRSGrid,"cod_dialog_grid") != "") {
									$strIframeDialogSrc = (getValue($objRSGrid,"grid_default") != "") ? getValue($objRSGrid,"grid_default") : "data.php";
									
									$strIframeDialogSrc .= "?var_cod_pai=" . $intCodDado . "&var_cod_dialog_grid=" . getValue($objRSGrid,"cod_dialog_grid");
									$intIframeDialogHeight = getValue($objRSGrid,"grid_height");
								} else { 
									$strIframeDialogSrc = "about:blank";
									$intIframeDialogHeight = "0";
								}
								echo("<tr><td colspan='2' style='padding-left:10px;'><iframe src=\"" . $strIframeDialogSrc . "\" name='grid_dialog_" . $strDialogGrp . "' id='grid_dialog_" . $strDialogGrp . "' width='100%' height='" . $intIframeDialogHeight . "' frameborder='0' scrolling='auto' allowtransparency='true'></iframe></td></tr>");
							}
				?>
			</table>
		  </td>
		</tr>
	   </form>
	  </table>
      <?php				
							athEndFloatingBox(); 
							echo("<br><div id='kernel_debug_".$strDialogGrp."' style='background-color:white; width:".(CFG_DIALOG_WIDTH - 10)."px; padding:5px; display:none;'></div>");
						}
					}
		?>
   </td>
  </tr>
</table>
</body>
</html>
<?php
    }
	if($strOperacao != "INS") { 
		$objRS2 = NULL;
		$objResult2->closeCursor();
	}
}
else{
	mensagem("err_dados_titulo","err_dados_obj_desc","","","erro",1);
}

$objResult->closeCursor();
$objConn = NULL;
?>