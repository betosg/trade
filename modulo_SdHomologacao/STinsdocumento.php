<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado  = request("var_chavereg");	// COD_HOMOLOGACAO
	
	// CARREGA PREFIX PARA SESSION
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// VERIFICAÇÃO DE ACESSO
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
		
	if($intCodDado == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("cod_homo_null",C_NONE),"","erro","1");
		die();
	}

	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// LOCALIZA TODOS OS DOCUMENTOS EMITIDOS
	// PARA A HOMOLOGAÇÃO ATUAL, NEW NEW NEW
	try{
		$strSQL = "
			SELECT sd_homologacao.situacao FROM sd_homologacao WHERE sd_homologacao.cod_homologacao = ".$intCodDado;
		$objResult  = $objConn->query($strSQL);
		$objRS	    = $objResult->fetch();
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// MSG QUE COLABORADOR JÁ FOI HOMOLOGADO
	if(getValue($objRS,"situacao") != "aberto"){
		mensagem("err_sql_titulo","err_sql_desc_card","Esta HOMOLOGAÇÃO não está ABERTA, para INSERÇÃO DE DOCUMENTOS.","STdocumentos.php?var_chavereg=".$intCodDado,"aviso",1,"");
		die();	
	}

	// INICIALIZA VAR PARA TROCAR DE COR LINHA
	$strColor = CL_CORLINHA_2;
	
	// FUNÇÃO PARA TROCA DE COR DE LINHAS
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE);?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css">
			.tr_filtro_field { padding-left:5px; }
			.tr_filtro_label { padding-left:5px; padding-top:5px; }
			.td_search_left  { 
				padding:8px;
				border-top:1px solid #C9C9C9;
				border-left:1px solid #C9C9C9;
				border-bottom:1px solid #C9C9C9; 
			}
			.td_search_right  { 
				padding:5px;
				border-top:1px solid #C9C9C9;
				border-right:1px solid #C9C9C9;
				border-left: 1px dashed #C9C9C9;
				border-bottom:1px solid #C9C9C9;
			}
			.table_master{
				background-color:#FFFFFF;
				border-top:   1px solid #E9E9E9;
				border-right: 1px solid #E9E9E9;
				border-bottom:1px solid #E9E9E9;
				border-left:  1px solid #E9E9E9;
				padding-bottom: 5px;
			}
			.td_no_resp{ 
				font-size:11px; 
				font-weight:bold; 
				color:#C9C9C9; 
				text-align:center; 
				border:1px solid #E9E9E9;
				padding:5px 5px 0px 5px;
			}
			.td_resp{ border:1px solid #E9E9E9; padding:5px 0px 2px 10px; }
			.td_resp_cabec{ font-size:11px; font-weight:bold; color:#CCC;}
			.td_resp_conte{ padding:6px 0px 2px 20px; }
			.td_text_resp { border:2px dashed #E9E9E9; padding:4px 9px 4px 9px; }
		</style>
		<script type="text/javascript">
			var strLocation = null;
			function ok() {
				var strMSG = "";
				if(
					(document.getElementById("var_tipo").value == "")||
					(document.getElementById("var_titulo").value == "")||
					(document.getElementById("var_html_texto").value == "")
				  ){ strMSG += "\n\nDADOS DO DOCUMENTO:";}
				if(document.getElementById("var_tipo").value == "") { strMSG += "\nTipo do Documento"; }
				if(document.getElementById("var_titulo").value == "") { strMSG += "\nTítulo do Documento"; }
				if(document.getElementById("var_html_texto").value == "") { strMSG += "\nTexto do Documento"; }
				if(strMSG != ""){
					alert("Verifique os Campos obrigatórios:" + strMSG);
				} else{
					strLocation = "../modulo_SdHomologacao/STdocumentos.php?var_chavereg=<?php echo($intCodDado);?>";
					submeterForm();
				}
			}

			function cancelar() {
				document.location.href = "../modulo_SdHomologacao/STdocumentos.php?var_chavereg=<?php echo($intCodDado);?>";
			}

			function aplicar() {
				var strMSG = "";
				if(
					(document.getElementById("var_tipo").value == "")||
					(document.getElementById("var_titulo").value == "")||
					(document.getElementById("var_html_texto").value == "")
				  ){ strMSG += "\n\nDADOS DO DOCUMENTO:";}
				if(document.getElementById("var_tipo").value == "") { strMSG += "\nTipo do Documento"; }
				if(document.getElementById("var_titulo").value == "") { strMSG += "\nTítulo do Documento"; }
				if(document.getElementById("var_html_texto").value == "") { strMSG += "\nTexto do Documento"; }
				if(strMSG != ""){
					alert("Verifique os Campos obrigatórios:" + strMSG);
				} else{
					strLocation = "../modulo_SdHomologacao/STinsdocumento.php?var_chavereg=<?php echo($intCodDado);?>";
					submeterForm();
				}
			}

			function submeterForm() {
				document.formstatic.DEFAULT_LOCATION.value = strLocation;
				document.formstatic.submit();
			}
			
			function selectDoc(){
				// alert(document.getElementById('var_html_ressalva_declaracao')[document.getElementById('var_html_ressalva_declaracao').selectedIndex].innerHTML);
				document.getElementById("var_tipo").value   = document.getElementById("var_tipo_model").value;
				document.getElementById("var_titulo").value = document.getElementById('var_html_ressalva_declaracao')[document.getElementById('var_html_ressalva_declaracao').selectedIndex].innerHTML;
				var strComboValue = document.getElementById("var_html_ressalva_declaracao").value;
				document.getElementById("var_html_texto").innerHTML = strComboValue;
			}
			
			function ajaxGetModels(){
				if(document.getElementById('var_tipo_model').value == ""){ return(null); }
				var strSQL = "SELECT texto,titulo FROM sd_ressalva_declaracao_termo WHERE dtt_inativo IS NULL AND tipo = '" + document.getElementById('var_tipo_model').value + "' ORDER BY tipo";
				
				var objAjax;
				var strReturnValue;
				objAjax = createAjax();							
				objAjax.onreadystatechange = function(){
					if(objAjax.readyState == 4) {
						if(objAjax.status == 200) {
							strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
							alert(strReturnValue);
							// Cria uma opção em branco
							var optionBlank   = document.createElement('option');
							optionBlank.text  = "Selecione o Modelo...";
							var obj 		  = document.getElementById("var_html_ressalva_declaracao");
							obj.add(optionBlank);
							
							// Dados
							var Item1, Item2, prDados;
							var arrAux1 = null;
							var arrAux2 = null;
							prDados = strReturnValue;
							arrAux1 = prDados.split("@@");
											
							if(prDados.length > 1) {
								for(Item1 in arrAux1) {
									Item2 = arrAux1[Item1];
									arrAux2 = Item2.split("|");
								
									var optionNew = document.createElement('option');
									optionNew.setAttribute('value',arrAux2[0]);
									var textOption =  document.createTextNode(arrAux2[1]);
									optionNew.appendChild(textOption);
									obj.appendChild(optionNew);
								
									//obj.add( new Option(caption,value) );
									//obj.add( new Option(arrAux2[1],arrAux2[0]) );
								}
							}
						} else {
							alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
						}
					}
				}
				objAjax.open("GET", "../_ajax/STreturnmodels.php?var_sql=" + strSQL,  true); 
				objAjax.send(null); 
			}
		</script>
	</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("680","",getTText("doc_ins",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<table cellpadding="0" cellspacing="0" border="0" height="100%" width="660" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15px 0px 0px 15px;">
			<strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 80px 10px 80px;">
			<table cellspacing="2" cellpadding="3" border="0" width="100%">
			<form name="formstatic" id="formstatic" action="STinsdocumentoexec.php" method="post">
				<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado);?>" />
				<input type="hidden" name="DEFAULT_LOCATION" value="" />
				<!-- DADOS DOCUMENTO -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("modelo_de_documento",C_TOUPPER));?></strong></td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("tipo",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="var_tipo_model" id="var_tipo_model" style="width:200px;" onChange="javascript:limpaSelect('var_html_ressalva_declaracao');ajaxGetModels();">
							<option value="">Selecione o Tipo...</option>
							<option value="termo">TERMO</option>
							<option value="ressalva">RESSALVA</option>
							<option value="declaracao">DECLARAÇÃO</option>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("modelo_documento",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="var_html_ressalva_declaracao" id="var_html_ressalva_declaracao" style="width:280px;" onChange="selectDoc();">
							<!--option value=""></option-->
							<?php //echo(montaCombo($objConn,"SELECT texto, '['||UPPER(tipo)||']'||' '||titulo AS nome FROM sd_ressalva_declaracao_termo WHERE dtt_inativo IS NULL ORDER BY tipo","texto","nome",""));?>
						</select>
						<br /><span class="comment_med">Você pode selecionar um modelo de Documento para inserção</span>
					</td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("dados_documento",C_TOUPPER));?></strong></td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("tipo",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<select name="var_tipo" id="var_tipo" style="width:100px;">
							<option value=""></option>
							<option value="termo">TERMO</option>
							<option value="ressalva">RESSALVA</option>
							<option value="declaracao">DECLARAÇÃO</option>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("titulo",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left"><input type="text" name="var_titulo" id="var_titulo" size="65" maxlength="255" /></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("texto_html",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
						  <td width="75%"><textarea name="var_html_texto" id="var_html_texto" rows="10" cols="80"></textarea>&nbsp;<br /></td>
							<td width="25%" valign="middle" align="left"></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td width="23%" align="right" valign="top"><strong><?php echo(getTText("abrir_documento",C_UCWORDS));?>:</strong></td>
					<td width="77%" align="left">
						<input type="checkbox" name="var_flag_abrir" value="TRUE" checked="checked" class="inputclean" />&nbsp;Abrir o documento Gerado
					</td>
				</tr>
				<tr>
					<td colspan="2" style="border-bottom:1px solid #CCC;padding-top:15px;">
						<span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span>
					</td>
				</tr>
				<tr><td colspan="2" height="10">&nbsp;</td></tr>
				<tr>
					<td colspan="2" align="right">
						<button onClick="ok();return false;"><?php echo(getTText("ok",C_UCWORDS));?></button>
						<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
						<button onClick="aplicar();return false;"><?php echo(getTText("aplicar",C_UCWORDS));?></button>
					</td>
				</tr>
			</form>			
			</table>			
		</td>
	</tr>
</table>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>