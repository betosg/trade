<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS");

	// REQUESTS
	$strLocation = request("var_location");
	
	// ABRE OBJETO DE CONEXÃO COM DATABASE
	$objConn = abreDBConn(CFG_DB);
	
	// SQL QUE LOCALIZA AS TAREFAS
	$objConn->beginTransaction();
	try{
		$strSQL = "SELECT cod_atividade, titulo FROM bs_atividade WHERE modelo = TRUE;";
		$objResult = $objConn->query($strSQL);
		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	
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
				border-top:   1px solid #CCC;
				border-right: 1px solid #CCC;
				border-bottom:1px solid #CCC;
				border-left:  1px solid #CCC;
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
			
			#img_drop_dt_ini{ cursor:pointer; display:none }
						
			#img_drop_dt_fim{ cursor:pointer; display:none }
			
			#lst_dt_ini{ width:250px;height:100px;overflow:scroll;display:none }
		</style>
		<script type="text/javascript">
			var strLocation = null;
			
			function validaCampos(){
				// Esta função faz uma pré-validação via JS dos campos marcados com asterisco
				var strMSG  = "";
				strMSG += (
 						   (document.getElementById('var_bs_atividade_modelo').value  == "")|| 
						   ((getCheckedValue(document.getElementById('var_bs_atividade_opcao')) == "S") && (document.getElementById("var_bs_atividade_data_ini").value == ""))
						  ) ? "\n\nDADOS DA ATIVIDADE:" : "";
				strMSG += (document.getElementById('var_bs_atividade_modelo').value   == "") ? "\nModelo de Atividade"	: "";
				strMSG += ((getCheckedValue(document.getElementById('var_bs_atividade_opcao')) == "S") && (document.getElementById("var_bs_atividade_data_ini").value == "")) ? "\nData de Início das Tarefas" : "";
				if(strMSG != ""){ alert('Os seguintes campos não foram preenchidos:'+strMSG); return(false); }
				else { return(true); }
			}
			
			function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
				strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
				AbreJanelaPAGE(strLink, "570", "270");
			}
			
			function ajaxDetailDataLoader(prSQL,prFuncao,prID,prFuncExtra,prIDLoader){
				var objAjax;
				var strReturnValue;
				var objLoader = document.getElementById(prIDLoader);
	
				objAjax = createAjax();
				
				if(objLoader != null){
					objLoader.innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' width='13' />";
				}
				
				objAjax.onreadystatechange = function() {
					if(objAjax.readyState == 4) {
						if(objAjax.status == 200) {
							strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
							//alert(strReturnValue);
							switch (prFuncao) {
								case "ajaxMontaCombo":  ajaxMontaCombo(prID, strReturnValue);
									if(prFuncExtra != '') eval(prFuncExtra);
								break;
								case "ajaxMontaEdit":   ajaxMontaEdit(prID, strReturnValue);
									if(prFuncExtra != '') eval(prFuncExtra);
								break;
							}
							if(objLoader != null){
								objLoader.innerHTML = "";
							}
						} else {
							alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
						}
					}
				}
				objAjax.open("GET","../_ajax/returndados.php?var_sql="+prSQL,true); 
				objAjax.send(null); 
			}
			
			function ok() {
				if(validaCampos()){
					<?php if($strLocation != ""){?>
					strLocation = "<?php echo($strLocation);?>";
					<?php } else{?>
					strLocation = "../modulo_BsAtividade/STindex.php";
					<?php }?>
					submeterForm();
				} else{
					return(false);
				}
			}

			function cancelar() {
				<?php if($strLocation != ""){?>
				document.location.href = "<?php echo($strLocation);?>";
				<?php } else{?>
				document.location.href = "../modulo_BsAtividade/STindex.php";
				<?php }?>
			}
			
			function aplicar() {
				if(validaCampos()){
					strLocation = "../modulo_BsAtividade/STinsatividade.php";
					submeterForm();
				} else{
					return(false);
				}
			}

			function submeterForm() {
				document.formstatic.DEFAULT_LOCATION.value = strLocation;
				document.formstatic.submit();
			}
		</script>
	</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("710","",getTText("bs_atividade",C_UCWORDS)." - (".getTText("insercao_do_modelo",C_NONE).")",CL_CORBAR_GLASS_1); ?>
<form name="formstatic" action="STinsatividademodeloexec.php" method="post">
	<input type="hidden" name="DEFAULT_LOCATION" id="DEFAULT_LOCATION" value="" />
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="690" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15px 0px 0px 15px;"><strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong></td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 70px 10px 70px;">
			<table cellspacing="2" cellpadding="4" border="0" width="100%">
				<!-- DADOS DIALOG -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_da_atividade",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("atividade_modelo",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_bs_atividade_modelo" id="var_bs_atividade_modelo" style="width:280px;">
							<?php echo(montaCombo($objConn,"SELECT cod_atividade, cod_atividade||' - '||bs_categoria.nome||' - '||titulo AS nome FROM bs_atividade LEFT JOIN bs_categoria ON (bs_categoria.cod_categoria = bs_atividade.cod_categoria) WHERE modelo = TRUE","cod_atividade","nome",""));?>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><input type="radio" name="var_bs_atividade_opcao" id="var_bs_atividade_opcao" class="inputclean" value="S" checked="checked" /></td>
					<td align="left">
						<?php echo(getTText("desejo_alterar_data_das_tarefas",C_NONE));?>
						<input type="text" name="var_bs_atividade_data_ini" id="var_bs_atividade_data_ini" size="12" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" />
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><input type="radio" name="var_bs_atividade_opcao" id="var_bs_atividade_opcao" class="inputclean" value="N" /></td>
					<td align="left"><?php echo(getTText("desejo_manter_data_das_tarefas",C_NONE));?></td>
				</tr>
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
					<td width="50%" style="padding-left:45px;">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td align="right" width="23%" style="padding-right:8px;"><img src="../img/mensagem_info.gif" /></td>
								<td align="left"  width="77%"><?php echo(getTText("info_mudanca_de_responsaveis",C_NONE));?></td>
							</tr>
						</table>
					</td>
					<td align="right">
						<button onClick="ok('formstatic');"><?php echo(getTText("ok",C_NONE));?></button>
					</td>
					<td width="10%" align="left">
						<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_NONE));?></button>
					</td>
					<td width="27%" align="left" style="padding-right:25px;">
						<button onClick="aplicar('formstatic');"><?php echo(getTText("aplicar",C_NONE));?></button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
	</table>
</form>
<?php athEndFloatingBox();?>
</center>
</body>
</html>