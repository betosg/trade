<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$intCodDado = request('var_chavereg');

/*if($intCodDado == ''){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}*/

$objConn   = abreDBConn(CFG_DB);

/***************************************************************************************
/* --- INICIO DA BUSCA PELOS REGISTROS REFERENTES AO USUARIO QUE DESEJA SER ATUALIZADO
/***************************************************************************************

/* BUSCA PELO REGISTRO DA PJ */
try{
	$strSQL  = " SELECT cod_pj, razao_social, nome_fantasia, data_fundacao, senha, temp_executivo, temp_regional, temp_matriz, temp_contato, sys_usr_ins, sys_dtt_ins ";
	$strSQL .= " FROM cad_pj ";
	$strSQL .= " WHERE cod_pj =" . $intCodDado;
	
	$objConn->query($strSQL);	
	$objResult = $objConn->query($strSQL);
	$objRS = $objResult->fetch();
	
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}

/* BUSCA PELO REGISTRO DO DOCUMENTO DA PJ */
try{
	$strSQL  = " SELECT cod_pj, nome, valor "; 
	$strSQL .= " FROM cad_doc_pj ";
	$strSQL .= " WHERE cod_pj =" . $intCodDado;
	
	$objConn->query($strSQL);	
	$objResult = $objConn->query($strSQL);
	$objRSDoc = $objResult->fetch();
	
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}

/* BUSCA PELO REGISTRO DA ATIVIDADE DA PJ */
try{
	$strSQL  = " SELECT cod_atividade, cod_pj "; 
	$strSQL .= " FROM cad_atividade_pj ";
	$strSQL .= " WHERE cod_pj =" . $intCodDado;
	
	$objConn->query($strSQL);	
	$objResult = $objConn->query($strSQL);
	$objRSAtiv = $objResult->fetch();
	
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}

/* BUSCA PELO REGISTRO DA ATUACAO DA PJ */
try{
	$strSQL  = " SELECT cod_pj, cod_atuacao "; 
	$strSQL .= " FROM cad_pj_atuacao ";
	$strSQL .= " WHERE cod_pj =" . $intCodDado;
	
	$objConn->query($strSQL);	
	$objResult = $objConn->query($strSQL);
	$objRSAtua = $objResult->fetch();
	
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}

/* BUSCA PELO REGISTRO DA TIPO DA PJ */
try{
	$strSQL  = " SELECT cod_pj, cod_tipo "; 
	$strSQL .= " FROM cad_tipo_pj ";
	$strSQL .= " WHERE cod_pj =" . $intCodDado . " ORDER BY 2 ASC";
	
	$objConn->query($strSQL);	
	$objResult = $objConn->query($strSQL);
	$objRSTipo = $objResult->fetch();
	
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}

/* BUSCA PELO REGISTRO DO ENDEREÇO DA PJ */
try{
	$strSQL  = " SELECT cod_endereco, cod_pj, cep, logradouro, numero, complemento, endereco, bairro, cidade, estado, pais, fone, fone_extra1, fone_extra2, fone_extra3, email, email_extra, homepage, ordem "; 
	$strSQL .= " FROM cad_endereco_pj ";
	$strSQL .= " WHERE cod_pj =" . $intCodDado . " AND ordem = 10 ORDER BY 1 ASC";
	
	$objConn->query($strSQL);	
	$objResult = $objConn->query($strSQL);
	$objRSEnd = $objResult->fetch();
	
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();	
}
/*******************
/* --- FIM DA BUSCA 
/*******************

/* CONVERTE A DATA PARA MODELO BRASILEIRO E TROCA TRACO POR BARRA */
$dtDataFundacao = cDate(CFG_LANG,getValue($objRS,"data_fundacao"), false);
$dtDataFundacao = str_replace("-","/",$dtDataFundacao);
/******************************************************************/
?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--

