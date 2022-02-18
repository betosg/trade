<?php
try{	
	$strSQL  = " SELECT cod_descr_campo, nome, rotulo, descricao, tipo, classe, obrigatorio, obs, rotulo_grp, nome_tabela
					, param_edit_type, param_edit_size, param_edit_maxlength 
			        , param_combo_nullable, param_combo_disabled, param_combo_select, param_combo_select_values, param_combo_select_captions, param_combo_values, param_combo_captions, param_combo_select_group,param_combo_width
			        , param_memo_rows, param_memo_cols
			        , param_radio_values, param_radio_captions
			        , param_add_img, param_add_link, param_add_extra
					, js_eventos, js_funcoes, valor_padrao, valor_sistema
				FROM sys_descritor_campos_edicao
				WHERE cod_app = " . getsession($strSesPfx . "_chave_app") . "
					AND (descritor_grp = '" . getsession($strSesPfx . "_descritor_grp") . "' OR descritor_grp IS NULL)
					AND dtt_inativo IS NULL 
					AND operacao = 'FIL'
			    ORDER BY ordem ";
	$objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

if($objResult->rowCount() > 0){
athBeginFloatingBox("205","",getTText("filtrar_por",C_NONE) . "...",CL_CORBAR_GLASS_2); ?>
<script>
	function collapseMenu(prIndex){
		if(document.getElementById(prIndex).style.display == "block"){
			document.getElementById(prIndex).style.display = "none";
			document.getElementById("menu_img_" + prIndex).src = "../img/collapse_generic_close.gif";
		}
		else{
			document.getElementById(prIndex).style.display = "block";
			document.getElementById("menu_img_" + prIndex).src = "../img/collapse_generic_open.gif";
		}
	}
	
	function submeterForm(){
		document.formeditor_000.submit();
	}
	
	function abreJanelaPageLocal(pr_link, pr_extra){
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

	function setFormField(formname, fieldname, valor){
		if ((formname != "") && (fieldname != "") && (valor != "")){
	    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
	  	}
	}
	
	function resetSearchField(prFieldName,prFieldLabel){
		document.getElementById(prFieldName).value = "";
		document.getElementById(prFieldLabel).innerHTML = "<?php echo(getTText("selecione",C_UCWORDS)."..."); ?>";
	}
	<?php
		if(getsession($strSesPfx . "_field_detail") != ''){
	?>
			self.parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME); ?>_principal").cols = "10,*";
	<?php
		}
	?>
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<form name="formeditor_000" action="dataprepare.php" method="post" target="<?php echo(CFG_SYSTEM_NAME . "_main"); ?>">
<?php
	$strValorPadrao = ""; 
	$strRotuloGRP = "";
	$strSetDisabled = "";
	
	foreach($objResult as $objRS){
		if($strRotuloGRP != getValue($objRS,"rotulo_grp")){
			if($strRotuloGRP != ""){
				echo("
						</table>
					</td>
				</tr>
					");
			}
			
			$strRotuloGRP = getValue($objRS,"rotulo_grp");
			
			echo("
				<tr>
					<td colspan=\"2\" align=\"left\" bgcolor=\"#DBDBDB\" height=\"16\" onClick=\"collapseMenu('" . $strRotuloGRP . "');\">
						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
							<tr>
								<td width=\"99%\" style=\"padding-left:5px\"><b>" . $strRotuloGRP . "</b></td>
								<td width=\"1%\"  style=\"padding-right:5px\"><img src=\"../img/collapse_generic_close.gif\" id=\"menu_img_" . $strRotuloGRP . "\"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td colspan=\"2\" height=\"5\"></td></tr>
				<tr> 
					<td colspan=\"2\">
						<table id=\"" . $strRotuloGRP . "\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"display:none\">
				");
		}
		
		/*----------- Faz ou desfaz marcação de grupos de controles ------------*/
		
		if(getValue($objRS,"tipo") == "STR" && getValue($objRS,"classe") == "COMBO"){
			$strComponente = "var_" . strtolower(getValue($objRS,"tipo")) . "eq_" . getValue($objRS,"nome_tabela") . "__" . getValue($objRS,"nome");
		}
			elseif(getValue($objRS,"nome_tabela") != ""){
				$strComponente = "var_" . strtolower(getValue($objRS,"tipo")) . "_" . getValue($objRS,"nome_tabela") . "__" . getValue($objRS,"nome");
			}
			else{
				$strComponente = "var_" . strtolower(getValue($objRS,"tipo")) . "_" . getValue($objRS,"nome");

		}
		$strValor = "";
		$strMarca = "";
		$boolContinua = false;
		
		
		if(getValue($objRS,"obrigatorio") == "1"){
			$strComponente .= "ô"; 
			$strMarca = "*"; 
		}
		
		/*----- Se for AUTO então usa um controle oculto -----*/
		//echo($strComponente . ": " . strpos(getValue($objRS,"tipo"), "AUTO")."<br>");
		if( (!$boolContinua) && (strpos(getValue($objRS,"tipo"), "AUTO") !== false) ) {
			echo("<input name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" type=\"hidden\" value=\"\">\n");
			$boolContinua = true;
		}
		/*----------- Senão usa os visíveis -----------*/
		if($boolContinua == false){
			
			$strClasse = getValue($objRS,"classe");
			
			echo("
				 <tr>
					<td align=\"left\" valign=\"top\" style=\"padding-left:5px;\">
					<label for=\"" . $strComponente . "\">" . $strMarca . getTText(getValue($objRS,"rotulo"),C_UCWORDS) . ":</label>&nbsp;
					<br>
				");
				
			/********* Pega todos os eventos e ações e monta as chamadas *********/
			$strEventos = getValue($objRS,"js_eventos");
			$strFuncoes = getValue($objRS,"js_funcoes");
			
			$arrEventos = explode(";", $strEventos);
			$strChamadas = " ";
			
			foreach($arrEventos as $strEvento){
				$strEvento = trim($strEvento);
				$strFuncao = getTextBetweenTags($strFuncoes, "[", "]", $PosIni, $PosFim);
				if (($strEvento != "") && ($strFuncao != "")) {
					$strChamadas .= $strEvento . "=\"Javascript:" . $strFuncao . "\" ";
				$strFuncoes = substr_replace($strFuncoes, "", $PosIni, $PosFim - $PosIni);
				}
			}
			
			$strValorPadrao = getValue($objRS,"valor_padrao");
			$strSetDisabled = "";
			if(getsession($strSesPfx . "_field_detail") != '')
				if(getValue($objRS,"nome") == getsession($strSesPfx . "_field_detail")){
					$strValorPadrao = (getValue($objRS,"valor_sistema")) ? getsession(getValue($objRS,"valor_padrao")) : getValue($objRS,"valor_padrao");
					$strSetDisabled = "readonly='readonly'";
				}
			
			switch($strClasse){
				
				/****************** EDITs *******************/
				case "EDIT":
					echo("<input ".$strSetDisabled." name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" type=\"" . getValue($objRS,"param_edit_type") . "\" size=\"" . getValue($objRS,"param_edit_size") . "\" maxlength=\"" . getValue($objRS,"param_edit_maxlength") . "\" value=\"" . $strValorPadrao . "\" ");
					echo(" " . $strChamadas);
					echo(">");
				break;//*/
				
				/******************* COMBOs *******************/
				case "COMBO":
					$strValor = $strValorPadrao;
					echo("<select name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" size=\"1\"");
					echo(" " . $strChamadas);
					if(getValue($objRS,"param_combo_width") != 0){ echo("style=\"width:".getValue($objRS,"param_combo_width")."px\""); }
					if(getValue($objRS,"param_combo_disabled") == "1" ) { echo(" disabled"); }
					echo(">");
					if(getValue($objRS,"param_combo_nullable") == "1" ) { 
						echo("<option value=\"\"");
						if( $strValorPadrao == "") echo "selected";
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
						
						$intI = 0;
						foreach($arrValues as $strArrValues){
							$strPattern = '{{system_name}}';
							$strArrValuesReplaced =  $strArrValues;
							$strArrValues = preg_replace($strPattern, CFG_SYSTEM_NAME, $strArrValuesReplaced);
							
							echo("<option value=\"" . trim($strArrValues) . "\"");
							if(trim(substr($strValor,0,6)) == '[text]'){
								if(trim(strtoupper(strip_tags(getTText(trim($arrCaptions[$intI]),C_UCWORDS)))) == trim(strtoupper(substr($strValor,6,strlen($strValor))))){ echo(" selected"); }
							}
							else {
								if(getValue($objRS,"tipo") == "STATUS") { 
									if ((trim(strval($strValor) . "") == trim(strval($strArrValues) . "")) && (trim(strval($strArrValues) . "") == "I")) echo(" selected");
									if ((trim(strval($strValor) . "") == trim(strval($strArrValues) . "")) && (trim(strval($strArrValues) . "") == "A")) echo(" selected");
								}
								else {
									//aqui verifica pelo nome(caption)  para fazer o combo selected
									if(strval(trim($strValor) . "") == trim(strval($strArrValues) . "")) echo(" selected");
								}
							}
							echo(">" . getTText(trim($arrCaptions[$intI]),C_UCWORDS) . "</option>");
							$intI++;
						}
					}
					echo("</select>");
				break;//*/
				
				/******************* MEMOs ********************/
				case "MEMO": 
					echo("<textarea name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" cols=\"" . getValue($objRS,"param_memo_cols") . "\" rows=\"" . getValue($objRS,"param_memo_rows") . "\"");
					echo(" " . $strChamadas);
					echo(">" . $strValor . "</textarea>");
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
						if(getValue($objRS,"tipo") == "MOEDA"   ) { $strValor = number_format($strValor, 2); }
						if(getValue($objRS,"tipo") == "MOEDA4CD") { $strValor = number_format($strValor, 4); }
					}
					echo($strValor);
				break;//*/
				
				/******************* RADIOs *******************/
				case "RADIO":
					$arrValues   = explode(";", getValue($objRS,"param_radio_values")."");
					$arrCaptions = explode(";", getValue($objRS,"param_radio_captions")."");
					$strValor = $strValorPadrao;
					$intI = 0;
					
					foreach($arrValues as $strArrValues){
						echo("<input name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" type=\"radio\" value=\"" . trim($strArrValues) . "\"");
						//if(strval(trim($strValor) . "") == trim(strval($strArrValues) . "")) { echo(" checked"); }
						if(trim(substr($strValor,0,6)) == '[text]'){
							if(trim(strtoupper(strip_tags(getTText(trim($arrCaptions[$intI]),C_UCWORDS)))) == trim(strtoupper(substr($strValor,6,strlen($strValor))))){ echo(" checked"); }
						}
						else{
							if(getValue($objRS,"tipo") == "STATUS") { 
								if ((trim(strval($strValor) . "") == trim(strval($strArrValues) . "")) && (trim(strval($strArrValues) . "") == "I")) echo(" checked");
								if ((trim(strval($strValor) . "") == trim(strval($strArrValues) . "")) && (trim(strval($strArrValues) . "") == "A")) echo(" checked");
							}
							else {
								if(trim(strval($strValor) . "") == trim(strval($strArrValues) . "")) echo(" checked");
							}
						}
						echo(" class=\"inputclean\">" . getTText(trim($arrCaptions[$intI]),C_UCWORDS) . "&nbsp;&nbsp;");
						$intI++;
					}
				break;//*/
				
				/******************* CHECKs *******************/
				case "CHECK":
					echo("<input name=\"" . $strComponente . "\" id=" . $strComponente . " type=\"radio\" value=\"true\"");
					if($strValor == true ) { echo(" checked"); }
					echo(" class=\"inputclean\">Sim");
					
					echo("<input name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" type=\"radio\" value=\"false\"");
					if($strValor == false ) { echo(" checked"); }
					echo(" class=\"inputclean\">Não");
				break;//*/
				
				/******************* SEARCH_SQL *******************/
				case "SEARCH_SQL":
					echo("
						<div style=\"width:137px; display:inline;\" id=\"ret_000_" . getValue($objRS,"nome") . "\">" . getTText("selecione",C_UCWORDS) . "...</div>
						<input type=\"hidden\" name=\"" . $strComponente . "\" id=\"" . $strComponente . "\" value=\"" . $strValor . "\">
						<a href=\"javascript:abreJanelaPageLocal('resultaslw.php?var_coditem=" .getValue($objRS,"cod_descr_campo")."&var_fieldname=" . $strComponente . "&var_dialog_grp=000','');\"><img src=\"../img/icon_zoom.gif\" border=\"0\" hspace=\"1\" align=\"absmiddle\" title=\"" . getTText("pesquisar",C_UCWORDS) . "\"></a>
						<a href=\"javascript:resetSearchField('" . $strComponente . "','ret_000_" . getValue($objRS,"nome") . "');\"><img src=\"../img/icon_back.gif\" border=\"0\" hspace=\"1\" align=\"absmiddle\" title=\"" . getTText("limpar",C_UCWORDS) . "\"></a>
						");
			
				break;//*/
				
			}
		
			/******************* Demais recursos dos componentes ******************* /
			if(getValue($objRS,"param_add_link") != "") {
				if(strpos(getValue($objRS,"param_add_link"),"SQL:") === 0){
					$strLink = "resultaslw.php?var_sql=" . trim(str_replace("SQL:","",getValue($objRS,"param_add_link")));
				}
				else{
					$strLink = getValue($objRS,"param_add_link");
				}
				
				echo("<a href=\"javascript:AbreJanelaPAGE_LOCAL('" . $strLink . "','" . getValue($objRS,"param_add_extra") . "');\"><img src=\"" . getValue($objRS,"param_add_img") . "\" hspace=\"5\" border=\"0\" align=\"absmiddle\"></a>");
			}//*/
			
			echo("
					</td>
				  </tr>
				  <tr><td colspan=\"2\" height=\"10\"></td></tr>
				");
			
			$boolContinua = true;
			}
	}
	
	if($strRotuloGRP != ""){
				echo("
						</table>
					</td>
				</tr>
					");
	}
?>  
	  </td>
	  	  <input type="image" name="Submit" border="0" style='border:none;cursor:arrow;background:none;width:0px;height:0px' src="../img/transparent.gif">
	</tr>
	</form>
	<tr><td height="1" class="linedialog"></td></tr>
	<tr align="right" valign="middle">
	  <td><br><button onClick="submeterForm();"><?php echo(getTText("aplicar",C_UCWORDS)) ?></button></td>
	</tr>
</table>
<?php
}else{
    //Mesmo que não existam campos para montagem do filtro, precisamos montar o formulário 
	//para que o disparo do SUBMIT seja realizado no onLoad do menu (onde ficam ops filtros - esse include)
	echo("<form name='formeditor_000' action='dataprepare.php' method='post' target='". CFG_SYSTEM_NAME . "_main' ></form>");
}
athEndFloatingBox();

$objResult->closeCursor();
?>