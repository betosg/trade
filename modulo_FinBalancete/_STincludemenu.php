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
</script>
<?php athBeginFloatingBox("205","",getTText("filtrar_por",C_NONE) . "...",CL_CORBAR_GLASS_2); ?>
<form name="formeditor_000" action="STdata.php" method="get" target="<?php echo(CFG_SYSTEM_NAME . "_main"); ?>">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<!-- AQUI ESTÁ O CÓDIGO PARA FAZER O CABEÇALHO DE AGRUPAMENTO DE CAMPOS -->
		<tr> 
			<td colspan="2">
				<!-- id="Nome do Rótulo" AQUI COLOCA O ID DA TABELA PARA FAZER O AGRUPAMENTO -->
				<table id="" border="0" cellpadding="0" cellspacing="0" width="100%" style="display:block">
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_dt_ini"><?php echo(getTText("dt_inicio",C_NONE));?>:</label>&nbsp;
							<br>
							<input type="text" name="var_dt_ini" size="12" maxlength="10" onkeypress="return validateNumKey(event);" onkeydown="FormataInputData(this,event);" value="<?php echo(dDate(CFG_LANG,dateNow(),false));?>">
						</td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_dt_fim"><?php echo(getTText("dt_fim",C_NONE));?>:</label>&nbsp;
							<br>
							<input type="text" name="var_dt_fim" size="12" maxlength="10" onkeypress="return validateNumKey(event);" onkeydown="FormataInputData(this,event);" value="<?php echo(dDate(CFG_LANG,dateNow(),false));?>">
						</td>
					</tr>
					<tr><td height="5"></td></tr>
					<!--tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_tipo_lcto"><?php //echo(getTText("tipo_lcto",C_NONE));?>:</label>&nbsp;
							<br>
							<select name="var_tipo_lcto" style="width:120px;">
								<option value="ALL" selected="selected"><?php //echo(getTText("tipo_todos",C_TOUPPER));?></option>
								<option value="ORD"><?php //echo(getTText("tipo_ordinario",C_TOUPPER));?></option>
								<option value="CNT"><?php //echo(getTText("tipo_em_conta",C_TOUPPER));?></option>
								<option value="TSF"><?php //echo(getTText("tipo_transfer",C_TOUPPER));?></select>								
							</select>
						</td>
					</tr>
					<tr><td height="5"></td></tr-->
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_conta_banco"><?php echo(getTText("conta_banco",C_NONE));?>:</label>&nbsp;
							<br>
							<select name="var_conta_banco" style="width:160px;">
								<option value="" selected="selected"><?php echo(getTText("selecione",C_UCWORDS)); ?>...</option>
								<?php echo(montaCombo($objConn,"SELECT cod_conta, cod_conta || ' - ' || nome AS conta FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY nome ","cod_conta","conta",getVarEntidade($objConn,"fin_cod_conta_banco_padrao"))); ?>
							</select>
						</td>
					</tr>
					<tr><td height="5"></td></tr>
					<!--tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_plano_conta"><?php //echo(getTText("plano_conta",C_NONE));?>:</label>&nbsp;
							<br>
							<select name="var_plano_conta" style="width:180px;">
								<option value="" selected="selected"><?php //echo(getTText("selecione",C_UCWORDS)); ?>...</option>
								<?php //echo(montaCombo($objConn,"SELECT cod_plano_conta, cod_reduzido||' - '||nome AS nome FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido","cod_plano_conta","nome",""));?>
							</select>
						</td>
					</tr-->
				</table>
			</td>
			<input type="image" name="Submit" border="0" style="border:none;cursor:arrow;background:none;width:0px;height:0px" src="../img/transparent.gif">
		</tr>
		<tr><td colspan="2" height="5"></td></tr>
		<tr><td height="1" class="linedialog"></td></tr>
		<tr align="right" valign="middle">
			<td><br><button onClick="submeterForm();"><?php echo(getTText("aplicar",C_UCWORDS)) ?></button></td>
		</tr>
	</table>
</form>
<?php athEndFloatingBox(); ?>