function pesquisaPJ(){
	var var_nome_doc = document.forminsfastPJ.dbvar_str_nome_doc_new.value;
	var var_valor = document.forminsfastPJ.dbvar_str_valor_new.value;
	var var_codigo = document.forminsfastPJ.var_chavereg.value;
	var var_aux1 = '';
	var var_aux2 = '';
	var strParam = '';
	
	if ((var_nome_doc == 'CNPJ') && (var_valor != '')) {
		objAjax = createAjax();
		var cnpj = checkCNPJ (document.forminsfastPJ.dbvar_str_valor_new.value, true);				
		
		if (!cnpj) document.forminsfastPJ.dbvar_str_valor_new.value = '';
		
		objAjax.onreadystatechange = function(){
			if(objAjax.readyState == 4) {
				if(objAjax.status == 200) {
					/********************/
					/* SE CNPJ É VALIDO */
					/********************/									
					if(cnpj){								
						var arrPJ = objAjax.responseText.split(";");
						//alert(objAjax.responseText.length);
						//alert(objAjax.responseText);
						/********************************************************************************************************************************/
						/*                                         VERIFICAÇÃO DO CNPJ JÁ CADASTRADO                                   					*/
						/*            (é comparado com 1 (depois com 110) pois retorna um caracter que nao é visivel [undefined] sobre o CNPJ)          */
						/********************************************************************************************************************************/									
						var_aux1 = '';
						var_aux2 = '';
						
						if(objAjax.responseText.length > 1) {
							var_aux1 = arrPJ[0];
							var_aux2 = arrPJ[1];
						}
						
						if ((var_aux1 != var_codigo) && (var_aux2 != '')) {
							document.forminsfastPJ.dbvar_str_valor_new.value = '';
							alert('CNPJ ' + var_aux2 + ' já está cadastrado!');
							return(false);
						}
						else {
							return(true);
						}
					}//FIM DA FUNCTION					
				}
			}
		}
		
		strParam = "var_cnpj=" + var_valor;
		objAjax.open("GET", "../_ajax/returninfopj.php?" + strParam,  true); 
		objAjax.send(null);
	}
	else {
		return(true);
	}
}

function submeterForm(){
	document.forminsfastPJ.var_location.value = "STupdfastPJ.php?var_chavereg="+<?php echo($intCodDado); ?>;
	document.forminsfastPJ.submit();
}

function validateNumKey (){
	var inputKey = event.keyCode;
	var returnCode = true;

	if ( inputKey > 47 && inputKey < 58 || inputKey == 32){
		return;
	} else {
		returnCode = false;
		event.keyCode = 0;
	}
	event.returnValue = returnCode;
}

