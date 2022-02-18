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
<form name="formeditor_000" action="STdata.php" method="post" target="<?php echo(CFG_SYSTEM_NAME."_main"); ?>">
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
							<label for="var_teste"><?php echo(getTText("cod_atividade",C_NONE));?>:</label>&nbsp;
							<br>
							<input type="text" name="var_cod_atividade" size="5" maxlength="20" onkeypress="return validateNumKey(event);" />
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_teste1"><?php echo(getTText("categoria",C_NONE));?>:</label>&nbsp;
							<br>
							<select name="var_cod_categoria" style="width:120px;">
								<option value=""></option>
								<?php echo(montaCombo($objConn,"SELECT cod_categoria, nome FROM bs_categoria WHERE dtt_inativo IS NULL","cod_categoria","nome"));?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_teste1"><?php echo(getTText("situacao",C_NONE));?>:</label>&nbsp;
							<br>
							<select name="var_situacao" style="width:120px;">
								<option value=""></option>
								<option value="aberto"><?php echo(getTText("aberto",C_TOUPPER));?></option>
								<option value="executando"><?php echo(getTText("executando",C_TOUPPER));?></option>
								<option value="fechado"><?php echo(getTText("fechado",C_TOUPPER));?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_teste1"><?php echo(getTText("prioridade",C_NONE));?>:</label>&nbsp;
							<br>
							<select name="var_prioridade" style="width:120px;">
								<option value=""></option>
								<option value="normal"><?php echo(getTText("normal",C_TOUPPER));?></option>
								<option value="baixa"><?php echo(getTText("baixa",C_TOUPPER));?></option>
								<option value="media"><?php echo(getTText("media",C_TOUPPER));?></option>
								<option value="alta"><?php echo(getTText("alta",C_TOUPPER));?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_teste"><?php echo(getTText("titulo",C_NONE));?>:</label>&nbsp;
							<br>
							<input type="text" name="var_titulo" size="30" maxlength="100" />
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_teste"><?php echo(getTText("id_responsavel",C_NONE));?>:</label>&nbsp;
							<br>
							<input type="text" name="var_id_responsavel" size="30" maxlength="100" />
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_teste"><?php echo(getTText("periodo",C_NONE));?>:</label>&nbsp;
							<br>
							<input type="text" name="var_periodo_min" id="var_periodo_min" size="12" maxlength="10" onkeypress="return validateNumKey(event);" onkeydown="FormataInputData(this,event)" />
							<?php echo(getTText("ate",C_NONE));?>
							<input type="text" name="var_periodo_max" id="var_periodo_max" size="12" maxlength="10" onkeypress="return validateNumKey(event);" onkeydown="FormataInputData(this,event)" />
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