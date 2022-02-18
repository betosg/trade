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
							<label for="var_teste1">Conta Realizada:</label>&nbsp;
							<br>
							<select name="var_conta_realizada">
								<option value=""></option>
								<?php echo(montaCombo($objConn," SELECT cod_conta, nome FROM fin_conta ORDER BY nome ","cod_conta","nome")); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_teste">Mes:</label>&nbsp;
							<br>
							<select name="var_mes" style="width:50px;">
								<option value=""></option>
								<?php 
								$arrMes = array("01" => "jan"
											  , "02" => "fev"
											  , "03" => "mar"
											  , "04" => "abr"
											  , "05" => "mai"
											  , "06" => "jun"
											  , "07" => "jul"
											  , "08" => "ago"
											  , "09" => "set"
											  , "10" => "out"
											  , "11" => "nov"
											  , "12" => "dec");
								
								foreach($arrMes as $strNum => $strLabel) {
									echo("<option value=\"" . $strNum . "\"" . ((date("m") == intval($strNum)) ? " selected" : "") . ">" . getTText($strLabel,C_UCWORDS) . "</option>" );
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style="padding-left:5px;">
							<label for="var_teste1">Ano:</label>&nbsp;
							<br>
							<select name="var_ano" style="width:60px;">
								<option value="2009">2009</option>
								<option value="2008">2008</option>
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