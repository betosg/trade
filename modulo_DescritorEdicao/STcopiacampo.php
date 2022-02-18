<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
	
	$intCodDado  = request("var_chavereg");
	
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "COPY_CAMPO");
	
	$objConn   = abreDBConn(CFG_DB);
	
	/*--------- Define qual página chamar depois -----------*/
	
	$strButtons = "<button onClick=\"javascript:submeter();return false;\">" . getTText("ok",C_UCWORDS)  . "</button>
				   <button onClick=\"javascript:cancelar();return false;\">" . getTText("cancelar",C_UCWORDS) . "</button>";
	
	$strAviso2 = getTText("aviso_copy_campo",C_NONE);

	/*---------------------------------------------------------------------------------
	 Nome do campo-chave da tabela para ser usado no athInsertToDB/athUpdateToDB
	 ---------------------------------------------------------------------------------*/
	try{ 
		$strSQL = " SELECT nome, nome_tabela 
					FROM sys_descritor_campos_edicao 
				    WHERE cod_app = " . getsession($strSesPfx . "_chave_app") . "
				    AND (descritor_grp = '" . getsession($strSesPfx . "_descritor_grp") . "' OR descritor_grp IS NULL)
				    AND classe = 'CHAVE' ";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	foreach($objResult as $objRS){	
		$strCampoChave = getValue($objRS,"nome");
		$strNomeTabela = getValue($objRS,"nome_tabela");
	}

	/*---------------------------------------------------------------------------------
	 Campos da tabela para ser usado no athInsertToDB e athUpdateToDB
	----------------------------------------------------------------------------------*/
	try{
		$strSQL = " SELECT nome, rotulo, descricao, tipo, classe, obrigatorio, obs, rotulo_grp 
						, param_edit_type, param_edit_size, param_edit_maxlength 
				        , param_combo_nullable, param_combo_disabled, param_combo_select, param_combo_select_values, param_combo_select_captions, param_combo_values, param_combo_captions, param_combo_select_group 
				        , param_memo_rows, param_memo_cols
				        , param_radio_values, param_radio_captions
						, file_dir_arquivos
				        , param_add_img, param_add_link, param_add_extra
					FROM sys_descritor_campos_edicao
					WHERE cod_app = " . getsession($strSesPfx . "_chave_app") . "
						AND (descritor_grp = '" . getsession($strSesPfx . "_descritor_grp") . "' OR descritor_grp IS NULL)
						AND dtt_inativo IS NULL 
						AND ((operacao = 'UPD') OR (operacao IS NULL))
						AND classe <> 'CHAVE'
				    ORDER BY ordem LIMIT 10 ";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	$strBGColor = CL_CORLINHA_2;
	$strRotuloGRP = "";
	$strComponenteFoco = ""; 
	
	if($objResult->rowCount() > 0){
		
		$strRotulo = getsession($strSesPfx . "_titulo");
		if($intCodDado != ""){
			try{
				$strSQL = " SELECT * 
							FROM  " . $strNomeTabela .
						  " WHERE " . $strCampoChave . " = " . $intCodDado;
				$objRS2  = $objConn->query($strSQL)->fetchAll();
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
		
		/* Aqui ele testa se o resultado do result set é um array vazio, ou seja, não encontrou nada na consulta, caso for qualquer operação menos inserção. 
		No caso da inserção ele entra porque a variável que iria receber o result set foi inicializada com "". */
	    if($objRS2 !== array()){ 

?>
<html>
<head>
<title><?php echo(getTText("copia_campo",C_UCWORDS)); ?></title>
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
  AbreJanelaPAGE(pr_link, "680", "350");
}

function setFormField(formname, fieldname, valor){
	if ((formname != "") && (fieldname != "") && (valor != ""))
	{
    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  	}
}
		
function submeter(){
	document.formeditor.submit();
}

function cancelar(){
	window.parent.frames["<?php echo(CFG_SYSTEM_NAME . "_left"); ?>"].document.formeditor_000.submit();
	return(false);
}

function callUploader(prFormName, prFieldName, prDir){
	strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir;
	AbreJanelaPAGE(strLink, "570", "270");
}
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("600","none",getTText($strRotulo,C_TOUPPER)." (". getTText("copiar_campo",C_UCWORDS) . ")",CL_CORBAR_GLASS_1); ?>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
	   <form name="formeditor" action="STcopiacampoexec.php" method="post">
		<input type="hidden" name="RECORD_KEY_VALUE" value="<?php echo($intCodDado); ?>">
		<tr><td height="22" style="padding:10px">&nbsp;</td></tr>
		<tr> 
		  <td align="center" valign="top">
			<table width="550" border="0" cellspacing="0" cellpadding="4">
				<?php
						/*echo("
							 <tr>
								<td align=\"right\">
									<label><strong>*" . getTText($strCampoChave,C_UCWORDS) . " " . getTText("do_copiado",C_UCWORDS) . ":</strong></label>&nbsp;
								</td>
								<td>" . $intCodDado . "</td>
							 </tr>
							");*/
					
					foreach($objResult as $objRS){
						/****************** Faz ou desfaz marcação de grupos de controles ******************/
						
						if($strRotuloGRP != getValue($objRS,"rotulo_grp")){
							$strRotuloGRP = getValue($objRS,"rotulo_grp");
							
							if($strRotuloGRP != ""){
								echo("
									 <tr><td colspan=\"2\" align=\"left\" valign=\"top\">
									 <span><strong>" . $strRotuloGRP . "</strong></span>
									 </td></tr>");
							}
						}
						
						$strComponente = "dbvar_" . strtolower(getValue($objRS,"tipo")) . "_" . getValue($objRS,"nome");
						$strValor = "";
						$strMarca = "";
						$boolContinua = false;
						
						
						$strValor = $objRS2[0][getValue($objRS,"nome")]; //porque tenho que pegar apenas uma coluna por vez, foi feito um fetchAll no objRS2
						if(getValue($objRS,"param_add_link") != ""){
							$strValor = html_entity_decode($strValor . "");
						}
						

						
						if(getValue($objRS,"obrigatorio") == "1"){
							$strComponente .= "ô"; 
							$strMarca = "*"; 
						}
						
						/****** Se for AUTO então usa um controle oculto ******/
						//echo($strComponente . ": " . strpos(getValue($objRS,"tipo"), "AUTO")."<br>");
						if( (!$boolContinua) && (strpos(getValue($objRS,"tipo"), "AUTO") !== false) ) {
							echo("<input name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" type=\"hidden\" value=\"\">");
							$boolContinua = true;
						}
						
						/****** Se o campo for TIPO ou CODIGO então não exibe ******/
						if(getValue($objRS,"rotulo") == "tipo" || getValue($objRS,"rotulo") == "codigo"){
							$boolContinua = true;
						}
						
						/********** Senão usa os visíveis **********/
						if(!$boolContinua){
							
							//$strClasse = getValue($objRS,"classe");
							$strClasse = "LABEL";
							
							/****** Guarda nome do componente para colocar foco ******/
							if ($strComponenteFoco == "") { 
								$strComponenteFoco = $strComponente;
								if($strClasse == "LABEL") { $strComponenteFoco = ""; }
								if($strClasse == "COMBO" && getValue($objRS,"param_combo_disabled") == "0") { $strComponenteFoco = ""; } 
							}
							
							
							echo("
							     <tr bgcolor=\"" . $strBGColor . "\">
									<td width=\"1%\" nowrap align=\"right\" valign=\"top\">
										<label><strong>" . $strMarca . getTText(getValue($objRS,"rotulo"),C_UCWORDS) . ":</strong></label>&nbsp;
									</td>
									<td>
								");
								
							switch($strClasse){								
								
								/******************* LABELs *******************/
								case "LABEL":
									if($strValor != "") {
										if(getValue($objRS,"tipo") == "DATE"    ) { $strValor = dDate(CFG_LANG, $strValor, false);}
										if(getValue($objRS,"tipo") == "DATETIME") { $strValor = dDate(CFG_LANG, $strValor, true); }
										if(getValue($objRS,"tipo") == "AUTODATE") { $strValor = dDate(CFG_LANG, $strValor, true); }
										if(getValue($objRS,"tipo") == "EMAIL"   ) { $strValor = "<a href=\"mailto:" . $strValor . "\">" . $strValor . "</a>"; }
										if(getValue($objRS,"tipo") == "LINK"    ) { $strValor = "<a href=\"" . $strValor . "\" target=\"_blank\">" . $strValor . "</a>"; }
										if(getValue($objRS,"tipo") == "ARQUIVO" ) { $strValor = "<a href=\"../" . CFG_USR_DIR_UPLOAD_ARQ . "/" . $strValor . "\" target=\"_blank\">" . $strValor . "</a>"; }
										if(getValue($objRS,"tipo") == "MOEDA"   ) { $strValor = number_format((double) $strValor, 2); }
										if(getValue($objRS,"tipo") == "MOEDA4CD") { $strValor = number_format((double) $strValor, 4); }
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

							
							}
							
							/******************* Demais recursos dos componentes *******************/
							if(getValue($objRS,"param_add_link") != "" && ($strOperacao != "VIE" && $strOperacao != "DEL")) {
								if(strpos(getValue($objRS,"param_add_link"),"SQL:") === 0){
									$strLink = "resultaslw.php?var_sql=" . trim(str_replace("SQL:","",getValue($objRS,"param_add_link")));
								}
								else{
									$strLink = getValue($objRS,"param_add_link");
								}
								echo("<a href=\"javascript:AbreJanelaPAGE_LOCAL('" . $strLink . "','" . getValue($objRS,"param_add_extra") . "');\"><img src='" . getValue($objRS,"param_add_img") . "' hspace='5' border='0' align='absmiddle'></a>");
							}
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
				<tr align="left">
				  <td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px">&nbsp;</td>
				</tr>
				<tr><td colspan="2" class="linedialog"></td></tr>
				<tr> 
				  <td colspan="2">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
						   <td style="padding:10px 0px 10px 10px; vertical-align:top;">
					<?php 
						if($strAviso2 != ""){
							echo("
								  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
									<tr>
										<td width=\"1%\" valign=\"top\"><img src=\"../img/mensagem_aviso.gif\" border=\"0\" hspace=\"10\"></td>
										<td>" . $strAviso2 . "</td>
									</tr>
									<tr>
										<td colspan=\"2\" align=\"right\" style=\"padding-top:10px;\">" . $strButtons . "</td>
									</tr>
								  </table>
								");
						}
					?>
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
        } 
    }
	
	
	$objResult->closeCursor();
	$objConn = NULL;
	
	/* if($strComponenteFoco != ""){
		echo("	<script type=\"text/javascript\" language=\"JavaScript\">
					ATHSetFocus(\"formeditor\", \"" . $strComponenteFoco . "\");
				</script>");
	} */
?>