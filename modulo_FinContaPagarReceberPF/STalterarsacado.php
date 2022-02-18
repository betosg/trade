<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado  = request("var_chavereg");	// cod_pf
	// $strPopulate = "yes";
	
	// if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session
	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
		
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// busca dados sobre a agenda para confirmação antes de DEL
	try{
		$strSQL = "SELECT cad_pf.dados_sacado FROM cad_pf WHERE cad_pf.cod_pf = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	// fetch dos dados localizados
	$objRS = $objResult->fetch();
				
	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
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
			
			
			function ajaxGetDadosPF(){
				// Verifica os dados da PJ conforme o CPF digitado
				if(document.getElementById('DBVAR_NUM_CPF').value != ""){
					var objAjax;
					var strReturnValue;
					var strSQL;
					var arrResult;
					var i;
					
					document.getElementById('search_img').src = "../img/icon_ajax_loader.gif";
					
					strSQL  = "SELECT nome, endprin_logradouro, endprin_numero, endprin_complemento, endprin_cep, endprin_bairro,";
					strSQL  = strSQL + " endprin_cidade, endprin_estado FROM cad_pf WHERE cpf = ";
					strSQL  = strSQL + "'" + document.getElementById('DBVAR_NUM_CPF').value + "'";
					
					objAjax = createAjax();
					objAjax.onreadystatechange = function(){
						if(objAjax.readyState == 4){
							if(objAjax.status == 200){
								strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
								//alert(strReturnValue);
								
								arrResult = strReturnValue.split("|");
								arrResult[0] = (arrResult[0] == null) ? "" : arrResult[0];
								arrResult[1] = (arrResult[1] == null) ? "" : arrResult[1];
								arrResult[2] = (arrResult[2] == null) ? "" : arrResult[2];
								arrResult[3] = (arrResult[3] == null) ? "" : arrResult[3];
								arrResult[4] = (arrResult[4] == null) ? "" : arrResult[4];
								arrResult[5] = (arrResult[5] == null) ? "" : arrResult[5];
								arrResult[6] = (arrResult[6] == null) ? "" : arrResult[6];
								arrResult[7] = (arrResult[7] == null) ? "" : arrResult[7];
								arrResult[8] = (arrResult[8] == null) ? "" : arrResult[8];
								document.getElementById('DBVAR_STR_NOME').value 		= arrResult[0];
								document.getElementById('DBVAR_STR_LOGRADOURO').value 	= arrResult[1];
								document.getElementById('DBVAR_STR_NUMERO').value 		= arrResult[2];
								document.getElementById('DBVAR_STR_COMPLEMENTO').value 	= arrResult[3];
								document.getElementById('DBVAR_STR_CEP').value 			= arrResult[4];
								document.getElementById('DBVAR_STR_BAIRRO').value 		= arrResult[5];
								document.getElementById('DBVAR_STR_CIDADE').value 		= arrResult[6];
								document.getElementById('DBVAR_STR_ESTADO').value 		= arrResult[7];
								// alert(prSQL);
								// verifica se retornou dados
								// if(strReturnValue.indexOf('|') != -1){ alert('Esta empresa já está CADASTRADA!'); }
								document.getElementById('search_img').src = "../img/icon_zoom_disabled.gif";
							}
							else {
								alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
							}
						}
					}
					objAjax.open("GET", "../_ajax/STreturndados.php?var_sql=" + strSQL, true); 
					objAjax.send(null); 
				} else{
					return(false);
				}
			}
			
			
			function ok(){
				strLocation = "";
				submeterForm();
			}

			function cancelar(){ window.close(); }

			function aplicar(){
				strLocation = "../modulo_FinContaPagarReceber/STalterarsacado.php?var_chavereg=<?php echo($intCodDado);?>";
				submeterForm();
			}

			function submeterForm(){
				document.forminsert.DEFAULT_LOCATION.value = strLocation;
				document.forminsert.submit();
			}
		</script>
	</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;">
<!-- body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;"  -->

<!-- USO -->
<center>
<?php athBeginFloatingBox("520","",getTText("upd_sacado",C_UCWORDS),CL_CORBAR_GLASS_1); ?>
<table cellpadding="0" cellspacing="0" border="0" height="315" width="500" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15Px 0px 0px 15px;">
			<strong><?php echo(getTText("confirme_dados",C_NONE));?>:</strong>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 30px 10px 30px;">
			<table cellspacing="2" cellpadding="3" border="0" width="100%">
				
				<!-- DADOS SACADO -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_sacado",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<form name="forminsert" action="STalterarsacadoexec.php" method="post">
				<input type="hidden" name="DEFAULT_LOCATION" 
				 value="../modulo_FinContaPagarReceber/STalterarsacado.php?var_chavereg=<?php echo($intCodDado)?>">
				<input type="hidden" name="DBVAR_NUM_COD_PF" value="<?php echo($intCodDado);?>" />
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right"><strong><?php echo(getTText("cpf",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<input type="text" name="DBVAR_NUM_CPF" id="DBVAR_NUM_CPF" onKeyPress="return validateNumKey(event);"
							 maxlength="20" size="15" />
							<span id="search" style="vertical-align:middle;display:inline;cursor:pointer;">
								<img src="../img/icon_zoom_disabled.gif" title="<?php echo(getTText("buscar",C_NONE));?>"
								 onClick="ajaxGetDadosPF();" id="search_img" />
							</span>
							<span class="comment_peq">
								<?php echo(getTText("busca_por_cpf",C_NONE));?>
							</span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top">
							<strong>*<?php echo(getTText("nome",C_UCWORDS));?>:</strong>
						</td>
						<td width="77%" align="left">
							<input type="text" name="DBVAR_STR_NOME" id="DBVAR_STR_NOME" size="55" maxlength="120" />
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top">
							<strong><?php echo(getTText("cep",C_UCWORDS));?>:</strong>
						</td>
						<td width="77%" align="left">
							<input type="text" name="DBVAR_STR_CEP" id="DBVAR_STR_CEP" size="10" maxlength="8" 
							 onKeyPress="return validateNumKey(event);" />
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top">
							<strong><?php echo(getTText("logradouro",C_UCWORDS));?>:</strong>
						</td>
						<td width="77%" align="left">
							<input type="text" name="DBVAR_STR_LOGRADOURO" id="DBVAR_STR_LOGRADOURO" size="55" maxlength="120" />
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top">
							<strong><?php echo(getTText("numero",C_UCWORDS));?>:</strong>
						</td>
						<td width="77%" align="left">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
							<td width="10%" align="left">
							<input type="text" name="DBVAR_STR_NUMERO" id="DBVAR_STR_NUMERO" size="5" maxlength="10" />
							</td>
							<td width="30%" align="center">
							<strong><?php echo(getTText("complemento",C_UCWORDS));?>:</strong>
							</td>
							<td width="60%" align="left">
							<input type="text" name="DBVAR_STR_COMPLEMENTO" id="DBVAR_STR_COMPLEMENTO" size="15" maxlength="10" />
							</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top">
							<strong><?php echo(getTText("bairro",C_UCWORDS));?>:</strong>
						</td>
						<td width="77%" align="left">
							<input type="text" name="DBVAR_STR_BAIRRO" id="DBVAR_STR_BAIRRO" size="28" maxlength="80" />
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top">
							<strong><?php echo(getTText("cidade",C_UCWORDS));?>:</strong>
						</td>
						<td width="77%" align="left">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
							<td width="30%" align="left">
							<input type="text" name="DBVAR_STR_CIDADE" id="DBVAR_STR_CIDADE" size="28" maxlength="80" />
							</td>
							<td width="20%" align="center">
							<strong><?php echo(getTText("uf",C_UCWORDS));?>:</strong>
							</td>
							<td width="50%" align="left" style="vertical-align:middle;">
							<select name="DBVAR_STR_ESTADO" style="width:50px;">
								<option value=""></option>
								<?php echo(montaCombo($objConn,"SELECT sigla_estado FROM 
								 								lc_estado ORDER BY sigla_estado",
															   "sigla_estado","sigla_estado","RS"));?>
							</select>
							</td>
							</tr>
							</table>
						</td>
					</tr>
				</form>
				<!-- DADOS SACADO -->
				<tr>
					<td colspan="2" style="border-bottom:1px solid #CCC;padding-top:15px;">
						<span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span>
					</td>
				</tr>
								
				
			</table>			
		</td>
	</tr>
	<!-- LINHA DOS BUTTONS E AVISO -->
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td width="20%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr><td align="right" width="23%" style="padding-right:8px;"></td></tr>
						</table>
					</td>
					<!-- goNext() -->
					<td width="35%" align="right">
						<button onClick="ok();">
							<?php echo(getTText("ok",C_NONE));?>
						</button>
					</td>
					<td width="20%" align="left" >
						<button onClick="cancelar('');return false;">
							<?php echo(getTText("cancelar",C_NONE));?>
						</button>
					</td>
					<td width="25%" align="left" >
						<button onClick="aplicar();">
							<?php echo(getTText("aplicar",C_NONE));?>
						</button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
</table>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodDado); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>