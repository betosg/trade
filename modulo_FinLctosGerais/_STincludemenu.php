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
</script>
<?php athBeginFloatingBox("205","",getTText("filtrar_por",C_NONE) . "...",CL_CORBAR_GLASS_2); ?>
<form name="formeditor_000" action="STdata.php" method="get" target="<?php echo(CFG_SYSTEM_NAME . "_main"); ?>">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<!-- AQUI EST� O C�DIGO PARA FAZER O CABE�ALHO DE AGRUPAMENTO DE CAMPOS
		<tr>
			<td colspan="2" align="left" bgcolor="#DBDBDB" height="16" onClick="collapseMenu('/*Nome do R�tulo*/');">
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
				<!-- id="Nome do R�tulo" AQUI COLOCA O ID DA TABELA PARA FAZER O AGRUPAMENTO -->
				<table id="" border="0" cellpadding="0" cellspacing="0" width="100%" style="display:block">
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_teste1">Conta Realizada:</label>&nbsp;
							<br>
							<select name="var_conta_realizada">
								<?php echo(montaCombo($objConn," SELECT cod_conta, nome FROM fin_conta ORDER BY nome ","cod_conta","nome")); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style="padding-left:5px;">
							<label for="var_teste">Data In�cio:</label>&nbsp;
							<br>
							<input type="text" name="var_data_inicio" value="<?php echo(dDate(CFG_LANG,date("Y-m-01"),false))?>" style="width:70px;" onKeyPress="validateNumKey(event);FormataInputData(this);">
						</td>
					</tr>
					<tr>
						<td style="padding-left:5px;">
							<label for="var_teste">Data Fim:</label>&nbsp;
							<br>
							<input type="text" name="var_data_fim" value="<?php echo(dDate(CFG_LANG,date("Y-m-t"),false))?>" style="width:70px;" onKeyPress="validateNumKey(event);FormataInputData(this);">
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