function FormataInputData(prCampo) {
	
	prObject = eval('document.forminsfastPJ.' + prCampo);
	
	var currValue, arrValue, inputKey;
	
	currValue = prObject.value;
	arrValue = currValue.split("/").join("");
	inputKey = event.keyCode;

	if (inputKey!=8 && inputKey!=127 && inputKey!=39 && inputKey!=37 && inputKey!=46) {
		if (arrValue.length>3)
			if (arrValue.substr(2,2)<13)
				prObject.value = arrValue.substr(0,2) + '/' + arrValue.substr(2,2) + '/' + arrValue.substr(4);
			else
				prObject.value = arrValue.substr(0,2) + '/12/' + arrValue.substr(4);
		else if (arrValue.length>1) 
				if (arrValue.substr(0,2)<32)
					prObject.value = arrValue.substr(0,2) + '/' + arrValue.substr(2)
				else
					prObject.value = '31/' + arrValue.substr(2);
	}
}
	window.onload = function(){
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodDado); ?>').style.height = 0;
			window.parent.document.getElementById('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo($intCodDado); ?>').style.height = document.body.scrollHeight;
		}
	 
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("630","none","PESSOA JURÍDICA (Atualização Rápida)",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
	   		<form name="forminsfastPJ" action="STupdfastPJexec.php" method="post">
				<input type="hidden" name="var_location" value="">
				<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado);?>">
				<input type="hidden" name="dbvar_cod_atividade" value="<?php echo(getValue($objRSAtiv,"cod_atividade"));?>">
				<input type="hidden" name="dbvar_cod_atuacao" value="<?php echo(getValue($objRSAtua,"cod_atuacao"));?>">
				<input type="hidden" name="dbvar_str_nome_doc" value="<?php echo(getValue($objRSDoc,"nome"));?>">
				<input type="hidden" name="dbvar_str_valor_doc" value="<?php echo(getValue($objRSDoc,"valor"));?>">
				<input type="hidden" name="dbvar_cod_tipo" value="<?php echo(getValue($objRSTipo,"cod_tipo"));?>">
				<input type="hidden" name="dbvar_cod_endereco" value="<?php echo(getValue($objRSEnd,"cod_endereco"));?>">
				<input name="dbvar_str_sys_usr_upd" id="dbvar_str_sys_usr_upd" type="hidden" value="<?php echo(getsession(CFG_SYSTEM_NAME . "_id_usuario")); ?>">
				
				<tr><td height="22" style="padding:10px"><b>Preencha os campos abaixo</b></td></tr>
				<tr>
					<td align="center" valign="top">
						<table width="550" border="0" cellspacing="0" cellpadding="4">
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_razao_social_000">*<strong>Razão Social:</strong></label>
								</td>
								<td nowrap align="left" width="99%" >
									<input name="dbvar_str_razao_social" id="dbvar_str_razao_social_000" value="<?php echo(getValue($objRS,"razao_social"));?>" type="text" size="60" maxlength="120"   title="Razão Social" tabindex="1"></td>
							</tr>
							<tr bgcolor="#FFFFFF"><td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
											<label for="dbvar_str_nome_fantasia_000">
												*<strong>Nome Fantasia:</strong>
											</label>
										</td>
										<td nowrap align="left" width="99%" >
									<input name="dbvar_str_nome_fantasia" id="dbvar_str_nome_fantasia_000" value="<?php echo(getValue($objRS,"nome_fantasia"));?>" type="text" size="50" maxlength="22"   title="Nome Fantasia" tabindex="2"></td>
							</tr><tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_date_data_fundacao_000">
										<strong>Data Fundação:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" >
									<input name="dbvar_date_data_fundacao" id="dbvar_date_data_fundacao" value="<?php echo($dtDataFundacao);?>" type="text" size="10" maxlength="10" onKeyUp="Javascript:FormataInputData(this.name);" onKeyPress="Javascript:return validateNumKey(event);"  title="Data Fundação" tabindex="3">
								</td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_temp_executivo_000">
										<strong>Executivo:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_temp_executivo" id="dbvar_str_temp_executivo_000" value="<?php echo(getValue($objRS,"temp_executivo"));?>" type="text" size="50" maxlength=""   title="Executivo" tabindex="4"></td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_temp_regional_000">
										<strong>Regional:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_temp_regional" id="dbvar_str_temp_regional_000" value="<?php echo(getValue($objRS,"temp_regional"));?>" type="text" size="50" maxlength=""   title="Regional" tabindex="5"></td>
							</tr>
							<tr bgcolor="#FFFFFF"> 
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_temp_matriz_000">
										<strong>Matriz:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" >
									<select name="dbvar_str_temp_matriz" id="dbvar_str_temp_matriz_000"  style="width:100px" size="1" title="Matriz" tabindex="6">
										<option value="" selected></option>
										<option value="MATRIZ" <?php if (getValue($objRS,"temp_matriz") == 'MATRIZ'){echo('selected');} ?>><i>MATRIZ</i></option>
										<option value="FILIAL" <?php if (getValue($objRS,"temp_matriz") == 'FILIAL'){echo('selected');} ?>><i>FILIAL</i></option>
									</select>			
								</td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_temp_contato_000"><strong>Contato:</strong></label>
								</td>
								<td nowrap align="left" width="99%" >
									<input name="dbvar_str_temp_contato" id="dbvar_str_temp_contato_000" value="<?php echo(getValue($objRS,"temp_contato"));?>" type="text" size="50" maxlength=""   title="Contato" tabindex="7">
									<span class="comment_med">&nbsp;Contato Comercial Gerência</span>
								</td>
							</tr>
							<tr bgcolor="#FFFFFF"><td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_senha_000"><strong>Senha:</strong></label>
								</td>
							  <td nowrap align="left" width="99%" >
									<input name="dbvar_str_senha" id="dbvar_str_senha_000" value="<?php echo(getValue($objRS,"senha"));?>" type="text" size="" maxlength="" title="Senha loja" tabindex="8">
								  <span class="comment_med">&nbsp;
								  Senha utilizada na loja. </span> <br>
							    Preencha apenas para	PJ	que<strong> n&atilde;o	for Expositor e Prestador de Servi&ccedil;o</strong> </td>
							</tr>
							<tr bgcolor="#FAFAFA"> 
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_nome_doc_000">
										*<strong>Documento:</strong>
									</label>
								</td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left">
												<select name="dbvar_str_nome_doc_new" id="dbvar_str_nome_doc" size="1" title="documento" tabindex="9" onBlur="pesquisaPJ();">
													<?php echo(montaCombo($objConn,"SELECT cod_doc_tp, nome FROM cad_doc_tp WHERE tipo ilike 'j' OR tipo ilike 'a'","nome","nome", getValue($objRSDoc,"nome"))); ?>
												</select>
												</td>
											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_valor_000">
													<strong></strong>
												</label>
											</td>
											<td nowrap align="left" width="99%" ><input name="dbvar_str_valor_new" id="dbvar_str_valor_000" value="<?php echo(getValue($objRSDoc,"valor"));?>" type="text" size="20" maxlength="50" title="número" tabindex="10" onBlur="pesquisaPJ();"></td>
										</tr>
										<tr>	
											<td nowrap align="left" colspan="3">
												<span class="comment_med">&nbsp;O n&uacute;mero do documento do tipo  <strong>DOCX_PJ</strong> deve conter 5 digitos.</span>
											</td>
										</tr>
									</table>
								</td>
							</tr>															
							<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_str_atividade_000">
											<strong>Atividade:</strong>
										</label>
								</td>
								<td nowrap align="left" width="99%" >
									<select name="dbvar_cod_atividade_new" class="edtext" style="width:300px;" tabindex="10">
										<?php echo(montaCombo($objConn,"SELECT cod_atividade, nome FROM cad_atividade ORDER BY nome","cod_atividade","nome",getValue($objRSAtiv,"cod_atividade"))); ?>
									</select>									
								</td>
							</tr>									
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_str_atuacao_000">
											<strong>Atuação:</strong>
										</label>
								</td>
								<td nowrap align="left" width="99%" >
									<select name="dbvar_cod_atuacao_new" class="edtext" style="width:300px;" tabindex="10">
										<option value=""selected></option>
										<?php echo(montaCombo($objConn,"SELECT cod_atuacao, nome FROM cad_atuacao ORDER BY 2","cod_atuacao","nome",getValue($objRSAtua,"cod_atuacao"))); ?>
									</select>
									<br> <span class="comment_med">&nbsp;Caso este combo esteja vazio, deve primeiro cadastrar Atuações.</span>								
								</td>
							</tr>
							<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_str_tipo_000">
											<strong>Tipo:</strong>
										</label>
								</td>
								<td nowrap align="left" width="99%" >
									<select name="dbvar_cod_tipo_new" class="edtext" style="width:300px;" tabindex="10">
										<option value=""></option>
										<?php echo(montaCombo($objConn,"SELECT cod_tipo, nome FROM cad_tipo ORDER BY nome","cod_tipo","nome",getValue($objRSTipo,"cod_tipo"))); ?>
									</select>									
								</td>
							</tr>									
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>ENDEREÇO</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>								
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_cep_000">
										<strong>CEP:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_cep" id="dbvar_str_cep_000" value="<?php echo(getValue($objRSEnd,"cep"));?>" type="" size="" maxlength="" title="CEP" tabindex="10"></td>
							</tr>
							<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_str_logradouro_000">
											<strong>logradouro:</strong>
										</label>
								</td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left"><input name="dbvar_str_logradouro" id="dbvar_str_logradouro" value="<?php echo(getValue($objRSEnd,"logradouro"));?>" type="text" size="50" maxlength="255" title="logradouro" tabindex="11" onChange="dbvar_str_endereco.value = dbvar_str_endereco.value + this.value;"></td>
											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_numero_000">
													<strong>&nbsp; número:</strong>
												</label>
											</td>
											<td nowrap> <input name="dbvar_str_numero" id="dbvar_str_numero_000" value="<?php echo(getValue($objRSEnd,"numero"));?>" type="text" size="10" maxlength="10" title="número" tabindex="12" onChange="dbvar_str_endereco.value = dbvar_str_endereco.value + ', ' + this.value;"></td>
											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_complemento_000">
													<strong>&nbsp; compl.:</strong>
												</label>
											</td>
											<td nowrap align="left" width="99%" ><input name="dbvar_str_complemento" id="dbvar_str_complemento" value="<?php echo(getValue($objRSEnd,"complemento"));?>" type="text" size="10" maxlength="10" title="compl." tabindex="13" onChange="dbvar_str_endereco.value = dbvar_str_endereco.value + ', ' + this.value;"></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_endereco_000">
										<strong>endereço:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_endereco" id="dbvar_str_endereco" value="<?php echo(getValue($objRSEnd,"endereco"));?>" type="text" size="50" maxlength="255"   title="endereço" tabindex="14"></td>
							</tr>
							<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_bairro_000">
										<strong>bairro:</strong>
									</label>
								</td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left"><input name="dbvar_str_bairro" id="dbvar_str_bairro_000" value="<?php echo(getValue($objRSEnd,"bairro"));?>" type="text" size="20" maxlength="30"   title="bairro" tabindex="15"></td>
  											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_cidade_000">
													<strong>&nbsp; cidade:</strong>
												</label>
											</td>
											<td nowrap align="left" width="99%" ><input name="dbvar_str_cidade" id="dbvar_str_cidade_000" value="<?php echo(getValue($objRSEnd,"cidade"));?>" type="text" size="20" maxlength="30" title="cidade" tabindex="16"></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_estado_000">
										<strong>UF:</strong>
									</label>
								</td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left">	
												<select name="dbvar_str_estado" id="dbvar_str_estado_000" style="width:40px" size="1" title="UF" tabindex="17">
													<option value="AC" <?php if (getValue($objRSEnd,"estado") == 'AC'){echo('selected');} ?>><i>AC</i></option>
													<option value="AL" <?php if (getValue($objRSEnd,"estado") == 'AL'){echo('selected');} ?>><i>AL</i></option>
													<option value="AP" <?php if (getValue($objRSEnd,"estado") == 'AP'){echo('selected');} ?>><i>AP</i></option>
													<option value="AM" <?php if (getValue($objRSEnd,"estado") == 'AM'){echo('selected');} ?>><i>AM</i></option>
													<option value="BA" <?php if (getValue($objRSEnd,"estado") == 'BA'){echo('selected');} ?>><i>BA</i></option>
													<option value="CE" <?php if (getValue($objRSEnd,"estado") == 'CE'){echo('selected');} ?>><i>CE</i></option>
													<option value="DF" <?php if (getValue($objRSEnd,"estado") == 'DF'){echo('selected');} ?>><i>DF</i></option>
													<option value="ES" <?php if (getValue($objRSEnd,"estado") == 'ES'){echo('selected');} ?>><i>ES</i></option>
													<option value="GO" <?php if (getValue($objRSEnd,"estado") == 'GO'){echo('selected');} ?>><i>GO</i></option>
													<option value="MA" <?php if (getValue($objRSEnd,"estado") == 'MA'){echo('selected');} ?>><i>MA</i></option>
													<option value="MT" <?php if (getValue($objRSEnd,"estado") == 'MT'){echo('selected');} ?>><i>MT</i></option>
													<option value="MS" <?php if (getValue($objRSEnd,"estado") == 'MS'){echo('selected');} ?>><i>MS</i></option>
													<option value="MG" <?php if (getValue($objRSEnd,"estado") == 'MG'){echo('selected');} ?>><i>MG</i></option>
													<option value="PA" <?php if (getValue($objRSEnd,"estado") == 'PA'){echo('selected');} ?>><i>PA</i></option>
													<option value="PB" <?php if (getValue($objRSEnd,"estado") == 'PB'){echo('selected');} ?>><i>PB</i></option>
													<option value="PR" <?php if (getValue($objRSEnd,"estado") == 'PR'){echo('selected');} ?>><i>PR</i></option>
													<option value="PE" <?php if (getValue($objRSEnd,"estado") == 'PE'){echo('selected');} ?>><i>PE</i></option>
													<option value="PI" <?php if (getValue($objRSEnd,"estado") == 'PI'){echo('selected');} ?>><i>PI</i></option>
													<option value="RJ" <?php if (getValue($objRSEnd,"estado") == 'RJ'){echo('selected');} ?>><i>RJ</i></option>
													<option value="RN" <?php if (getValue($objRSEnd,"estado") == 'RN'){echo('selected');} ?>><i>RN</i></option>
													<option value="RS" <?php if (getValue($objRSEnd,"estado") == 'RS'){echo('selected');} ?>><i>RS</i></option>
													<option value="RO" <?php if (getValue($objRSEnd,"estado") == 'RO'){echo('selected');} ?>><i>RO</i></option>
													<option value="RR" <?php if (getValue($objRSEnd,"estado") == 'RR'){echo('selected');} ?>><i>RR</i></option>
													<option value="SC" <?php if (getValue($objRSEnd,"estado") == 'SC'){echo('selected');} ?>><i>SC</i></option>
													<option value="SP" <?php if (getValue($objRSEnd,"estado") == 'SP'){echo('selected');} ?>><i>SP</i></option>
													<option value="SE" <?php if (getValue($objRSEnd,"estado") == 'SE'){echo('selected');} ?>><i>SE</i></option>
													<option value="TO" <?php if (getValue($objRSEnd,"estado") == 'TO'){echo('selected');} ?>><i>TO</i></option>
												</select>
											</td>
  											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_pais_000">
													<strong>&nbsp; país:</strong>
												</label>
											</td>
											<td nowrap align="left" width="99%" >
												<select name="dbvar_str_pais" id="dbvar_str_pais_000" style="width:210px" size="1" title="país" tabindex="18">
												<?php 
												echo(montaCombo($objConn, " SELECT nome FROM lc_pais ORDER BY nome ", "nome", "nome", getValue($objRSEnd,"pais"), "")); 
												?>
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_email_000">
										<strong>e-mail:</strong>
									</label>
								</td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left"><input name="dbvar_str_email" id="dbvar_str_email_000" value="<?php echo(getValue($objRSEnd,"email"));?>" type="text" size="35" maxlength="255"   title="e-mail" tabindex="29"></td>
  											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_email_extra_000">
													<strong>&nbsp; e-mail extra:</strong>
												</label>
											</td>
											<td nowrap align="left" width="99%" ><input name="dbvar_str_email_extra" id="dbvar_str_email_extra_000" value="<?php echo(getValue($objRSEnd,"email_extra"));?>" type="text" size="35" maxlength="255"   title="e-mail extra" tabindex="30"></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_homepage_000">
										<strong>homepage:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_homepage" id="dbvar_str_homepage_000" value="<?php echo(getValue($objRSEnd,"homepage"));?>" type="text" size="35" maxlength="255"   title="homepage" tabindex="31"></td>
							</tr>
							<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_fone_000">
										<strong>fone 1:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_fone" id="dbvar_str_fone" value="<?php echo(getValue($objRSEnd,"fone"));?>" type="" size="" maxlength=""   title="fone 1" tabindex="32"></td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_fone_extra1_000">
										<strong>fone 2:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_fone_extra1" id="dbvar_str_fone_extra1_000" value="<?php echo(getValue($objRSEnd,"fone_extra1"));?>" type="" size="" maxlength=""   title="fone 2" tabindex="33"></td>
							</tr>
							<tr>  
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_fone_extra2_000">
										<strong>fone 3:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_fone_extra2" id="dbvar_str_fone_extra2_000" value="<?php echo(getValue($objRSEnd,"fone_extra2"));?>" type="" size="" maxlength=""   title="fone 3" tabindex="34"></td>
							</tr>
							<tr bgcolor="#FAFAFA">  
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_fone_extra3_000">
										<strong>fone 4:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_fone_extra3" id="dbvar_str_fone_extra3_000" value="<?php echo(getValue($objRSEnd,"fone_extra3"));?>" type="" size="" maxlength=""   title="fone 4" tabindex="35"></td>
							</tr>	
							<tr>  
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_int_ordem_000">
										<strong>Ordem:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_int_ordem" id="dbvar_int_ordem" value="<?php if (getValue($objRSEnd,"ordem") == ''){echo('10');}else{echo(getValue($objRSEnd,"ordem"));}?>" type="" size="5" dir="rtl" maxlength=""   title="ordem" tabindex="36"></td>
							</tr>							
							<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>
							<tr><td height="1" colspan="3" bgcolor="#DBDBDB"></td></tr>																									
						</table>
					</td>
				</tr>
				<tr>
					<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
<!-- 					
						<button onClick="document.forminsfastPJ.submit();"><?php //echo(getTText("ok",C_UCWORDS)); ?></button>
						<button onClick="history.back();return false;"><?php //echo(getTText("cancelar",C_UCWORDS)); ?></button>
-->
						<button onClick="submeterForm();return false;"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
					</td>
				</tr>					
		  		</form>
			</table>
			<?php athEndFloatingBox(); ?>
		</td>
	</tr>
</table>
</body>
</html>
<?php ?>