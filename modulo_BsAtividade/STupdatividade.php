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
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"UPD");

	// REQUESTS
	$intCodDado    	 = request("var_chavereg");			 // COD_ATIVIDADE / BS
	$strLocation	 = request("var_location");
	
	// ABRE OBJETO DE CONEXÃO COM DATABASE
	$objConn = abreDBConn(CFG_DB);
	
	// SQL QUE LOCALIZA A ATIVIDADE
	$objConn->beginTransaction();
	try{
		$strSQL = "
			SELECT 
				  bs_atividade.cod_atividade
				, bs_atividade.codigo
				, bs_atividade.tipo
				, bs_atividade.titulo
				, bs_atividade.situacao
				, bs_atividade.cod_categoria AS categoria
				, bs_atividade.prioridade
				, bs_atividade.id_responsavel
				, bs_atividade.descricao
				, bs_atividade.modelo
				, sys_usuario_responsavel.grp_user AS grupo_responsavel
			FROM  bs_atividade
			LEFT JOIN bs_categoria ON (bs_categoria.cod_categoria = bs_atividade.cod_categoria)
			LEFT JOIN sys_usuario sys_usuario_responsavel ON (sys_usuario_responsavel.id_usuario = bs_atividade.id_responsavel)
			WHERE bs_atividade.cod_atividade = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
		$objRS 	   = $objResult->fetch();
		
		// LOCALIZA A EQUIPE
		$strEquipe  = "";
		$strSQL = "SELECT id_usuario FROM bs_equipe WHERE dtt_inativo IS NULL AND cod_atividade = ".$intCodDado;
		$objResultE = $objConn->query($strSQL);
		if($objResultE->rowCount() > 0){
			foreach($objResultE as $objRSE){
				$strEquipe .= ";".getValue($objRSE,"id_usuario");
			}
		}
		
		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	
	if(getsession(CFG_SYSTEM_NAME."_id_usuario") != getValue($objRS,"id_responsavel")){
		mensagem("err_sql_titulo","aviso_atividade_nao_te_pertence","",$strLocation,"aviso",1);
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
 						   (document.getElementById('var_bs_codigo').value 		  == "")|| 
						   (document.getElementById('var_bs_tipo').value 		  == "")|| 
						   (document.getElementById('var_bs_titulo').value 		  == "")|| 
						   (document.getElementById('var_bs_situacao').value 	  == "")||
						   (document.getElementById('var_bs_categoria').value 	  == "")||
						   (document.getElementById('var_bs_prioridade').value 	  == "")|| 
						   (document.getElementById('var_bs_id_responsavel').value== "")|| 
						   (document.getElementById('var_bs_descricao').value 	  == "")
						  ) ? "\n\nDADOS DA TAREFA:" : "";
				strMSG += (document.getElementById('var_bs_codigo').value 		  == "") ? "\nCodigo" 		: "";
				strMSG += (document.getElementById('var_bs_tipo').value 		  == "") ? "\nTipo" 		: "";
				strMSG += (document.getElementById('var_bs_titulo').value 		  == "") ? "\nTítulo" 		: "";
				strMSG += (document.getElementById('var_bs_situacao').value 	  == "") ? "\nSituação" 	: "";
				strMSG += (document.getElementById('var_bs_categoria').value 	  == "") ? "\nCategoria"	: "";
				strMSG += (document.getElementById('var_bs_prioridade').value  	  == "") ? "\nPrioridade"	: "";
				// strMSG += (document.getElementById('var_bs_equipe').value  	  	  == "") ? "\nEquipe"	: "";
				strMSG += (document.getElementById('var_bs_id_responsavel').value == "") ? "\nResponsável"	: "";
				strMSG += (document.getElementById('var_bs_descricao').value  	  == "") ? "\nDescrição"	: "";
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
					strLocation = "../modulo_BsAtividade/STdata.php";
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
				document.location.href = "../modulo_BsAtividade/STdata.php";
				<?php }?>
			}
			
			function aplicar() {
				if(validaCampos()){
					strLocation = "../modulo_BsAtividade/STupdatividade.php?var_chavereg=<?php echo($intCodDado);?>";
					submeterForm();
				} else{
					return(false);
				}
			}

			function submeterForm() {
				document.formstatic.DEFAULT_LOCATION.value = strLocation;
				document.formstatic.submit();
			}
			
			function searchModulo(prType){
				if(prType == "pessoa"){
					var combo	  = document.getElementById("var_bs_tipo");
					// var combo     = document.forms[0].dbvar_str_tipoô;
					// strModulo     = (combo.options[combo.selectedIndex].value == "cad_pf") ? "CadPF" : "CadPJ";
					strModulo     = combo.options[combo.selectedIndex].value;
					switch(strModulo){
						case "cad_pf" :
						strModulo = "CadPF";
						break;
						
						case "cad_pj" :
						strModulo = "CadPJ";
						break;
						
						case "cad_pj_fornec" :
						strModulo = "CadPJFornec";
						break;
					}
					strComponente = "var_bs_codigo";
				}
				else if(prType == "centrocusto"){
					strModulo     = "FinCentroCusto";
					strComponente = "dbvar_num_cod_centro_custoô_000";
				}
				else if(prType == "planoconta"){
					strModulo     = "FinPlanoConta";
					strComponente = "dbvar_num_cod_plano_contaô_000";
				}
			
				AbreJanelaPAGE("../modulo_" + strModulo + "/?var_acao=single&var_fieldname=" + strComponente + "&var_formname=formstatic","800", "600");
			}
		</script>
	</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("710","",getTText("bs_atividade",C_UCWORDS)." - (".getTText("alteracao",C_NONE).")",CL_CORBAR_GLASS_1); ?>
