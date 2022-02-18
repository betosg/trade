<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$objConn = abreDBConn(CFG_DB);

$strListTables = montaCombo($objConn," SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name NOT LIKE 'sys_%' ORDER BY 1  ","table_name","table_name","");

?>
<html>
<head>
<title>PROEVENTO STUDIO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
String.prototype.trim = function(){
return this.replace(/^\s*/, "").replace(/\s*$/, "");
}

function submeterForm(){
	var objItens  = document.formwizard.var_itens;
	var objHidden = document.formwizard.var_buffer_tabelas;
	
	for(i = 0; i < objItens.options.length; i++) {
		objHidden.value += objItens.options[i].value + " ";
	}
	
	objHidden.value = "'" + objHidden.value.trim().replace(" ","', '") + "'";
	
	alert(objHidden.value);
	
	document.formwizard.submit();
}

function newItem() {
	var objTabela = document.formwizard.var_tabela;
	var objItens  = document.formwizard.var_itens;
	var boolTemTabela;
	
	for(i = 0; i < objTabela.options.length; i++) {
		if(objTabela.options[i].selected){
			for(j = 0; j < objItens.options.length; j++) {
				boolTemTabela = (objTabela.options[i].value == objItens.options[j].value);
				if(boolTemTabela) { break; }
			}
			
			if(!boolTemTabela){
				var temp = new Option(objTabela.options[i].text, objTabela.options[i].value);
				objItens.options[objItens.options.length] = temp;
				objTabela.options[i].style.backgroundColor = "#AFD987";
				break;
			}
 		}
	}
}

function removeItem(){
	var objTabela  = document.formwizard.var_tabela;
	var objItens  = document.formwizard.var_itens;
	
	for(i = 0; i < objItens.options.length; i++) {
		if(objItens.options[i].selected == true){
			for(j = 0; j < objTabela.options.length; j++) {
				if(objItens.options[i].value == objTabela.options[j].value){
					objTabela.options[j].style.backgroundColor = "#FFFFFF";
				}
			}
			objItens.options[i] = null;
		}
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
		  <form name="formwizard" action="wizardlotesp3.php" method="post">
		   <input type="hidden" name="var_buffer_tabelas" value="">
			<tr>
				<td width="1%"><img src="wizard_lotes.jpg"></td>
				<td align="left" valign="top">
					<table width="99%" border="0" cellspacing="0" cellpadding="4">	
						<tr>
							<td colspan="2" valign="top"><h3>Selecione os tabelas e a forma de critério de pesquisa</h3></td>
						</tr>
						<tr>
							<td nowrap align="right">Tipo de junção:</td>
							<td align="left">
								<select name="var_join">
									<option selected value="">Nenhuma</option>
									<option value="INNER JOIN">Junção Simples</option>
									<option value="LEFT OUTER JOIN">Junção Hierárquica (Topo para baixo)</option>
									<option value="RIGHT OUTER JOIN">Junção Hierárquica (De baixo para topo)</option>
								</select> 
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<table border="0" cellspacing="8" cellpadding="0">
									<tr>
										<td valign="top" align="center">
											<select name="var_tabela" size="13" style="width:200px;" onDblClick="newItem();">
												<?php echo($strListTables); ?>
											</select>
										</td>
										<td align="center">
											<input type="button" value=" &gt;&gt; " class="inputclean" onClick="newItem();"><br>
											<input type="button" value=" &lt;&lt; " class="inputclean" onClick="removeItem();">
										</td>
										<td valign="top" align="center">
											<select name="var_itens" size="13" style="width:200px;">
											</select>
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