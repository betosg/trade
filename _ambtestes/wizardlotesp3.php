<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strTabelas = request("var_buffer_tabelas");
$strJoin    = request("var_join");

$objConn = abreDBConn(CFG_DB);

(strpos($strTabelas,",") !== false) ? $strColumn = "(table_name || '.' || column_name)" : $strColumn = "column_name";
$strListFields = montaCombo($objConn," SELECT " . $strColumn . " AS column FROM information_schema.columns WHERE table_name IN (" . $strTabelas . ") ","column","column","");


?>
<html>
<head>
<title>PROEVENTO STUDIO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function submeterForm(){
	var objItens  = document.formwizard.var_itens;
	var objHidden = document.formwizard.var_buffer;
	
	for(i = 0; i < objItens.options.length; i++) {
		objHidden.value += objItens.options[i].value + " ";
	}
	
	document.formwizard.submit();
}

function newItem() {
	var objCampo  = document.formwizard.var_campo;
	var objQuant  = document.formwizard.var_quant;
	var objItens  = document.formwizard.var_itens;
	var objBuffer = document.formwizard.var_buffer;
	var strValor  = document.formwizard.var_valor.value;
	var strTipo   = document.formwizard.var_tipo.value;
	
	var retValue 	   = "";
	var intAux   	   = 0;
	var strTextQuant   = "";
	var strValueQuant  = "";
	var strNotText     = "";
	var strNotValue    = "";
	var strClauseText  = "";
	var strClauseValue = "";
	
	for(i = 0; i < objQuant.options.length; i++) {
		if(objQuant.options[i].selected == true && objQuant.options[i].value != ""){
			if(objQuant.options[i].value != "NOT"){
				strTextQuant  += objQuant.options[i].text + " ";
				strValueQuant += objQuant.options[i].value;
				intAux++;
				if(!validateQuant(strValueQuant.replace(" ",""))){
					alert("Por favor, preencha com os quantificadores corretos");
					return;
				}
			}
			else{
				strNotText = " NÃO";
				strNotValue = " NOT "
				intAux++;
			}
 		}
	}
	
	switch(strTipo){
		case "num":
			strRegExp = /\d|(\d( +)(e|E)?( +)\d)/
			if(strRegExp.exec(strValor)){ mixValor = strValor.replace(" E "," AND "); }	else { alert("Por favor, digite um valor válido para esse campo.");	return;	}
			break;
		case "str":
			mixValor = "'" + strValor + "'";
			break;
		case "date":
			strValor = strValor.toUpperCase();
			(strValor.indexOf(" E ") > -1) ? mixValor = "'" + strValor.replace(" E ","' AND '") + "'" : mixValor = "'" + strValor + "'";
			break;
	}
	
	if(objItens.options.length != 0){
		strClauseText  = "E ";
		strClauseValue = "AND ";
	}
	
	strClauseText  += objCampo.options[objCampo.selectedIndex].text + strNotText + " " + strTextQuant + strValor;
	strClauseValue += strNotValue + objCampo.options[objCampo.selectedIndex].value + " " + strValueQuant + " " + mixValor;
	
	var temp = new Option(strClauseText, strClauseValue);
	
    objItens.options[objItens.options.length] = temp;
}

function removeItem(){
	var objItens  = document.formwizard.var_itens;
	
	for(i = 0; i < objItens.options.length; i++) {
		if(objItens.options[i].selected == true){
			objItens.options[i] = null;
			
			if(i == 0 && objItens.options[0] != null){
				objItens.options[0].value = objItens.options[0].value.substr(4);
				objItens.options[0].text = objItens.options[0].text.substr(2);
			}
		}
	}
}

function validateQuant(prValue){
	var objRegExp = /^=$|^>=?$|^<=?$|^LIKE$|^IN$|^BETWEEN$|^<>$/;
	return(objRegExp.exec(prValue));
}

function newItemConsulta() {
	var objCampo = document.formwizard.var_campo_consulta;
	var objItens  = document.formwizard.var_itens_consulta;
	var boolTemCampo;
	
	for(i = 0; i < objCampo.options.length; i++) {
		if(objCampo.options[i].selected){
			for(j = 0; j < objItens.options.length; j++) {
				boolTemCampo = (objCampo.options[i].value == objItens.options[j].value);
				if(boolTemCampo) { break; }
			}
			
			if(!boolTemCampo){
				var temp = new Option(objCampo.options[i].text, objCampo.options[i].value);
				objItens.options[objItens.options.length] = temp;
				objCampo.options[i].style.backgroundColor = "#AFD987";
				break;
			}
 		}
	}
}

