<html>
	<head>
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script>
			var intCurrentPos = -1;
			var intCurrentPosMouse;
			var intCurrentEditing;
			
			function switchColor(prObj, prColor){
				if(prObj != null){
					prObj.style.backgroundColor = prColor;
				}
			}
			
			function navigateRow(e){
				if(!e) { e = window.event; 	} 
				
				objTableTemplate = document.getElementById("table_template");
				objTableCfg 	 = document.getElementById("table_cfg");
				
				if(e.keyCode == 40){
					switchColor(objTableTemplate.rows[intCurrentPos], "");
					if(intCurrentPos < objTableTemplate.rows.length-1){
						switchColor(objTableTemplate.rows[++intCurrentPos], "#CCCCCC");
					}
					else{
						intCurrentPos = objTableTemplate.rows.length;
					}
					
				}
				else if(e.keyCode == 38){
					switchColor(objTableTemplate.rows[intCurrentPos], "");
					if(intCurrentPos > 0){
						switchColor(objTableTemplate.rows[--intCurrentPos], "#CCCCCC");
					}
					else{
						intCurrentPos = -1;
					}
				} 
				else if (e.keyCode == 0 && e.type == "mouseover") {
					switchColor(objTableTemplate.rows[intCurrentPos], "");
					switchColor(objTableTemplate.rows[intCurrentPosMouse], "#CCCCCC");
					intCurrentPos = intCurrentPosMouse;
				}
				else if (e.keyCode == 13 || e.type == "dblclick"){
					intCurrentEditing = intCurrentPos;
					objTableCfg.rows[0].cells[1].innerHTML = objTableTemplate.rows[intCurrentPos].cells[0].caption;
					document.getElementById("habilitado").checked = (objTableTemplate.rows[intCurrentPos].style.display != "none") ? true : false;
					document.getElementById("negrito").checked = (objTableTemplate.rows[intCurrentPos].cells[0].style.fontWeight == "bold") ? true : false;
					document.getElementById("familia").value = objTableTemplate.rows[intCurrentPos].cells[0].style.fontFamily;
					document.getElementById("tam").value = objTableTemplate.rows[intCurrentPos].cells[0].style.fontSize;
				}
			}
			
			function switchRow(prOrient){
				var mixAux = "";
				var objRowAtual = document.getElementById("table_template").rows[intCurrentEditing].cells[0];
				
				objRowSwitch = (prOrient == "up") ? document.getElementById("table_template").rows[intCurrentEditing-1].cells[0] : document.getElementById("table_template").rows[intCurrentEditing+1].cells[0];
						
				mixAux = objRowSwitch.innerHTML;
				objRowSwitch.innerHTML = objRowAtual.innerHTML;
				objRowAtual.innerHTML = mixAux;
				
				mixAux = objRowSwitch.style;
				
				objRowSwitch.style.fontFamily = objRowAtual.style.fontFamily;
				objRowSwitch.style.fontSize = objRowAtual.style.fontSize;
				objRowSwitch.style.fontWeight = objRowAtual.style.fontWeight;
				
				objRowAtual.style.fontFamily = mixAux.fontFamily;
				objRowAtual.style.fontSize = mixAux.fontSize;
				objRowAtual.style.fontWeight = mixAux.fontWeight;
			}
			
			document.onkeydown = navigateRow;
		</script>
	</head>
	<body>
		<table id="table_cfg" border="0" cellpadding="0" cellspacing="4">
			<tr>
				<td align="right">Titulo área:&nbsp;</td>
				<td></td>
			</tr>
			<tr>
				<td align="right">Habilitado:&nbsp;</td>
				<td><input type="checkbox" id="habilitado" value="sim" class="inputclean"></td>
			</tr>
			<tr>
				<td valign="top" align="right">Fonte:&nbsp;</td>
				<td style="padding-left10px;">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td align="right">Estilo Fonte:&nbsp;</td>
							<td><input type="text" id="familia" value="" size="30"></td>
						<tr>
							<td align="right">Tamanho (Pixels):&nbsp;</td>
							<td><input type="text" id="tam" value="" size="3"></td>
						</tr>
						<tr>
							<td align="right">Negrito:&nbsp;</td>
							<td><input type="checkbox" id="negrito" value="sim" class="inputclean"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="right">Mover:&nbsp;</td>
				<td>
					<input type="button" value="para cima" onClick="switchRow('up');" class="inputclean">
					<input type="button" value="para baixo" onClick="switchRow('down');" class="inputclean">
				</td>
			</tr>		
		</table>
		<hr>
		<table id="table_template" width="310" border="0" cellpadding="0" cellspacing="0" align="center">
		  <tr onDblClick="navigateRow(event);" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);" onMouseOut="switchColor(this,'');"> 
		    <td align="center" valign="top" caption="NOME" style="font-family:arial narrow;font-size:15px;font-weight:bold">NOME</td>
		  </tr>
		  <tr onDblClick="navigateRow(event);" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);" onMouseOut="switchColor(this,'');">
			<td align="center" caption="NOMECOMPLETO/ENTIDADE" style="font-family:arial narrow;font-size:11px;font-weight:normal">
				NOMECOMPLETO
				ENTIDADE
			</td>
		  </tr>
		  <tr onDblClick="navigateRow(event);" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);" onMouseOut="switchColor(this,'');"> 
		    <td height="1" align="center"><img src="../img/dot_gray.gif" width="260" height="1" vspace="4"></td>
		  </tr>
		  <tr onDblClick="navigateRow(event);" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);" onMouseOut="switchColor(this,'');"> 
		    <td align="center" caption="EMPRESA" style="font-family:arial narrow;font-size:17px;font-weight:bold">
			  EMPRESA
			</td>
		  </tr>
		  <tr onDblClick="navigateRow(event);" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);" onMouseOut="switchColor(this,'');">
			<td align="center" caption="LOCAL/PAÍS" style="font-family:arial narrow;font-size:15px;font-weight:normal">
			   LOCAL
			   PAIS
			</td>
		  </tr>
		  <tr onDblClick="navigateRow(event);" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);" onMouseOut="switchColor(this,'');"> 
		    <td height="1" align="center"><img src="../img/dot_gray.gif" width="260" height="1" vspace="4"></td>
		  </tr>
		  <tr onDblClick="navigateRow(event);" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);" onMouseOut="switchColor(this,'');"> 
		    <td valign="top" align="center" class="padrao_peq" caption="CODIGO DE BARRAS" style="font-family:arial;font-size:10px;font-weight:normal"><br>CODIGO DE BARRAS
			  <br><br>
		  </tr>
		  <tr onDblClick="navigateRow(event);" onMouseOver="intCurrentPosMouse = this.rowIndex;navigateRow(event);" onMouseOut="switchColor(this,'');">
		    <td align="center" caption="ATIVIDADE" style="font-family:arial narrow;font-size:13px;font-weight:bold">
			  ATIVIDADE
		    </td>
		  </tr>
		</table>
	</body>
</html>