<form name="formstatic" action="STupdatividadeexec.php" method="post">
	<input type="hidden" name="DEFAULT_LOCATION" id="DEFAULT_LOCATION" value="" />
	<input type="hidden" name="var_chavereg" id="var_chavereg" value="<?php echo($intCodDado);?>" />
	<table cellpadding="0" cellspacing="0" border="0" height="315" width="690" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15px 0px 0px 15px;"><strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong></td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 70px 10px 70px;">
			<table cellspacing="2" cellpadding="4" border="0" width="100%">
				<!-- DADOS AGENDA -->
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_da_atividade",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<!-- PARA LIGAÇÃO DA ATIVIDADE / BS -->
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cod_atividade",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo($intCodDado);?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cliente",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<table border="0px" cellpadding="0px" cellspacing="0px">
							<tr>
								<td style="padding-right:5px;" valign="middle"><input name="var_bs_codigo" id="var_bs_codigo" class='edtext' type='text' maxlength='10' value="<?php echo(getValue($objRS,"codigo"));?>" onKeyPress="Javascript:return validateNumKey(event);" style="width:40px;"></td>
								<td style="padding-right:3px;" valign="middle">
									<select name="var_bs_tipo" id="var_bs_tipo" class="edtext" size="1" style="width:120px;">
										<option value="cad_pf" <?php echo((getValue($objRS,"tipo") == "cad_pf") ? "selected='selected'" : "");?> ><?php echo(getTText("pessoa_fisica", C_UCWORDS)); ?></option>
										<option value="cad_pj" <?php echo((getValue($objRS,"tipo") == "cad_pj") ? "selected='selected'" : "");?> ><?php echo(getTText("pessoa_juridica", C_UCWORDS))?></option>
										<option value="cad_pj_fornec" <?php echo((getValue($objRS,"tipo") == "cad_pj_fornec") ? "selected='selected'" : "");?> ><?php echo(getTText("fornecedor", C_UCWORDS))?></option>
									</select>
								</td>
								<td valign="middle"><input type="button" value="<?php echo(getTText("buscar",C_UCWORDS)); ?>" onClick="searchModulo('pessoa');" class="inputclean"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("titulo",C_UCWORDS));?>:</strong></td>
					<td align="left"><input type="text" name="var_bs_titulo" id="var_bs_titulo" value="<?php echo(getValue($objRS,"titulo"));?>" maxlength="250" size="60" /></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("situacao",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_bs_situacao" id="var_bs_situacao" style="width:150px;">
							<option value="aberto" <?php echo((getValue($objRS,"situacao") == "aberto") ? "selected='selected'" : "");?> ><?php echo(getTText("aberto",C_TOUPPER));?></option>
							<option value="executando" <?php echo((getValue($objRS,"situacao") == "executando") ? "selected='selected'" : "");?> ><?php echo(getTText("executando",C_TOUPPER));?></option>
							<option value="fechado" <?php echo((getValue($objRS,"situacao") == "fechado") ? "selected='selected'" : "");?> ><?php echo(getTText("fechado",C_TOUPPER));?></option>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("categoria",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_bs_categoria" id="var_bs_categoria" style="width:150px;">
							<?php echo(montaCombo($objConn,"SELECT bs_categoria.cod_categoria, UPPER(bs_categoria.cod_categoria||' - '||bs_categoria.nome) AS nome FROM bs_categoria WHERE dtt_inativo IS NULL","cod_categoria","nome",getValue($objRS,"cod_categoria")));?>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("prioridade",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_bs_prioridade" id="var_bs_prioridade" style="width:150px;">
							<option value="normal" <?php echo((getValue($objRS,"prioridade") == "normal") ? "selected='selected'" : "");?> ><?php echo(getTText("normal",C_TOUPPER));?></option>
							<option value="baixa" <?php echo((getValue($objRS,"prioridade") == "baixa") ? "selected='selected'" : "");?> ><?php echo(getTText("baixa",C_TOUPPER));?></option>
							<option value="media" <?php echo((getValue($objRS,"prioridade") == "media") ? "selected='selected'" : "");?> ><?php echo(getTText("media",C_TOUPPER));?></option>
							<option value="alta" <?php echo((getValue($objRS,"prioridade") == "alta") ? "selected='selected'" : "");?> ><?php echo(getTText("alta",C_TOUPPER));?></option>
						</select>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("responsavel",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<select name="var_grupo_usuario_responsavel" id="var_grupo_usuario_responsavel" style="width:80px;" onChange="limpaSelect('var_bs_id_responsavel');ajaxDetailDataLoader((this.value != '') ? 'SELECT DISTINCT id_usuario, id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL AND oculto = FALSE AND grp_user = \'' + this.value + '\' ORDER BY id_usuario' : '','ajaxMontaCombo','var_bs_id_responsavel','','loader_ajax_responsavel');">
							<?php echo(montaCombo($objConn,"SELECT DISTINCT grp_user FROM sys_usuario ORDER BY grp_user","grp_user","grp_user",getValue($objRS,"grupo_responsavel")));?>
						</select>
						&nbsp;
						<select name="var_bs_id_responsavel" id="var_bs_id_responsavel" style="width:100px;">
							<?php echo(montaCombo($objConn,"SELECT DISTINCT id_usuario FROM sys_usuario WHERE dtt_inativo IS NULL AND oculto = FALSE AND grp_user = '".getValue($objRS,"grupo_responsavel")."' ORDER BY id_usuario","id_usuario","id_usuario",getValue($objRS,"grupo_responsavel")));?>
						</select>
						&nbsp;<span id="loader_ajax_responsavel"></span>
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong><?php echo(getTText("equipe",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<img src="../img/icon_equipe.gif" border="0" title="<?php echo(getTText("equipe",C_NONE));?>" onclick="AbreJanelaPAGE('../modulo_BsAtividade/STequipe.php?var_chavereg=<?php echo(getValue($objRS,"cod_atividade"));?>','500','200');" style="cursor:pointer;" /></td>
						<!-- input type="text" name="var_bs_equipe" id="var_bs_equipe" size="60" value="<?php echo($strEquipe);?>" />
						<br /><span class="comment_med"><php echo(getTText("obs_separacao_equipe",C_NONE))?></span-->
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(getTText("modelo",C_UCWORDS));?>:</strong></td>
					<td align="left">
						<input type="radio" name="var_bs_modelo" id="var_bs_modelo" value="S" class="inputclean" <?php echo((getValue($objRS,"modelo") == true) ? "checked='checked'" : "")?> > 
						<?php echo(getTText("sim",C_NONE));?>&nbsp;
						<input type="radio" name="var_bs_modelo" id="var_bs_modelo" value="N" class="inputclean" <?php echo((getValue($objRS,"modelo") == false) ? "checked='checked'" : "")?> >
						<?php echo(getTText("nao",C_NONE));?>&nbsp;
					</td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong>*<?php echo(getTText("descricao_da_atividade",C_UCWORDS));?>:</strong></td>
					<td align="left"><textarea name="var_bs_descricao" id="var_bs_descricao" style="width:350px;height:120px;"><?php echo(getValue($objRS,"descricao"));?></textarea></td>
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
					<td width="%" align="right">
						<button onClick="ok('formstatic');">
							<?php echo(getTText("ok",C_NONE));?>
						</button>
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
<script type="text/javascript">document.getElementById('var_bs_titulo').focus();</script>
</body>
</html>