function removeItemConsulta(){
	var objCampo  = document.formwizard.var_campo_consulta;
	var objItens  = document.formwizard.var_itens_consulta;
	
	for(i = 0; i < objItens.options.length; i++) {
		if(objItens.options[i].selected == true){
			for(j = 0; j < objCampo.options.length; j++) {
				if(objItens.options[i].value == objCampo.options[j].value){
					objCampo.options[j].style.backgroundColor = "#FFFFFF";
				}
			}
			objItens.options[i] = null;
		}
	}
}

function showContainer(prContainer){
	if(prContainer == "campos_consulta"){
		document.getElementById("campos_consulta").style.display = ""
		document.getElementById("filtros_consulta").style.display = "none";
	}
	else{
		document.getElementById("filtros_consulta").style.display = "";
		document.getElementById("campos_consulta").style.display = "none"
	}
}
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="middle">
	<?php athBeginFloatingBox("700","none","Assistente de Lotes - PROEVENTO STUDIO","#AFD987"); ?>
		<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
		  <form name="formwizard" action="editconfigexec.php" method="post">
		   <input type="hidden" name="var_buffer" value="">
			<tr>
				<td width="1%"><img src="wizard_lotes.jpg"></td>
				<td align="center" valign="top">
					<table width="99%" border="0" cellspacing="0" cellpadding="0">	
						<tr>
							<td colspan="2" valign="top"><h3>Selecione os campos e os critérios de pesquisa</h3></td>
						</tr>
						<tr>
							<td height="20" bgcolor="#CCCCCC" onClick="showContainer('campos_consulta')" style="padding-left:10px;"><b> - Campos da pesquisa</b></td>
						</tr>
						<tr>
							<td>
								<table id="campos_consulta" width="100%" border="0" cellspacing="0" cellpadding="2">
									<tr>
										<td align="left" valign="top" width="1%">
											<select name="var_campo_consulta" size="13" style="width:200px;" onDblClick="newItemConsulta();">
												<?php echo($strListFields); ?>
											</select>
										</td>
										<td align="center">
											<input type="button" value=" &gt;&gt; " class="inputclean" onClick="newItemConsulta();"><br>
											<input type="button" value=" &lt;&lt; " class="inputclean" onClick="removeItemConsulta();">
										</td>
										<td align="center" valign="top" width="98%">
											<select name="var_itens_consulta" size="13" style="width:100%;">
											</select><br>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td height="20" bgcolor="#CCCCCC" onClick="showContainer('filtros_consulta')" style="padding-left:10px;"><b> - Filtros da pesquisa</b></td>
						</tr>
						<tr>
							<td>
								<table id="filtros_consulta" width="100%" border="0" cellspacing="0" cellpadding="2" style="display:none">
									<tr>
										<td align="left" valign="top" width="1%">
											<select name="var_campo" size="13" style="width:150px;">
												<?php echo($strListFields); ?>
											</select>
										</td>
										<td valign="top" align="center" width="1%">
											<select name="var_quant" multiple size="13" style="width:80px">
												<option value="NOT">NÃO É/ESTÁ</option>
												<option>----------</option>
												<option value=">">MAIOR</option>
												<option value="<">MENOR</option>
												<option value="=">IGUAL A</option>
												<option value="<>">DIFERENTE DE</option>
												<option>----------</option>
												<option value="BETWEEN">ENTRE</option>
												<option value="IN">EM</option>
												<option value="VALUE">COMO</option>
											</select>
										</td>
										<td align="center" valign="top" width="98%">
											<select name="var_itens" size="13" style="width:100%;">
											</select><br>
										</td>
									</tr>
									<tr>
										<td colspan="2" align="center" valign="bottom">
											<input type="text" name="var_valor" size="25">
											<select name="var_tipo" style="width:80px;">
												<option value="str">texto</option>
												<option value="num">numero</option>
												<option value="date">data</option>
											</select>
										   <input type="button" value=" Ok " class="inputclean" onClick="newItem()">
										</td>
										<td align="center">
											<input type="button" value="remover" onClick="removeItem();" class="inputclean">
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<table width="95%" border="0" cellspacing="0" cellpadding="4">
						<tr><td height="5" colspan="3"></td></tr>
						<tr><td height="1" colspan="3" bgcolor="#DBDBDB"></td></tr>
						<tr>
							<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
								<button onClick="location.href='<?php echo(getsession($strSesPfx . "_grid_default")); ?>';"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
								<button onClick="submeterForm();">Avançar >></button>
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