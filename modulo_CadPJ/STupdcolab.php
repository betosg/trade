<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intCodPJ 	 = request("var_cod_pj");
$intCodPF 	 = request("var_chavereg");
$strCPF  	 = request("var_cpf");
$strRedirect = request("var_redirect");

$objConn = abreDBConn(CFG_DB);

$strRotulo = "Alteração";
try {
	$strSQL = "
	   SELECT t1.nome, t1.data_nasc, t1.sexo
			, t1.email, t1.email_extra, t1.website, t1.foto, t1.apelido
			, t1.nome_pai, t1.nome_mae, t1.estado_civil, t1.instrucao
			, t1.nacionalidade, t1.naturalidade, t1.obs
			, t1.rg, t1.cnh, t1.pis, t1.ctps, t1.cpf, t1.titulo_eleitoral, t2.dt_inativo, t2.motivo_inativo
			, t1.endprin_cep, t1.endprin_logradouro, t1.endprin_numero, t1.endprin_complemento, t1.endprin_bairro 
			, t1.endprin_cidade, t1.endprin_estado, t1.endprin_pais, t1.endprin_fone1, t1.endprin_fone2
			, t1.endcom_cep, t1.endcom_logradouro, t1.endcom_numero, t1.endcom_complemento, t1.endcom_bairro
			, t1.endcom_cidade, t1.endcom_estado, t1.endcom_pais, t1.endcom_fone1, t1.endcom_fone2
			, t2.tipo, t2.funcao, t2.categoria, t2.departamento, t2.dt_admissao, t2.dt_demissao, t2.obs as obs_vaga
			, t2.cod_cargo, t2.cod_nivel_hierarquico, t2.classificacao_vip, t2.cod_pj_pf
		FROM 
			cad_pf t1, relac_pj_pf t2
		WHERE t1.cod_pf = " . $intCodPF . "
		  AND t2.cod_pf = " . $intCodPF . "
		  AND t2.cod_pj = " . $intCodPJ . "
		  AND t2.cod_pf = t1.cod_pf";
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
		$strTITE = getValue($objRS, "titulo_eleitoral");
		
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
		
		// Relação PJ x PF
		$strCodRelacPjPf	  = getValue($objRS, "cod_pj_pf"); 		
		$strCategoria 		  = getValue($objRS, "categoria");
		$strTipo 			  = getValue($objRS, "tipo");
		$strFuncao 			  = getValue($objRS, "funcao");
		$strDepartamento      = getValue($objRS, "departamento");
		$dtAdmissao			  = getValue($objRS, "dt_admissao");
		$dtDemissao           = getValue($objRS, "dt_demissao");
		$strObsVaga           = getValue($objRS, "obs_vaga");
		$strCargo             = getValue($objRS, "cod_cargo");
		$strNivel			  = getValue($objRS, "cod_nivel_hierarquico");
		$strClVip			  = getValue($objRS, "classificacao_vip");
		$dtInativo			  = getValue($objRS, "dt_inativo");
		$strMotivo  		  = getValue($objRS, "motivo_inativo");
	}
}
catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
$objResult->closeCursor();
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function verifica(prLocation){
	var var_msg = "";
	var strLocation = prLocation;
	if (document.formeditor.var_cod_pj.value == '') var_msg += "\nEmpresa";
	if (document.formeditor.var_nome.value == '') var_msg += "\nNome";
	if (document.formeditor.var_apelido.value == '') var_msg += "\nNome Credencial";
	if (document.formeditor.var_sexo.value == '') var_msg += "\nSexo";
	if (document.formeditor.var_cpf.value == '') var_msg += "\nCPF";
	if (document.formeditor.var_rg.value == '') var_msg += "\nRG";
	if (document.formeditor.var_endprin_cep.value == '') var_msg += "\nCEP";
	if (document.formeditor.var_endprin_logradouro.value == '') var_msg += "\nLogradouro";
	if (document.formeditor.var_endprin_numero.value == '') var_msg += "\nNúmero";
	if (document.formeditor.var_endprin_bairro.value == '') var_msg += "\nBairro";
	if (document.formeditor.var_endprin_cidade.value == '') var_msg += "\nCidade";
	if (document.formeditor.var_endprin_estado.value == '') var_msg += "\nEstado";
	if (document.formeditor.var_endprin_pais.value == '') var_msg += "\nPaís";
	if (document.formeditor.var_endprin_fone1.value == '') var_msg += "\nFone 1";
	
	if(
		(document.getElementById("var_situacao_colab").value == "INATIVO")&&
		((document.getElementById("var_dt_inativo").value == "")||(document.getElementById("var_motivo_inativo").innerHTML == ""))
	  ){ var_msg += "\n\nINATIVAÇÃO DO COLABORADOR";  }
	if((document.getElementById("var_situacao_colab").value == "INATIVO")&&(document.getElementById("var_dt_inativo").value == "")){ var_msg += "\nData Inativação"; }
	if((document.getElementById("var_situacao_colab").value == "INATIVO")&&(document.getElementById("var_motivo_inativo").innerHTML == "")){ var_msg += "\nMotivo Inativação"; }
	
	if (true){
		if(strLocation != ""){ document.getElementById('var_redirect').value = strLocation; }
		document.formeditor.submit();
	}
	else {
		alert("Informar campos abaixo:\n" + var_msg);
	}
}

