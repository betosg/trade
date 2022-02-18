<?php $objConn = abreDBConn(CFG_DB); ?> 
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
<?php athBeginFloatingBox("205","",getTText("filtrar_por",C_NONE) . "...",CL_CORBAR_GLASS_2); ?>
<form name="formeditor_000" action="STdata.php" method="get" target="<?php echo(CFG_SYSTEM_NAME . "_main"); ?>">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<!-- AQUI ESTÁ O CÓDIGO PARA FAZER O CABEÇALHO DE AGRUPAMENTO DE CAMPOS
		<tr>
			<td colspan="2" align="left" bgcolor="#DBDBDB" height="16" onClick="collapseMenu('/*Nome do Rótulo*/');">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="99%" style="padding-left:5px"><b>" . $strRotuloGRP . "</b></td>
						<td width="1%"  style="padding-right:5px"><img src="../img/collapse_generic_close.gif" id="menu_img_" . $strRotuloGRP . ""></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="2" height="5"></td></tr>
		-->
		<tr> 
			<td colspan="2">
				<!-- id="Nome do Rótulo" AQUI COLOCA O ID DA TABELA PARA FAZER O AGRUPAMENTO -->
				<table id="" border="0" cellpadding="0" cellspacing="0" width="100%" style="display:block">
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_mes"><?php echo(getTText("mes",C_UCWORDS));?>:</label>&nbsp;
							<br>
							<select name="var_mes" style="width:80px;">
								<option value=""><?php echo(getTText("selecione",C_UCWORDS)); ?>...</option>
								<option value="01" <?php if (date("m") ==  1) echo(" selected"); ?>>janeiro</option>
								<option value="02" <?php if (date("m") ==  2) echo(" selected"); ?>>fevereiro</option>
								<option value="03" <?php if (date("m") ==  3) echo(" selected"); ?>>março</option>
								<option value="04" <?php if (date("m") ==  4) echo(" selected"); ?>>abril</option>
								<option value="05" <?php if (date("m") ==  5) echo(" selected"); ?>>maio</option>
								<option value="06" <?php if (date("m") ==  6) echo(" selected"); ?>>junho</option>
								<option value="07" <?php if (date("m") ==  7) echo(" selected"); ?>>julho</option>
								<option value="08" <?php if (date("m") ==  8) echo(" selected"); ?>>agosto</option>
								<option value="09" <?php if (date("m") ==  9) echo(" selected"); ?>>setembro</option>
								<option value="10" <?php if (date("m") == 10) echo(" selected"); ?>>outubro</option>
								<option value="11" <?php if (date("m") == 11) echo(" selected"); ?>>novembro</option>
								<option value="12" <?php if (date("m") == 12) echo(" selected"); ?>>dezembro</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_ano"><?php echo(getTText("ano",C_UCWORDS));?>:</label>&nbsp;
							<br>
							<select name="var_ano" style="width:60px;">
								<?php echo(montaCombo($objConn, " SELECT DISTINCT ano FROM fin_saldo_ac ORDER BY ano DESC", "ano", "ano", date("Y"))); ?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="2" height="5"></td></tr>
		<tr><td height="1" class="linedialog"></td></tr>
		<tr align="right" valign="middle">
			<td><br><button onClick="submeterForm();"><?php echo(getTText("aplicar",C_UCWORDS)) ?></button></td>
		</tr>
	</table>
</form>
<?php athEndFloatingBox(); ?>
<?php $objConn = NULL; ?> 