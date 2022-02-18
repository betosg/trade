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
						<td style="padding-left:5px;">
							<label for="var_codigo"><?php echo getTText("codigo",C_UCWORDS);?>:</label>&nbsp;
							<br>
							<input type="text" name="var_codigo" value="" style="width:50px;" onKeyPress="validateNumKey(event);">
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_cod_conta"><?php echo getTText("cod_conta",C_UCWORDS);?>:</label>&nbsp;
							<br>
							<select name="var_cod_conta" style="width:140px;">
								<option value=""><?php echo(getTText("selecione",C_UCWORDS)); ?>...</option>
								<?php echo(montaCombo($objConn," SELECT cod_conta, nome FROM fin_conta ORDER BY tipo, ordem, nome ","cod_conta","nome","")); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_cod_plano_conta"><?php echo getTText("cod_plano_conta",C_UCWORDS);?>:</label>&nbsp;
							<br>
							<select name="var_cod_plano_conta" style="width:170px;">
								<option value=""><?php echo(getTText("selecione",C_UCWORDS)); ?>...</option>
								<?php echo(montaCombo($objConn,"SELECT DISTINCT t2.cod_plano_conta, t1.cod_reduzido || ' ' || t1.nome AS rotulo 
																FROM fin_plano_conta t1, fin_lcto_ordinario t2 
																WHERE t1.cod_plano_conta = t2.cod_plano_conta 
																
																UNION 
																
																SELECT DISTINCT t2.cod_plano_conta, t1.cod_reduzido || ' ' || t1.nome 
																FROM fin_plano_conta t1, fin_lcto_em_conta t2 
																WHERE t1.cod_plano_conta = t2.cod_plano_conta 
																
																ORDER BY 2 ","cod_plano_conta","rotulo","")); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_cod_centro_custo"><?php echo getTText("cod_centro_custo",C_UCWORDS);?>:</label>&nbsp;
							<br>
							<select name="var_cod_centro_custo" style="width:170px;">
								<option value=""><?php echo(getTText("selecione",C_UCWORDS)); ?>...</option>
								<?php echo(montaCombo($objConn," SELECT cod_centro_custo, nome FROM fin_centro_custo ORDER BY cod_reduzido, nome ","cod_centro_custo","nome","")); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_tipo_conta"><?php echo getTText("tipo_conta",C_UCWORDS);?>:</label>&nbsp;
							<br>
							<select name="var_tipo_conta" style="width:140px;">
								<option value=""><?php echo(getTText("selecione",C_UCWORDS)); ?>...</option>
								<option value="pagar"><?php echo(getTText("pagar",C_UCWORDS)); ?></option>
								<option value="receber"><?php echo(getTText("receber",C_UCWORDS)); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_periodo"><?php echo getTText("periodo",C_UCWORDS);?>:</label>&nbsp;
							<br>
							<select name="var_periodo" style="width:100px;">
								<option value=""><?php echo(getTText("selecione",C_UCWORDS)); ?>...</option>
								<option value="ate_hoje" selected><?php echo(getTText("ate_hoje",C_UCWORDS)); ?></option>
								<option value="sete_dias"><?php echo(getTText("sete_dias",C_UCWORDS)); ?></option>
								<option value="quinze_dias"><?php echo(getTText("quinze_dias",C_UCWORDS)); ?></option>
								<option value="trinta_dias"><?php echo(getTText("trinta_dias",C_UCWORDS)); ?></option>
								<option value="sessenta_dias"><?php echo(getTText("sessenta_dias",C_UCWORDS)); ?></option>
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