function cancelar(){
	window.location= "STviewpfs.php?var_chavereg=<?php echo($intCodPJ);?>";
}

function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
	strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
	AbreJanelaPAGE(strLink, "570", "270");
}

function setFormField(formname, fieldname, valor){
	if ((formname != "") && (fieldname != "") && (valor != "")){
    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  	}
}

function copiaCamposEndereco(){
	document.getElementById('var_endcom_cep').value = document.getElementById('var_endprin_cep').value;
	document.getElementById('var_endcom_logradouro').value = document.getElementById('var_endprin_logradouro').value;
	document.getElementById('var_endcom_numero').value = document.getElementById('var_endprin_numero').value;
	document.getElementById('var_endcom_complemento').value = document.getElementById('var_endprin_complemento').value;
	document.getElementById('var_endcom_bairro').value = document.getElementById('var_endprin_bairro').value;
	document.getElementById('var_endcom_cidade').value = document.getElementById('var_endprin_cidade').value;
	document.getElementById('var_endcom_estado').value = document.getElementById('var_endprin_estado').value;
	document.getElementById('var_endcom_fone1').value = document.getElementById('var_endprin_fone1').value;
	document.getElementById('var_endcom_fone2').value = document.getElementById('var_endprin_fone2').value;
}

function setInativo(){
	if(document.getElementById("var_situacao_colab").value == "ATIVO"){ document.getElementById("table_inativo").style.display = "none"; }
	else { document.getElementById("table_inativo").style.display = "block"; }
	resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
}

function swapInputCombo(prCombo,prInput) {
  //alert('aqui' + document.getElementById(prCombo).style.display);
  if (document.getElementById(prCombo).style.display == "none") {
	  document.getElementById(prCombo).style.display = "block";  document.getElementById(prCombo).disabled = 0;
	  document.getElementById(prInput).style.display = "none";   document.getElementById(prInput).disabled = 1;
  } else {
	  document.getElementById(prCombo).style.display = "none"; 	  document.getElementById(prCombo).disabled = 1;
	  document.getElementById(prInput).style.display = "block";	  document.getElementById(prInput).disabled = 0;
  }
}

