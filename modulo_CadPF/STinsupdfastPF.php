<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");

$strOperacao  = request("var_oper");       // Operação a ser realizada
$intCodDado   = request("var_chavereg");   // Código chave da página

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$objConn = abreDBConn(CFG_DB);

if ($strOperacao == 'INS') {
	$strRotulo = "Inserção";
	
	$strNome  		  = '';
	$strApelido		  = '';
	$dtDataNasc       = '';
	$strSexo  		  = 'm';
	$strEmail         = '';
	$strEmailExtra    = '';
	$strWebsite       = '';
	$strFoto          = '';
	$strEstadoCivil   = '';
	$strInstrucao     = '';
	$strNacionalidade = '';
	$strNaturalidade  = '';
	$strObs           = '';
	$strStatus        = '';
	$strNomePai       = '';
	$strNomeMae       = '';
	
	$strCPF  = '';
	$strRG   = '';
	$strCNH  = '';
	$strPIS  = '';
	$strCTPS = '';
	
	$strEndPrinCEP         = '';
	$strEndPrinLogradouro  = '';
	$strEndPrinNumero      = '';
	$strEndPrinComplemento = '';
	$strEndPrinBairro      = '';
	$strEndPrinCidade      = '';
	$strEndPrinEstado      = 'SP';
	$strEndPrinPais        = 'Brasil';
	$strEndPrinFone1       = '';
	$strEndPrinFone2       = '';
	$strEndPrinFone3       = '';
	$strEndPrinFone4       = '';
	$strEndPrinFone5       = '';
	$strEndPrinFone6       = '';
	
	$strEndComCEP         = '';
	$strEndComLogradouro  = '';
	$strEndComNumero      = '';
	$strEndComComplemento = '';
	$strEndComBairro      = '';
	$strEndComCidade      = '';
	$strEndComEstado      = '';
	$strEndComPais        = '';
	$strEndComFone1       = '';
	$strEndComFone2       = '';
	$strEndComFone3       = '';
	$strEndComFone4       = '';
	$strEndComFone5       = '';
	$strEndComFone6       = '';
}
else {
	$strRotulo = "Alteração";
	
	try {
		$strSQL  = " SELECT t1.nome, t1.data_nasc, t1.sexo, t1.email, t1.email_extra, t1.website, t1.foto, t1.apelido ";
		$strSQL .= "      , t1.nome_pai, t1.nome_mae, t1.estado_civil, t1.instrucao, t1.nacionalidade, t1.naturalidade, t1.obs ";
		$strSQL .= "      , t1.cpf, t1.rg, t1.cnh, t1.pis, t1.ctps ";
		$strSQL .= "      , t1.endprin_cep, t1.endprin_logradouro, t1.endprin_numero, t1.endprin_complemento, t1.endprin_bairro ";
		$strSQL .= "      , t1.endprin_cidade, t1.endprin_estado, t1.endprin_pais, t1.endprin_fone1, t1.endprin_fone2 ";
		$strSQL .= "      , t1.endprin_fone3, t1.endprin_fone4, t1.endprin_fone5, t1.endprin_fone6 ";
		$strSQL .= "      , t1.endcom_cep, t1.endcom_logradouro, t1.endcom_numero, t1.endcom_complemento, t1.endcom_bairro ";
		$strSQL .= "      , t1.endcom_cidade, t1.endcom_estado, t1.endcom_pais, t1.endcom_fone1, t1.endcom_fone2 ";
		$strSQL .= "      , t1.endcom_fone3, t1.endcom_fone4, t1.endcom_fone5, t1.endcom_fone6 ";
		$strSQL .= " FROM cad_pf t1 ";
		$strSQL .= " WHERE t1.cod_pf = " . $intCodDado;
		
		$objResult = $objConn->query($strSQL);
		
		if ($objResult->rowCount() > 0) {
			$objRS = $objResult->fetch();
			
			$strNome  		  = getValue($objRS, "nome");
			$strApelido		  = getValue($objRS, "apelido");
			$dtDataNasc       = dDate(CFG_LANG, getValue($objRS, "data_nasc"), false);
			$strSexo  		  = getValue($objRS, "sexo");
			$strEmail         = getValue($objRS, "email");
			$strEmailExtra    = getValue($objRS, "email_extra");
			$strWebsite       = getValue($objRS, "website");
			$strFoto          = getValue($objRS, "foto");
			$strEstadoCivil   = getValue($objRS, "estado_civil");
			$strInstrucao     = getValue($objRS, "instrucao");
			$strNacionalidade = getValue($objRS, "nacionalidade");
			$strNaturalidade  = getValue($objRS, "naturalidade");
			$strObs           = getValue($objRS, "obs");
			$strNomePai       = getValue($objRS, "nome_pai");
			$strNomeMae       = getValue($objRS, "nome_mae");
			
			$strCPF  = getValue($objRS, "cpf");
			$strRG   = getValue($objRS, "rg");
			$strCNH  = getValue($objRS, "cnh");
			$strPIS  = getValue($objRS, "pis");
			$strCTPS = getValue($objRS, "ctps");
			
			$strEndPrinCEP         = getValue($objRS, "endprin_cep");
			$strEndPrinLogradouro  = getValue($objRS, "endprin_logradouro");
			$strEndPrinNumero      = getValue($objRS, "endprin_numero");
			$strEndPrinComplemento = getValue($objRS, "endprin_complemento");
			$strEndPrinBairro      = getValue($objRS, "endprin_bairro");
			$strEndPrinCidade      = getValue($objRS, "endprin_cidade");
			$strEndPrinEstado      = getValue($objRS, "endprin_estado");
			$strEndPrinPais        = getValue($objRS, "endprin_pais");
			$strEndPrinFone1       = getValue($objRS, "endprin_fone1");
			$strEndPrinFone2       = getValue($objRS, "endprin_fone2");
			$strEndPrinFone3       = getValue($objRS, "endprin_fone3");
			$strEndPrinFone4       = getValue($objRS, "endprin_fone4");
			$strEndPrinFone5       = getValue($objRS, "endprin_fone5");
			$strEndPrinFone6       = getValue($objRS, "endprin_fone6");
			
			$strEndComCEP         = getValue($objRS, "endcom_cep");
			$strEndComLogradouro  = getValue($objRS, "endcom_logradouro");
			$strEndComNumero      = getValue($objRS, "endcom_numero");
			$strEndComComplemento = getValue($objRS, "endcom_complemento");
			$strEndComBairro      = getValue($objRS, "endcom_bairro");
			$strEndComCidade      = getValue($objRS, "endcom_cidade");
			$strEndComEstado      = getValue($objRS, "endcom_estado");
			$strEndComPais        = getValue($objRS, "endcom_pais");
			$strEndComFone1       = getValue($objRS, "endcom_fone1");
			$strEndComFone2       = getValue($objRS, "endcom_fone2");
			$strEndComFone3       = getValue($objRS, "endcom_fone3");
			$strEndComFone4       = getValue($objRS, "endcom_fone4");
			$strEndComFone5       = getValue($objRS, "endcom_fone5");
			$strEndComFone6       = getValue($objRS, "endcom_fone6");
		}
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objResult->closeCursor();

}

?> 
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--

function cancelar(){
	document.formeditor.var_location.value = "STinsupdfastPF.php?var_chavereg=<?php echo($intCodDado); ?>?var_oper=<?php echo($strOperacao); ?>";
	document.formeditor.submit();
}

//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("630","none","COLABORADOR (" . $strRotulo . ")",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;" cellspacing="0" cellpadding="4">
	   		<form name="formeditor" action="STinsupdfastPFexec.php" method="post">
				<input type="hidden" name="var_oper" value="<?php echo($strOperacao); ?>">
				<input type="hidden" name="var_chavereg" value="<?php echo($intCodDado); ?>">
		<tr><td height="22" style="padding:10px"><b>Preencha os campos abaixo</b></td></tr>
		<tr> 
		  <td align="center" valign="top">
			<table width="550" border="0" cellspacing="0" cellpadding="4">
								<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DADOS</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="#FAFAFA">
									 <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_nome">
											<strong>*nome:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_nome" id="var_nome" value="<?php echo($strNome); ?>" type="text" size="50" maxlength="100"   title="nome"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_apelido">
											<strong>Apelido:</strong>
										</label>
									</td>
						<td nowrap align="left" width="99%" ><input name="var_apelido" id="var_apelido" value="<?php echo($strApelido); ?>" type="text" size="30" maxlength="50"   title="Apelido">
					    <span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_date_data_nasc">
											<strong>data nascimento:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_data_nasc" id="var_data_nasc" value="<?php echo($dtDataNasc); ?>" type="text" size="10" maxlength="10" onKeyUp="Javascript:FormataInputData(this);" onKeyPress="Javascript:return validateNumKey(event);"  title="data nascimento"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_sexo">
											<strong>*Sexo:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
									<select name="var_sexo" id="var_sexo"  style="width:120px" size="1" title="Sexo">
										<option value="m" <?php if ($strSexo == "m") echo("selected"); ?>><i>MASCULINO</i></option>
										<option value="f" <?php if ($strSexo == "f") echo("selected"); ?>><i>FEMINININO</i></option>
									</select><span class="comment_med">&nbsp;</span>
									</td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_email">
											<strong>e-mail:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_email" id="var_email" value="<?php echo($strEmail); ?>" type="text" size="60" maxlength="255"   title="e-mail"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_email_extra">
											<strong>e-mail extra:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_email_extra" id="var_email_extra" value="<?php echo($strEmailExtra); ?>" type="text" size="60" maxlength="255" title="e-mail extra"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_website">
											<strong>Website:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_website" id="var_website" value="<?php echo($strWebsite); ?>" type="text" size="60" maxlength="255" title="Website"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_foto">
											<strong>Foto:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input type="text" name="var_foto" id="var_foto" value="<?php echo($strFoto); ?>" size="50" readonly="true" title="Foto"><input type="button" name="btn_uploader" value="Upload" class="inputclean" onClick="callUploader('formeditor','var_foto','\\<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/fotospf\\');"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_estado_civil">
											<strong>Estado Civil:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
									<select name="var_estado_civil" id="var_estado_civil" style="width:180px" size="1" title="Estado Civil">
										<option value="" <?php if ($strEstadoCivil == "") echo("selected"); ?>></option>
										<option value="CASADO" <?php if ($strEstadoCivil == "CASADO") echo("selected"); ?>><i>Casado(a)</i></option>
										<option value="SEPARADO" <?php if ($strEstadoCivil == "SEPARADO") echo("selected"); ?>><i>Separado(a)</i></option>
										<option value="SOLTEIRO" <?php if ($strEstadoCivil == "SOLTEIRO") echo("selected"); ?>><i>Solteiro(a)</i></option>
										<option value="VIUVO" <?php if ($strEstadoCivil == "VIUVO") echo("selected"); ?>><i>Viúvo(a)</i></option>
										<option value="DIVORCIADO" <?php if ($strEstadoCivil == "DIVORCIADO") echo("selected"); ?>><i>Divorciado(a)</i></option>
										<option value="DESQUITADO" <?php if ($strEstadoCivil == "DESQUITADO") echo("selected"); ?>><i>Desquitado(a)</i></option>
										<option value="AMASIADO" <?php if ($strEstadoCivil == "AMASIADO") echo("selected"); ?>><i>Amasiado(a)</i></option>
									</select><span class="comment_med">&nbsp;</span>
									</td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_instrucao">
											<strong>Instrução:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
									<select name="var_instrucao" id="var_instrucao"  style="width:180px" size="1" title="Instrução">
										<option value="" <?php if ($strInstrucao == "") echo("selected"); ?>></option>
										<option value="1G_INCOMPLETO" <?php if ($strInstrucao == "1G_INCOMPLETO") echo("selected"); ?>><i>1° Grau Incompleto</i></option>
										<option value="1G_COMPLETO" <?php if ($strInstrucao == "1G_COMPLETO") echo("selected"); ?>><i>1° Grau Completo</i></option>
										<option value="2G_INCOMPLETO" <?php if ($strInstrucao == "2G_INCOMPLETO") echo("selected"); ?>><i>2° Grau Incompleto</i></option>
										<option value="2G_COMPLETO" <?php if ($strInstrucao == "2G_COMPLETO") echo("selected"); ?>><i>2° Grau Completo</i></option>
										<option value="CURSO_TECNICO" <?php if ($strInstrucao == "CURSO_TECNICO") echo("selected"); ?>><i>Curso Técnico</i></option>
										<option value="SUP_INCOMPLETO" <?php if ($strInstrucao == "SUP_INCOMPLETO") echo("selected"); ?>><i>Curso Superior Incompleto</i></option>
										<option value="SUP_COMPLETO" <?php if ($strInstrucao == "SUP_COMPLETO") echo("selected"); ?>><i>Curso Superior Completo</i></option>
										<option value="MESTRADO" <?php if ($strInstrucao == "MESTRADO") echo("selected"); ?>><i>Mestrado</i></option>
										<option value="DOUTORADO" <?php if ($strInstrucao == "DOUTORADO") echo("selected"); ?>><i>Doutorado</i></option>
									</select><span class="comment_med">&nbsp;</span>
									</td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_nacionalidade">
											<strong>Nacionalidade:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_nacionalidade" id="var_nacionalidade" value="<?php echo($strNacionalidade); ?>" type="text" size="35" maxlength="250"   title="Nacionalidade"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_naturalidade">
											<strong>Naturalidade:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_naturalidade" id="var_naturalidade" value="<?php echo($strNaturalidade); ?>" type="text" size="35" maxlength="250"   title="Naturalidade"><span class="comment_med">&nbsp;</span></td>
</tr>
								<tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_obs">
											<strong>Obs:</strong>
										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<textarea name="var_obs" id="var_obs" cols="60" rows="5"   title="Obs"><?php echo($strObs); ?></textarea><span class="comment_med">&nbsp;</span></td>
</tr>
								<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>FILIAÇÃO</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_nome_pai">
											<strong>Nome Pai:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_nome_pai" id="var_nome_pai" value="<?php echo($strNomePai); ?>" type="text" size="50" maxlength="250" title="Nome Pai"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_nome_mae">
											<strong>Nome Mãe:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_nome_mae" id="var_nome_mae" value="<?php echo($strNomeMae); ?>" type="text" size="50" maxlength="250" title="Nome Mãe"><span class="comment_med">&nbsp;</span></td>
</tr>
								<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DOCUMENTOS</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_cpf">
											<strong>*CPF:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" ><input name="var_cpf" id="var_cpf" value="<?php echo($strCPF); ?>" type="text" size="30" maxlength="120"   title="CPF">
								<span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_rg">
											<strong>RG:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_rg" id="var_rg" value="<?php echo($strRG); ?>" type="text" size="30" maxlength="120"   title="RG"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_cnh">
											<strong>CNH:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_cnh" id="var_cnh" value="<?php echo($strCNH); ?>" type="text" size="30" maxlength="120"   title="CNH"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_pis">
											<strong>PIS:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_pis" id="var_pis" value="<?php echo($strPIS); ?>" type="text" size="30" maxlength="120"   title="PIS"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_ctps">
											<strong>CTPS:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_ctps" id="var_ctps" value="<?php echo($strCTPS); ?>" type="text" size="30" maxlength="120"   title="CTPS"><span class="comment_med">&nbsp;</span></td>
</tr>
								<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>ENDEREÇO PRINCIPAL</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_num_endprin_cep">
											<strong>*Cep:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_cep" id="var_endprin_cep" value="<?php echo($strEndPrinCEP); ?>" type="text" size="15" maxlength="8"  onkeypress="Javascript:return validateNumKey(event);"  title="Cep"><span class="comment_med">&nbsp;(somente números)</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_logradouro">
											<strong>*Logradouro:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_logradouro" id="var_endprin_logradouro" value="<?php echo($strEndPrinLogradouro); ?>" type="text" size="50" maxlength="255"   title="Logradouro"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_numero">
											<strong>*Num. / Compl.:</strong>										</label>
									</td>
									<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
											<tr>	
												<td nowrap align="left"><input name="var_endprin_numero" id="var_endprin_numero" value="<?php echo($strEndPrinNumero); ?>" type="text" size="5" maxlength="20" title="Num. / Compl."><span class="comment_med">&nbsp;</span></td>
  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_complemento">
											<strong></strong>										</label>									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_complemento" id="var_endprin_complemento" value="<?php echo($strEndPrinComplemento); ?>" type="text" size="12" maxlength="50"   title="Complemento"><span class="comment_med">&nbsp;</span></td>
</tr>
										</table>									</td>
								</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_bairro">
											<strong>*Bairro:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_bairro" id="var_endprin_bairro" value="<?php echo($strEndPrinBairro); ?>" type="text" size="20" maxlength="30"   title="Bairro"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_cidade">
											<strong>*Cidade:</strong>										</label>
									</td>
									<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
											<tr>	
												<td nowrap align="left"><input name="var_endprin_cidade" id="var_endprin_cidade" value="<?php echo($strEndPrinCidade); ?>" type="text" size="20" maxlength="30" title="Cidade"><span class="comment_med">&nbsp;</span></td>
  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_estado">
											<strong>*Estado:</strong>										</label>									</td>
									<td nowrap align="left" width="99%" >
									<select name="var_endprin_estado" id="var_endprin_estado"  style="width:45px" size="1" title="Estado">
									<option value='' <?php if ($strEndPrinEstado == "") echo("selected"); ?>></option>
									<option value='AC' <?php if ($strEndPrinEstado == "AC") echo("selected"); ?>>AC</option>
									<option value='AL' <?php if ($strEndPrinEstado == "AL") echo("selected"); ?>>AL</option>
									<option value='AM' <?php if ($strEndPrinEstado == "AM") echo("selected"); ?>>AM</option>
									<option value='AP' <?php if ($strEndPrinEstado == "AP") echo("selected"); ?>>AP</option>
									<option value='BA' <?php if ($strEndPrinEstado == "BA") echo("selected"); ?>>BA</option>
									<option value='CE' <?php if ($strEndPrinEstado == "CE") echo("selected"); ?>>CE</option>
									<option value='DF' <?php if ($strEndPrinEstado == "DF") echo("selected"); ?>>DF</option>
									<option value='ES' <?php if ($strEndPrinEstado == "ES") echo("selected"); ?>>ES</option>
									<option value='GO' <?php if ($strEndPrinEstado == "GO") echo("selected"); ?>>GO</option>
									<option value='MA' <?php if ($strEndPrinEstado == "MA") echo("selected"); ?>>MA</option>
									<option value='MG' <?php if ($strEndPrinEstado == "MG") echo("selected"); ?>>MG</option>
									<option value='MS' <?php if ($strEndPrinEstado == "MS") echo("selected"); ?>>MS</option>
									<option value='MT' <?php if ($strEndPrinEstado == "MT") echo("selected"); ?>>MT</option>
									<option value='PA' <?php if ($strEndPrinEstado == "PA") echo("selected"); ?>>PA</option>
									<option value='PB' <?php if ($strEndPrinEstado == "PB") echo("selected"); ?>>PB</option>
									<option value='PE' <?php if ($strEndPrinEstado == "PE") echo("selected"); ?>>PE</option>
									<option value='PI' <?php if ($strEndPrinEstado == "PI") echo("selected"); ?>>PI</option>
									<option value='PR' <?php if ($strEndPrinEstado == "PR") echo("selected"); ?>>PR</option>
									<option value='RJ' <?php if ($strEndPrinEstado == "RJ") echo("selected"); ?>>RJ</option>
									<option value='RN' <?php if ($strEndPrinEstado == "RN") echo("selected"); ?>>RN</option>
									<option value='RO' <?php if ($strEndPrinEstado == "RO") echo("selected"); ?>>RO</option>
									<option value='RR' <?php if ($strEndPrinEstado == "RR") echo("selected"); ?>>RR</option>
									<option value='RS' <?php if ($strEndPrinEstado == "RS") echo("selected"); ?>>RS</option>
									<option value='SC' <?php if ($strEndPrinEstado == "SC") echo("selected"); ?>>SC</option>
									<option value='SE' <?php if ($strEndPrinEstado == "SE") echo("selected"); ?>>SE</option>
									<option value='SP' <?php if ($strEndPrinEstado == "SP") echo("selected"); ?>>SP</option>
									<option value='TO' <?php if ($strEndPrinEstado == "TO") echo("selected"); ?>>TO</option>
									</select><span class="comment_med">&nbsp;</span>
									</td>
								</tr>
										</table>									</td>
								</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_pais">
											<strong>*País:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_pais" id="var_endprin_pais" value="<?php echo($strEndPrinPais); ?>" type="text" size="20" maxlength="30" title="País"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_fone1">
											<strong>*Telefone 1:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_fone1" id="var_endprin_fone1" value="<?php echo($strEndPrinFone1); ?>" type="text" size="27" maxlength="27"   title="Telefone 1"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_fone2">
											<strong>*Telefone 2:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_fone2" id="var_endprin_fone2" value="<?php echo($strEndPrinFone2); ?>" type="text" size="27" maxlength="27"   title="Telefone 2"><span class="comment_med">&nbsp;</span></td>
</tr>
			<!--
			<tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_fone3">
											<strong>Telefone 3:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_fone3" id="var_endprin_fone3" value="<?php echo($strEndPrinFone3); ?>" type="text" size="27" maxlength="27"   title="Telefone 3"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_fone4">
											<strong>Telefone 4:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_fone4" id="var_endprin_fone4" value="<?php echo($strEndPrinFone4); ?>" type="text" size="27" maxlength="27"   title="Telefone 4"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_fone5">
											<strong>Telefone 5:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_fone5" id="var_endprin_fone5" value="<?php echo($strEndPrinFone5); ?>" type="text" size="27" maxlength="27"   title="Telefone 5"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endprin_fone6">
											<strong>Telefone 6:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endprin_fone6" id="var_endprin_fone6" value="<?php echo($strEndPrinFone6); ?>" type="text" size="27" maxlength="27"   title="Telefone 6"><span class="comment_med">&nbsp;</span></td>
</tr>
-->
								<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>ENDEREÇO SECUNDÁRIO</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_num_endcom_cep">
											<strong>Cep:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_cep" id="var_endcom_cep" value="<?php echo($strEndComCEP); ?>" type="text" size="15" maxlength="8"  onkeypress="Javascript:return validateNumKey(event);"  title="Cep"><span class="comment_med">&nbsp;(somente números)</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_logradouro">
											<strong>Logradouro:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_logradouro" id="var_endcom_logradouro" value="<?php echo($strEndComLogradouro); ?>" type="text" size="50" maxlength="255" title="Logradouro"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_numero">
											<strong>Num. / Compl.:</strong>										</label>
									</td>
									<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
											<tr>	
												<td nowrap align="left"><input name="var_endcom_numero" id="var_endcom_numero" value="<?php echo($strEndComNumero); ?>" type="text" size="5" maxlength="20" title="Num. / Compl."><span class="comment_med">&nbsp;</span></td>
  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_complemento">
											<strong></strong>										</label>									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_complemento" id="var_endcom_complemento" value="<?php echo($strEndComComplemento); ?>" type="text" size="12" maxlength="50"   title="Complemento"><span class="comment_med">&nbsp;</span></td>
</tr>
										</table>									</td>
								</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_bairro">
											<strong>Bairro:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_bairro" id="var_endcom_bairro" value="<?php echo($strEndComBairro); ?>" type="text" size="20" maxlength="30"   title="Bairro"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_cidade">
											<strong>Cidade:</strong>										</label>
									</td>
									<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
											<tr>	
												<td nowrap align="left"><input name="var_endcom_cidade" id="var_endcom_cidade" value="<?php echo($strEndComCidade); ?>" type="text" size="20" maxlength="30" title="Cidade"><span class="comment_med">&nbsp;</span></td>
  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_estado">
											<strong>Estado:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
									<select name="var_endcom_estado" id="var_endcom_estado" style="width:45px" size="1" title="Estado">
									<option value='' <?php if ($strEndComEstado == "") echo("selected"); ?>></option>
									<option value='AC' <?php if ($strEndComEstado == "AC") echo("selected"); ?>>AC</option>
									<option value='AL' <?php if ($strEndComEstado == "AL") echo("selected"); ?>>AL</option>
									<option value='AM' <?php if ($strEndComEstado == "AM") echo("selected"); ?>>AM</option>
									<option value='AP' <?php if ($strEndComEstado == "AP") echo("selected"); ?>>AP</option>
									<option value='BA' <?php if ($strEndComEstado == "BA") echo("selected"); ?>>BA</option>
									<option value='CE' <?php if ($strEndComEstado == "CE") echo("selected"); ?>>CE</option>
									<option value='DF' <?php if ($strEndComEstado == "DF") echo("selected"); ?>>DF</option>
									<option value='ES' <?php if ($strEndComEstado == "ES") echo("selected"); ?>>ES</option>
									<option value='GO' <?php if ($strEndComEstado == "GO") echo("selected"); ?>>GO</option>
									<option value='MA' <?php if ($strEndComEstado == "MA") echo("selected"); ?>>MA</option>
									<option value='MG' <?php if ($strEndComEstado == "MG") echo("selected"); ?>>MG</option>
									<option value='MS' <?php if ($strEndComEstado == "MS") echo("selected"); ?>>MS</option>
									<option value='MT' <?php if ($strEndComEstado == "MT") echo("selected"); ?>>MT</option>
									<option value='PA' <?php if ($strEndComEstado == "PA") echo("selected"); ?>>PA</option>
									<option value='PB' <?php if ($strEndComEstado == "PB") echo("selected"); ?>>PB</option>
									<option value='PE' <?php if ($strEndComEstado == "PE") echo("selected"); ?>>PE</option>
									<option value='PI' <?php if ($strEndComEstado == "PI") echo("selected"); ?>>PI</option>
									<option value='PR' <?php if ($strEndComEstado == "PR") echo("selected"); ?>>PR</option>
									<option value='RJ' <?php if ($strEndComEstado == "RJ") echo("selected"); ?>>RJ</option>
									<option value='RN' <?php if ($strEndComEstado == "RN") echo("selected"); ?>>RN</option>
									<option value='RO' <?php if ($strEndComEstado == "RO") echo("selected"); ?>>RO</option>
									<option value='RR' <?php if ($strEndComEstado == "RR") echo("selected"); ?>>RR</option>
									<option value='RS' <?php if ($strEndComEstado == "RS") echo("selected"); ?>>RS</option>
									<option value='SC' <?php if ($strEndComEstado == "SC") echo("selected"); ?>>SC</option>
									<option value='SE' <?php if ($strEndComEstado == "SE") echo("selected"); ?>>SE</option>
									<option value='SP' <?php if ($strEndComEstado == "SP") echo("selected"); ?>>SP</option>
									<option value='TO' <?php if ($strEndComEstado == "TO") echo("selected"); ?>>TO</option>
									</select><span class="comment_med">&nbsp;</span>
									</td>
								</tr>
										</table>									</td>
								</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_pais">
											<strong>País:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_pais" id="var_endcom_pais" value="<?php echo($strEndComPais); ?>" type="text" size="20" maxlength="30"   title="País"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_fone1">
											<strong>Telefone 1:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_fone1" id="var_endcom_fone1" value="<?php echo($strEndComFone1); ?>" type="text" size="27" maxlength="27"   title="Telefone 1"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_fone2">
											<strong>Telefone 2:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_fone2" id="var_endcom_fone2" value="<?php echo($strEndComFone2); ?>" type="text" size="27" maxlength="27"   title="Telefone 2"><span class="comment_med">&nbsp;</span></td>
</tr>
<!--
<tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_fone3">
											<strong>Telefone 3:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_fone3" id="var_endcom_fone3" value="<?php echo($strEndComFone3); ?>" type="text" size="27" maxlength="27"   title="Telefone 3"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_fone4">
											<strong>Telefone 4:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_fone4" id="var_endcom_fone4" value="<?php echo($strEndComFone4); ?>" type="text" size="27" maxlength="27"   title="Telefone 4"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FFFFFF">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_fone5">
											<strong>Telefone 5:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_fone5" id="var_endcom_fone5" value="<?php echo($strEndComFone5); ?>" type="text" size="27" maxlength="27"   title="Telefone 5"><span class="comment_med">&nbsp;</span></td>
</tr><tr bgcolor="#FAFAFA">  <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_endcom_fone6">
											<strong>Telefone 6:</strong>										</label>
									</td>
									<td nowrap align="left" width="99%" >
								<input name="var_endcom_fone6" id="var_endcom_fone6" value="<?php echo($strEndComFone6); ?>" type="text" size="27" maxlength="27"   title="Telefone 6"><span class="comment_med">&nbsp;</span></td>
</tr>
-->

								<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
							<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>		
							<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
						</table>
					</td>
				</tr>
				<tr>
					<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
						<button onClick="document.formeditor.submit();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
						<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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
<?php
$objConn = NULL;
?>
