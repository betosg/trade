<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");

$strTipoCampo = strtolower(request("var_tipo_campo"));

switch($strTipoCampo){	
	case "": 
	case "chave": 
	case "label": $strClausula = " AND nome LIKE '%chave%' ";
	break;
	case "file": $strClausula = " AND (nome LIKE '%edit%' OR nome like '%add%') ";
	break;
	case "search_": $strClausula = " AND (nome LIKE '%edit%' OR nome LIKE '%search_%' AND NOT nome LIKE '%searchpad%' OR nome like '%add%') ";
	break;
	case "searchpad": $strClausula = "AND (nome LIKE '%edit%' OR nome LIKE '%searchpad%' OR nome like '%add%') ";
	break;
	default: $strClausula = " AND (nome LIKE '%" . $strTipoCampo . "%' OR nome like '%add%') ";
	break;
}

$objConn = abreDBConn(CFG_DB);

/***************** Campos da tabela para ser usado no descritor *****************/
try{
	$strSQL = " SELECT nome_tabela, dlg_grp 
					, cod_descr_campo, nome, rotulo, descricao, tipo, classe, obrigatorio, obs, rotulo_grp 
					, param_edit_type, param_edit_size, param_edit_maxlength 
			        , param_combo_nullable, param_combo_disabled, param_combo_select, param_combo_select_values, param_combo_select_captions, param_combo_values, param_combo_captions, param_combo_select_group 
			        , param_memo_rows, param_memo_cols
			        , param_check_values, param_check_captions
			        , param_radio_values, param_radio_captions
			        , param_add_img, param_add_link, param_add_extra
					, search_query
					, searchpad_modulo
					, valor_padrao, valor_sistema
				FROM sys_descritor_campos_edicao
				WHERE descritor_grp IS NULL 
					AND dtt_inativo IS NULL 
					" . $strClausula . "
					AND nome_tabela = 'sys_descritor_campos_edicao'
			    ORDER BY dlg_grp, ordem ";
				//echo($strSQL);
		$objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

$strBGColor = CL_CORLINHA_1; // Definição da cor inicial
$strRotuloGRP = "";          // Inicialização da variavel de nomes de grupo

if($objResult->rowCount() > 0){
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function AbreJanelaPAGE_LOCAL(pr_link, pr_extra){
  var auxStrToChange, rExp, auxNewExtra, auxNewValue;
  if (pr_extra != ""){
   rExp = /:/gi;
   auxNewExtra = pr_extra
   if(pr_extra.search(rExp) != -1){
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

function callUploader(prFormName, prFieldName, prDir){
	strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir;
	AbreJanelaPAGE(strLink, "570", "270");
}

function setFormField(formname, fieldname, valor){
	if ((formname != "") && (fieldname != "") && (valor != "")){
    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  	}
}
//-->
</script>
</head>
<body style="margin:0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
		<table border="0" cellpadding="2" cellspacing="0" width="100%">
			<?php	
					foreach($objResult as $objRS){
						
						$strComponente = "dbvar_" . strtolower(getValue($objRS,"tipo")) . "_" . getValue($objRS,"nome");
						$strValor = "";
						$strMarca = "";
						$boolContinua = false;
						
						$strValor = (getValue($objRS,"valor_sistema")) ? getsession(getValue($objRS,"valor_padrao")) : getValue($objRS,"valor_padrao");
						
						if(getValue($objRS,"obrigatorio") == "1"){
							$strComponente .= "ô"; 
							$strMarca = "*"; 
						}
						
						/****** Se for AUTO então usa um controle oculto ******/
						if( (!$boolContinua) && (strpos(getValue($objRS,"classe"), "EDIT") !== false) && (strpos(getValue($objRS,"param_edit_type"), "hidden") !== false)) {
							echo("<input name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" type=\"hidden\" value=\"" . $strValor . "\">");
							$boolContinua = true;
						}
						
						/********** Senão usa os visíveis **********/
						if(!$boolContinua){
							
							$strClasse = getValue($objRS,"classe") ;
							
							
							echo("
							     <tr bgcolor=\"" . $strBGColor . "\">
									<td width=\"1%\" nowrap align=\"right\"><b>" . $strMarca . getTText(getValue($objRS,"rotulo"),C_UCWORDS) . ":</b>&nbsp;</td>
									<td>
								");
								
							switch($strClasse){								
								/****************** EDITs *******************/
								case "EDIT":
									echo("<input name=\"" . $strComponente . "\" id=\"" . $strComponente . "\"");
									
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
									echo(" value=\"" . $strValor . "\" type=\"" . getValue($objRS,"param_edit_type") . "\" size=\"" . getValue($objRS,"param_edit_size") . "\" maxlength=\"" . getValue($objRS,"param_edit_maxlength") . "\"");
									// Se for DATE coloca função para formatar entrada
									// Se for NUM coloca função para permitir a digitação de números apenas
									// Se for MOEDA ou MOEDA4CD coloca função para permitir a digitação de números em ponto flutuante apenas
									if(getValue($objRS,"tipo") == "DATE" || getValue($objRS,"tipo") == "DATETIME") { echo(" onkeyUp=\"Javascript:FormataInputData('formeditor_" . $strDialogGrp . "', '" . $strComponente . "');\" onkeypress='validateNumKey();'"); }
									if(getValue($objRS,"tipo") == "NUM"     ) { echo(" onkeypress=\"validateNumKey();\""); }
									if(getValue($objRS,"tipo") == "MOEDA"   ) { echo(" onkeypress=\"validateFloatKey();\""); }
									if(getValue($objRS,"tipo") == "MOEDA4CD") { echo(" onkeypress=\"validateFloatKey();\""); }
									echo(">"); 
								break;//*/
								
								/******************* FILEs *******************/
								case "FILE":
									echo("<input type=\"text\" name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" value=\"" . $strValor . "\" size=\"30\">
										  <input type=\"button\" name=\"btn_uploader\" value=\"Upload\" class=\"inputclean\" onClick=\"callUploader('formeditor','" . $strComponente ."','\\\\" . str_replace("profx_","",CFG_DB) . "\\\\upload\\\\');\">
										");
								break;//*/
								
								/******************* COMBOs *******************/
								case "COMBO":
									echo("<select name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" size=\"1\"");
									if(getValue($objRS,"param_combo_disabled") == "1" ) { echo(" disabled"); }
									echo(">");
									if(getValue($objRS,"param_combo_nullable") == "1" ) { echo("<option value=\"\" selected></option>") ; }
									if(getValue($objRS,"param_combo_select") != "") {
										echo(montaCombo($objConn, getValue($objRS,"param_combo_select"), getValue($objRS,"param_combo_select_values"), getValue($objRS,"param_combo_select_captions"), $strValor, getValue($objRS,"param_combo_select_group"))); 
									}
									if((getValue($objRS,"param_combo_values") != "") && (getValue($objRS,"param_combo_captions") != "")) {
										if(getValue($objRS,"param_combo_select") != "" && getValue($objRS,"param_combo_select_group") != "") {
											echo("<optgroup label=\"" . getTText("outros",C_TOUPPER) . "\">");
										}
										$arrValues   = explode(";", getValue($objRS,"param_combo_values"));
										$arrCaptions = explode(";", getValue($objRS,"param_combo_captions"));
										
										$intI = 0;
										foreach($arrValues as $strArrValues){
											echo("<option value=\"" . trim($strArrValues) . "\"");
											if(strval(trim($strValor) . "") == trim(strval($strArrValues) . "") ) { echo(" selected"); }
											echo(">" . getTText(trim($arrCaptions[$intI]),C_UCWORDS) . "</option>");
											$intI++;
										}
									}
									echo("</select>");
								break;//*/
								
								/******************* MEMOs ********************/
								case "MEMO":
									echo("<textarea name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" cols=\"" . getValue($objRS,"param_memo_cols") . "\" rows=\"3\">" . $strValor . "</textarea>");
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
										if(getValue($objRS,"tipo") == "NUM"     ) {
											if(getValue($objRS,"classe") == "COMBO" && getValue($objRS,"param_combo_select") != ""){
												$objResultLocal = $objConn->query(getValue($objRS,"param_combo_select"));
												foreach($objResultLocal as $objRSLocal){
													if($objRSLocal[getValue($objRS,"param_combo_select_values")] == $strValor) {
														$strValor = $objRSLocal[getValue($objRS,"param_combo_select_captions")];
													} 
												}
											}
										}
									}
									echo($strValor);
								break;//*/
								
								/******************* RADIOs *******************/
								case "RADIO": 
									$arrValues   = explode(";", getValue($objRS,"param_radio_values")."");
									$arrCaptions = explode(";", getValue($objRS,"param_radio_captions")."");
									$intI = 0;
									foreach($arrValues as $strArrValues){
										echo("<input name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" type=\"radio\" value=\"" . trim($strArrValues) . "\"");
										if(strval($strValor) == strval($strArrValues)) { echo(" checked"); }
										echo(" class=\"inputclean\">" . getTText(trim($arrCaptions[$intI]),C_UCWORDS));
										$intI++;
									}
								break;//*/
								
								/******************* CHECKs *******************/
								case "CHECK":
									echo("<input name=\"" . $strComponente . "\" id=" . $strComponente . " type=\"radio\" value=\"true\"");
									if($strValor == true || $strValor == "true") { echo(" checked"); }
									echo(" class='inputclean'>" . getTText("sim",C_UCWORDS));
									
									echo("<input name=\"" . $strComponente . "\" id=" . $strComponente . " type=\"radio\" value=\"false\"");
									if($strValor == false || $strValor == "false") { echo(" checked"); }
									echo(" class=\"inputclean\">" . getTText("nao",C_UCWORDS));
								break;//*/
								
								/******************* CHECK_MULTIPLO ******************* /
								case "CHECK_MULTIPLO": 
									$arrValues   = explode(";",getValue($objRS,"param_check_values")."");
									$arrCaptions = explode(";",getValue($objRS,"param_check_captions")."");
										
									$intJ = 0;
									foreach($arrValues as $strArrValues){
										//echo("<input name='" . $strComponente . "' class='arial11' type='checkbox' value='" . Trim(arrVALUES(j)) . "'")
										//if(CStr($strValor . "") == trim(CStr(arrVALUES(j)) . "") ) { echo(" checked"); }
										echo("<input name='" . $strComponente . "' type='checkbox' value='" . trim($strArrValues) . "'");
										if(strpos(strval($strValor . ""), trim(strval($strArrValues) . "")) > 0 ) { echo(" checked"); }
										echo(">" . trim($arrCaptions($intJ)));	
										$intJ++;
									}
								break;//*/
								
							}
							
							/******************* ADD *******************/
							if(getValue($objRS,"param_add_link") != "" && ($strOperacao != "VIE" && $strOperacao != "DEL")) {
								$strLink = getValue($objRS,"param_add_link");
																
								(strpos($strLink,"?")) ? $strLink .= "&" : $strLink .= "?"; // verifica se já tem uma querystring no link e coloca o caracter adequado
								$strLink .= "var_chavereg=" . $intCodDado;                  // coloca o código do item corrente para que seja usado na proxima página
								
								echo("<a href=\"javascript:AbreJanelaPAGE_LOCAL('" . $strLink . "','" . getValue($objRS,"param_add_extra") . "');\"><img src='" . getValue($objRS,"param_add_img") . "' border=\"0\" hspace=\"5\" align=\"absmiddle\"></a>");
							}//*/
							
							/******************* SEARCH SQL *******************/
							if(getValue($objRS,"search_query") != "" && ($strOperacao != "VIE" && $strOperacao != "DEL")){
								echo("<a href=\"javascript:AbreJanelaPAGE_LOCAL('resultaslw.php?var_coditem=".getValue($objRS,"cod_descr_campo")."&var_fieldname=" . $strComponente . "&var_dialog_grp=" . $strDialogGrp . "','');\"><img src=\"../img/icon_zoom.gif\" border=\"0\" hspace=\"5\" align=\"absmiddle\"></a>");
							}//*/
							
							/******************* SEARCH DEFAULT *******************/
							if(getValue($objRS,"searchpad_modulo") != "" && ($strOperacao != "VIE" && $strOperacao != "DEL")){
								echo("<a href=\"javascript:AbreJanelaPAGE_LOCAL('../" . getValue($objRS,"searchpad_modulo") . "/?var_acao=single&var_fieldname=" . $strComponente . "&var_formname=formeditor','');\"><img src=\"../img/icon_zoom.gif\" border=\"0\" hspace=\"5\" align=\"absmiddle\"></a>");
							}//*/
							
							echo("
							      &nbsp;&nbsp;<span class=\"comment_med\">" . getValue($objRS,"obs") . "</span></td>\n
								  </tr>\n
								");	
							
							/*** Troca cor de fundo da linha ****/
							$strBGColor = ($strBGColor == CL_CORLINHA_2) ? CL_CORLINHA_1 : CL_CORLINHA_2;
							
							$boolContinua = true;
						}
					}
			?>
		</table>
   </td>
  </tr>
</table>
</body>
</html>
<?php
} 

$objResult->closeCursor();
$objConn = NULL;
?>