//-->
</script>
</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="700" height="100%" align="center">
 <tr>
  <td align="center" valign="top">
	<?php athBeginFloatingBox("630","none","COLABORADOR (" . $strRotulo . ")",CL_CORBAR_GLASS_1); ?>
		<table width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
	   		<form name="formeditor" action="STupdcolabexec.php" method="post">
				<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ); ?>">
				<input type="hidden" name="var_cod_pf" value="<?php echo($intCodPF); ?>">
				<input type="hidden" name="var_cpf" value="<?php echo($strCPF); ?>">
				<input type="hidden" name="var_endprin_pais" value="BRASIL">
				<input type="hidden" name="var_endcom_pais" value="BRASIL">
				<input type="hidden" name="var_str_flag" value="no">
				<input type="hidden" name="var_redirect" id="var_redirect" value="../modulo_CadPJ/STviewpfs.php?var_chavereg=<?php echo($intCodPJ);?>" />
		<tr><td height="22" style="padding-left:35px;padding-top:15px;"><b>Preencha corretamente os campos abaixo:</b></td></tr>
		<tr> 
			<td align="center" valign="top">

				<table style="border:0px; width:550px;" cellspacing="0" cellpadding="4"> 
                    <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                    <tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>DADOS</strong></td></tr>
                    <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
                    <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_nome"><strong>*Nome:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_nome" id="var_nome" value="<?php echo($strNome); ?>" type="text" size="50" maxlength="100" title="nome"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_apelido"><strong>Nome Credencial:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_apelido" id="var_apelido" value="<?php echo($strApelido); ?>" type="text" size="30" maxlength="20"   title="Nome Credencial">&nbsp;(apelido)<br /><span class="comment_med">&nbsp;(Este é o nome que aparecerá na credencial, abrevie caso seja necessário)</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="dbvar_date_data_nasc"><strong>Nascimento:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_data_nasc" id="var_data_nasc" value="<?php echo($dtDataNasc); ?>" type="text" size="10" maxlength="10" readonly="true" title="data nascimento">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_data_nasc);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_sexo"><strong>Sexo:</strong></label></td>
                        <td nowrap align="left" width="99%" >
                            <select name="var_sexo" id="var_sexo"  style="width:120px" size="1" title="Sexo">
                                <option value="m" <?php if ($strSexo == "m") echo("selected"); ?>><i>MASCULINO</i></option>
                                <option value="f" <?php if ($strSexo == "f") echo("selected"); ?>><i>FEMINININO</i></option>
                            </select><span class="comment_med">&nbsp;</span>
                        </td>
                    </tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_email"><strong>e-mail:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_email" id="var_email" value="<?php echo($strEmail); ?>" type="text" size="60" maxlength="255"   title="e-mail"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_foto"><strong>Foto:</strong></label></td>
                        <td nowrap align="left" width="99%" >
                                <input type="text" name="var_foto" id="var_foto" value="<?php echo($strFoto); ?>" size="50" readonly="true" title="Foto">
                                <input type="button" name="btn_uploader" value="Upload" class="inputclean" onClick="callUploader('formeditor','var_foto','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/fotospf/','','');"><span class="comment_med">&nbsp;</span>
                        </td>
                    </tr>
                    <?php if($strFoto != ""){?>
                    <tr>
                        <td >&nbsp;</td>
                        <td align="left"><img src="../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"))?>/upload/fotospf/<?php echo($strFoto);?>" width="160"></td>
                    </tr>
                    <?php }?>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_estado_civil"><strong>Estado Civil:</strong></label></td>
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
                    </tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_nacionalidade"><strong>Nacionalidade:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_nacionalidade" id="var_nacionalidade" value="<?php echo($strNacionalidade); ?>" type="text" size="35" maxlength="250"   title="Nacionalidade"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_naturalidade"><strong>Naturalidade:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_naturalidade" id="var_naturalidade" value="<?php echo($strNaturalidade); ?>" type="text" size="35" maxlength="250"   title="Naturalidade"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_obs"><strong>Obs:</strong></label></td>
                        <td nowrap align="left" width="99%"><textarea name="var_obs" id="var_obs" cols="60" rows="5"   title="Obs"><?php echo($strObs); ?></textarea><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                    <tr>
                        <td></td>
                        <td align="left" valign="top" class="destaque_gde"><strong>FILIAÇÃO</strong></td>
                    </tr>
                    <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
                    <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_nome_pai"><strong>Nome Pai:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_nome_pai" id="var_nome_pai" value="<?php echo($strNomePai); ?>" type="text" size="50" maxlength="250" title="Nome Pai"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_nome_mae"><strong>Nome Mãe:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_nome_mae" id="var_nome_mae" value="<?php echo($strNomeMae); ?>" type="text" size="50" maxlength="250" title="Nome Mãe"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                    <tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>DOCUMENTOS</strong></td></tr>
                    <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
                    <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cpf"><strong>*CPF:</strong></label></td>
                        <td nowrap align="left" width="99%" ><?php echo($strCPF); ?><span class="comment_med">&nbsp;</span></td>
                    </tr><tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap><strong>RG:</strong></td>
                        <td nowrap align="left" width="99%"><input name="var_rg" id="var_rg" value="<?php echo($strRG); ?>" type="text" size="20" maxlength="10" title="RG"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap><strong>PIS:</strong></td>
                        <td nowrap align="left" width="99%"><input name="var_pis" id="var_pis" value="<?php echo($strPIS); ?>" type="text" size="20" maxlength="11" title="PIS"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap><strong>CTPS / Série:</strong></td>
                        <td nowrap align="left" width="99%"><input name="var_ctps" id="var_ctps" value="<?php echo($strCTPS); ?>" type="text" onKeyPress="formatar(this,'#######/#####');return validateNumKey(event);" size="20" maxlength="13" title="CTPS"><span class="comment_med">&nbsp;(Formato XXXXXXX/ZZZZZ)</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap><strong>Título Eleitoral:</strong></td>
                        <td nowrap align="left" width="99%"><input name="var_titulo_eleitoral" id="var_titulo_eleitoral" value="<?php echo($strTITE); ?>" type="text" size="20" maxlength="30" title="TITULO ELEITORAL"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                    <tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>ENDEREÇO PRINCIPAL</strong></td></tr>
                    <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
                    <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr><tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="dbvar_num_endprin_cep"><strong>CEP:</strong></label></td>
                        <td nowrap align="left" width="99%">
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr><td width="1%">
                                    <input name="var_endprin_cep" id="var_endprin_cep" value="<?php echo($strEndPrinCEP); ?>" type="text" size="12" maxlength="8"  onkeypress="Javascript:return validateNumKey(event);" title="CEP"></td>
                                    <td width="99%">
                                        <div style="padding-left:5px;">
                                            <img src="../img/icon_zoom_disabled.gif" alt="Buscar Cep" onClick="Javascript:ajaxBuscaCEP('var_endprin_cep','var_endprin_logradouro','var_endprin_bairro','var_endprin_cidade','var_endprin_estado','var_endprin_numero','loader_cep');" style="cursor:pointer" />
                                            &nbsp;<span id="loader_cep"></span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
						<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_logradouro"><strong>Logradouro:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_endprin_logradouro" id="var_endprin_logradouro" value="<?php echo($strEndPrinLogradouro); ?>" type="text" size="50" maxlength="255"   title="Logradouro"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_numero"><strong>Num. / Compl.:</strong></label></td>
                        <td nowrap>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr>	
                                    <td nowrap align="left"><input name="var_endprin_numero" id="var_endprin_numero" value="<?php echo($strEndPrinNumero); ?>" type="text" size="5" maxlength="20" title="Num. / Compl."><span class="comment_med">&nbsp;</span></td>
                                    <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_complemento"><strong></strong></label></td>
                                    <td nowrap align="left" width="99%" ><input name="var_endprin_complemento" id="var_endprin_complemento" value="<?php echo($strEndPrinComplemento); ?>" type="text" size="12" maxlength="50"   title="Complemento"><span class="comment_med">&nbsp;</span></td>
                                </tr>
                            </table>									
                        </td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_bairro"><strong>Bairro:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_endprin_bairro" id="var_endprin_bairro" value="<?php echo($strEndPrinBairro); ?>" type="text" size="20" maxlength="30"   title="Bairro"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_cidade"><strong>Cidade:</strong></label></td>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr>	
                                    <td nowrap align="left"><input name="var_endprin_cidade" id="var_endprin_cidade" value="<?php echo($strEndPrinCidade); ?>" type="text" size="20" maxlength="30" title="Cidade"><span class="comment_med">&nbsp;</span></td>
                                    <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_estado"><strong>*Estado:</strong></label></td>
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
                            </table>
                        </td>
                    </tr>
                    <tr bgcolor="#FFFFFF"> 
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_fone1"><strong>Telefone 1:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_endprin_fone1" id="var_endprin_fone1" value="<?php echo($strEndPrinFone1); ?>" type="text" size="27" maxlength="27"   title="Telefone 1"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_fone2"><strong>Telefone 2:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_endprin_fone2" id="var_endprin_fone2" value="<?php echo($strEndPrinFone2); ?>" type="text" size="27" maxlength="27"   title="Telefone 2"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                    <tr>
                        <td></td>
                        <td align="left" valign="top" class="destaque_gde"><strong>DADOS DA VAGA</strong>&nbsp;(relação PJ x PF: <?php echo($strCodRelacPjPf) ?>)</td>
                        <?php
							/* Nestes campos da Relação PJ x PF existe a possibilidade de ocultação dos mesms atraves da configuração
							Os campos tens suas funcionalidades mantidas, mas nesse processo de cadastramento FAST(ou livre), damos a 
							possibilidade do cliente ocultar alguns destes campos da relação PJxPJ  tanto no INS quando na UPD.
							   Até a data atual este teste esta [STInsFreePF.asp] e na [STupdcolab.php] do modulo_CadPJ
							Os campso a exibir ou não devem estar no registro de configuração da empresa (sys_VarEntidade) e devem 
							estar na ordem:
 							   CATEGORIA:S,FUNCAO:S,DEPARTAMENTO:S,COD_CARGO:S,COD_NIVEL_HIERARQUICO:S,TIPO:S,CLASSIFICACAO_VIP:S
							*/
							
							$str = strtoupper(getVarEntidade($objConn,"campos_livrerelpjxpf"));

							/*
							Código feito com ARR gerava problema quando a variável fosse mal preenchida, 
							então troquei para um tratamento masi simples de strings -> if ( (stripos($str,"CATEGORIA")<=-1) || (stripos($str,"CATEGORIA:S")>-1) ...
							
							define("CONST_CATEGORIA",0);
							define("CONST_FUNCAO",1);
							define("CONST_DEPARTAMENTO",2);
							define("CONST_COD_CARGO",3);
							define("CONST_COD_NIVEL_HIERARQUICO",4);
							define("CONST_TIPO",5);
							define("CONST_CLASSIFICACAO_VIP",6);
						
							if ($str == "") { $str = "CATEGORIA:S,FUNCAO:S,DEPARTAMENTO:S,COD_CARGO:S,COD_NIVEL_HIERARQUICO:S,TIPO:S,CLASSIFICACAO_VIP:S"; } 
							$sep = Array(",",":");
							$mat = multiexplode($sep,$str);
							if ($mat[CONST_CATEGORIA][1]=='S') ...
							if ($mat[CONST_FUNCAO][1]=='S') ...
							*/
						?>
                    </tr>
                    <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
                    <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>

                    <?php if ( (stripos($str,"CATEGORIA")<=-1) || (stripos($str,"CATEGORIA:S")>-1) ) {?>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_categoria"><strong>Categoria:</strong></label></td>
                        <td nowrap>
           	                <select name="var_categoria" id="var_categoria_combo" class="edtext" style="display:block; float:left; width:120px;">
								<!-- option value="" selected></option //-->
								<?php 
								    $strSQL  = "SELECT DISTINCT categoria, categoria FROM relac_pj_pf WHERE categoria NOT IN ('GERAL','ESPECIAL','PLENO') ";
								    $strSQL .= " UNION   SELECT 'GERAL'    , 'GERAL' FROM relac_pj_pf ";
								    $strSQL .= " UNION   SELECT 'ESPECIAL' , 'ESPECIAL' FROM relac_pj_pf ";
								    $strSQL .= " UNION   SELECT 'PLENO'    , 'PLENO' FROM relac_pj_pf ";
								    $strSQL .= " ORDER BY 1";
									echo(montaCombo($objConn,$strSQL ,"categoria","categoria",$strCategoria)); 
								?>
							</select>
							<input type="text" name="var_categoria" id="var_categoria_input" size="60" style="display:none; float:left; width:200px;" value="<?php echo $strCategoria;?>" disabled="disabled" />
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
                                                             alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_categoria_combo','var_categoria_input'); return fals;"></span>
                        </td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"FUNCAO")<=-1) || (stripos($str,"FUNCAO:S")>-1) ) {?>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_funcao"><strong>Função:</strong></label></td>
                        <td nowrap align="left" width="99%">
           	                <select name="var_trab_funcao" id="var_trab_funcao_combo" class="edtext" style="display:block; float:left; width:200px;">
								<!-- option value="" selected></option //-->
								<?php echo(montaCombo($objConn,"SELECT DISTINCT trim(funcao), funcao FROM relac_pj_pf ORDER BY 1","funcao","funcao",$strFuncao)); ?>
							</select>
							<input type="text" name="var_trab_funcao" id="var_trab_funcao_input" size="60" style="display:none; float:left; width:200px;" value="<?php echo $strFuncao;?>" disabled="disabled" />
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
							                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_trab_funcao_combo','var_trab_funcao_input'); return fals;"></span>
						</td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"DEPARTAMENTO")<=-1) || (stripos($str,"DEPARTAMENTO:S")>-1) ) {?>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_departamento"><strong>Departamento:</strong></label></td>									
                        <td nowrap>
           	                <select name="var_trab_departamento" id="var_trab_departamento_combo" class="edtext" style="display:block; float:left; width:200px;">
								<option value="" selected></option>
								<?php echo(montaCombo($objConn,"SELECT DISTINCT trim(departamento), departamento FROM relac_pj_pf ORDER BY 1","departamento","departamento",$strDepartamento)); ?>
							</select>
                           
                            <input name="var_trab_departamento" id="var_trab_departamento_input" value="<?php echo($strDepartamento); ?>" type="text" size="50" style="display:none; float:left; width:200px;" maxlength="100" title="Departamento" disabled="disabled">
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0"
						                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_trab_departamento_combo','var_trab_departamento_input'); return fals;"></span>
                        </td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"COD_CARGO")<=-1) || (stripos($str,"COD_CARGO:S")>-1) ) {?>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cod_cargo"><strong>Cargo:</strong></label></td>
                        <td nowrap align="left" width="99%">
							<select name="var_cod_cargo" id="var_cod_cargo" class="edtext" style="width:200px;">
								<option value="" selected></option>
								<?php echo(montaCombo($objConn,"SELECT cod_cargo, nome FROM cad_cargo ORDER BY 2","cod_cargo","nome",$strCargo)); ?> 
							</select>&nbsp;(cód. cargo)
                        </td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"COD_NIVEL_HIERARQUICO")<=-1) || (stripos($str,"COD_NIVEL_HIERARQUICO:S")>-1) ) {?>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cod_nivel"><strong>Nível:</strong></label></td>									
                        <td nowrap>
							<select name="var_cod_nivel" id="var_cod_nivel" class="edtext" style="width:200px;">
								<option value="" selected></option>
								<?php echo(montaCombo($objConn,"SELECT cod_nivel_hierarquico, nome FROM cad_nivel_hierarquico ORDER BY 2","cod_nivel_hierarquico","nome",$strNivel)); ?>
							</select>&nbsp;(cód. nivel hierarquico)
                        </td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"TIPO")<=-1) || (stripos($str,"TIPO:S")>-1) ) {?>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_tipo"><strong>Tipo:</strong></label></td>
                        <td nowrap>
           	                <select name="var_trab_tipo" id="var_trab_tipo_combo" class="edtext" style="display:block; float:left; width:120px;">
								<!-- option value="" selected></option //-->
								<?php 
								    $strSQL  = "SELECT DISTINCT tipo, tipo FROM relac_pj_pf WHERE tipo NOT IN ('AUTONOMO','AVULSO','TEMPORARIO','EMPREGADO', 'ESTAGIO') ";
								    $strSQL .= " UNION   SELECT 'AUTONOMO'    , 'AUTONOMO'   FROM relac_pj_pf ";
								    $strSQL .= " UNION   SELECT 'AVULSO'      , 'AVULSO'     FROM relac_pj_pf ";
								    $strSQL .= " UNION   SELECT 'TEMPORARIO'  , 'TEMPORARIO' FROM relac_pj_pf ";
								    $strSQL .= " UNION   SELECT 'EMPREGADO'   , 'EMPREGADO'  FROM relac_pj_pf ";
								    $strSQL .= " UNION   SELECT 'ESTAGIO'     , 'ESTAGIO'    FROM relac_pj_pf ";
								    $strSQL .= " ORDER BY 1";
									echo(montaCombo($objConn,$strSQL ,"tipo","tipo",$strTipo)); 
								?>
							</select>
							<input type="text" name="var_trab_tipo" id="var_trab_tipo_input" size="60" style="display:none; float:left; width:200px;" value="<?php echo $strTipo;?>" disabled="disabled" />
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
						                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_trab_tipo_combo','var_trab_tipo_input'); return false;"></span>
                        </td>
                    </tr>
                    <?php } ?>
                    
                    <?php if ( (stripos($str,"CLASSIFICACAO_VIP")<=-1) || (stripos($str,"CLASSIFICACAO_VIP:S")>-1) ) {?>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cod_nivel"><strong>Classificação:</strong></label></td>									
                        <td nowrap>
           	                <select name="var_classificacao_vip" id="var_classificacao_vip_combo" class="edtext" style="display:block; float:left; width:120px;">
								<!-- option value="" selected></option //-->
								<?php 
								    $strSQL  = "SELECT DISTINCT classificacao_vip, classificacao_vip FROM relac_pj_pf WHERE classificacao_vip is NOT NULL";
								    $strSQL .= " ORDER BY 1";
									echo(montaCombo($objConn,$strSQL ,"classificacao_vip","classificacao_vip",$strClVip)); 
								?>
							</select>
							<input type="text" name="var_classificacao_vip" id="var_classificacao_vip_inpuit" size="60" style="display:none; float:left; width:200px;" value="<?php echo $strClVip;?>" disabled="disabled" />
                        <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
						                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_classificacao_vip_combo','var_classificacao_vip_inpuit'); return false;"></span>
                    </tr>
                    <?php } ?>

                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_dt_admissao"><strong>Admissão:</strong></label></td>
                        <td nowrap><input name="var_trab_dt_admissao" id="var_trab_dt_admissao" value="<?php echo(dDate(CFG_LANG,$dtAdmissao,false));?>" type="text" size="10" maxlength="10" title="Data Admissão" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_trab_dt_admissao);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap><strong>Data Demissão:</strong></td>
                        <td nowrap><input name="var_trab_dt_demissao" id="var_trab_dt_demissao" value="<?php echo(dDate(CFG_LANG,$dtDemissao,false));?>" type="text" size="10" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" title="data de demissão">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_trab_dt_demissao);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a>
                        <br /><span class="comment_med">Preenchendo este campo, o colaborador que está sendo inserido não <br />aparecerá na listagem de colaboradores, do painel da Afiliada, somente <br />na listagem completa de colaboradores.</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_obs"><strong>Obs:</strong></label></td>
                        <td nowrap align="left" width="99%"><textarea name="var_trab_obs" cols="60" rows="5" title="Obs"><?php echo($strObsVaga);?></textarea><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                        
                    <?php if((getsession(CFG_SYSTEM_NAME."_grp_user") == "ADMIN") || (getsession(CFG_SYSTEM_NAME."_grp_user") == "SU")){?>
                    <tr>
                        <td></td>
                        <td align="left" valign="top" class="destaque_gde"><strong>INATIVAR COLABORADOR</strong></td>
                    </tr>
                    <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
                    <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="20%" align="right" valign="top" nowrap><strong>Situação:</strong></td>
                        <td nowrap>
                            <select name="var_situacao_colab" id="var_situacao_colab" style="width:70px;" onChange="setInativo();">
                                <option value="ATIVO">ATIVO</option>
                                <option value="INATIVO" <?php echo(($dtInativo == "") ? "" : "selected='selected'");?>>INATIVO</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table cellpadding="0" cellspacing="4" width="100%" border="0" id="table_inativo" <?php echo(($dtInativo == "") ? "style='display:none;'" : "style='display:block;'" );?>>
                                <tr bgcolor="#FAFAFA">
                                    <td width="1%" align="right" valign="top" nowrap><strong>*Data Inativação:</strong></td>
                                    <td nowrap style="padding-left:5px;"><input name="var_dt_inativo" id="var_dt_inativo" value="<?php echo(dDate(CFG_LANG,$dtInativo,false));?>" type="text" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" size="12" maxlength="10" title="Data de Inativação"><span class="comment_med">&nbsp;</span></td>
                                </tr>
                                <tr bgcolor="#FFFFFF">
                                    <td width="1%" align="right" valign="top" nowrap><strong>*Motivo Inativação:</strong></td>
                                    <td nowrap style="padding-left:5px;">
                                        <textarea name="var_motivo_inativo" id="var_motivo_inativo" rows="5" cols="60"><?php echo($strMotivo);?></textarea>
                                        <br /><span class="comment_med">&nbsp;</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php }?>
                    <tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>
                    <tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
				</table>

			</td>
		</tr>
		<tr>
			<td align="right" colspan="3" style="padding:10px 30px 10px 10px;">
				<button onClick="verifica('');"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
				<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
				<button onClick="verifica('../modulo_CadPJ/STupdcolab.php?var_chavereg=<?php echo($intCodPF);?>&var_cod_pj=<?php echo($intCodPJ);?>&var_cpf=<?php echo($strCPF);?>');"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
			</td>
		</tr>					
			</form>
		</table>
	<?php athEndFloatingBox(); ?>
  </td>
 </tr>
</table>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>
<iframe name="gToday:normal:agenda.js" id="gToday:normal:agenda.js"
        src="../_class/calendar/source/ipopeng.htm" scrolling="no" frameborder="0"
        style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe>
<?php
$objConn = NULL;
?>
