<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

//Recebe apenas o código da PJ pai...
$intCodPJ = request("var_chavereg");

$objConn = abreDBConn(CFG_DB);
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
	/*if (document.formeditor.var_cod_pj.value == '') var_msg += "\nEmpresa";
	if (document.formeditor.var_nome.value == '') var_msg += "\nNome";
	if (document.formeditor.var_cpf.value == '') var_msg += "\nCPF";
	if (document.formeditor.var_endprin_cep.value == '') var_msg += "\nCEP";
	if (document.formeditor.var_endprin_logradouro.value == '') var_msg += "\nLogradouro";
	if (document.formeditor.var_endprin_numero.value == '') var_msg += "\nNúmero";
	if (document.formeditor.var_endprin_bairro.value == '') var_msg += "\nBairro";
	if (document.formeditor.var_endprin_cidade.value == '') var_msg += "\nCidade";
	if (document.formeditor.var_endprin_estado.value == '') var_msg += "\nEstado";
	if (document.formeditor.var_endprin_pais.value == '') var_msg += "\nPaís";
	if (document.formeditor.var_endprin_fone1.value == '') var_msg += "\nFone 1";*/
	
    if (validateRequestedFields("formeditor") == true) {   
	
	//if (var_msg == ''){
		if(strLocation != ""){ document.getElementById('var_redirect').value = strLocation; }
		document.formeditor.submit();
	}
	else {
		//alert("Informar campos abaixo:\n" + var_msg);
        return false;
	}
}

function cancelar(){
	window.location= "STviewpfsSinog.php?var_chavereg=<?php echo($intCodPJ);?>";
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


</script>
</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="700" height="100%" align="center">
 <tr>
  <td align="center" valign="top">
	<?php athBeginFloatingBox("630","none","COLABORADOR (inserção livre/contato)",CL_CORBAR_GLASS_1); ?>
		<table width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
	   		<form name="formeditor" action="STinsFreePFexecSinog.php" method="post" id="formeditor">
				<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ); ?>">
				<input type="hidden" name="var_endprin_pais" value="BRASIL">
				<input type="hidden" name="var_redirect" id="var_redirect" value="../modulo_CadPJ/STviewpfsSinog.php?var_chavereg=<?php echo($intCodPJ);?>" />
		<tr> 
			<td align="center" valign="top">

				<table style="border:0px; width:550px;" cellspacing="0" cellpadding="4"> 
                    <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>

                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_nome"><strong>*Nome:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_nome" id="var_nomeô" value="" type="text" size="50" maxlength="100" title="nome"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    
                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_apelido"><strong>Nome Credencial:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_apelido" id="var_apelido" value="" type="text" size="30" maxlength="20"   title="Nome Credencial">&nbsp;(apelido)<br /><span class="comment_med">&nbsp;(Este é o nome que aparecerá na credencial, abrevie caso seja necessário)</span></td>
                    </tr>

                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_tratamento"><strong>Tratamento:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_tratamento" id="var_tratamento" value="" type="text" size="27" maxlength="27"   title="tratamento"><span class="comment_med">&nbsp;</span></td>
                    </tr>

                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cpf"><strong>CPF:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_cpf" id="var_cpf" value="" onKeyPress="javascript:return validateNumKey(event);" type="text" size="50" maxlength="11" title="nome"><span class="comment_med">&nbsp;</span></td>
                    </tr>

                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_rg"><strong>RG:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_rg" id="var_rg" value=""  onKeyPress="javascript:return validateNumKey(event);" type="text" size="50" maxlength="30" title="nome"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_email"><strong>e-mail:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_email" id="var_email" value="" type="text" size="60" maxlength="255"   title="e-mail"><span class="comment_med">&nbsp;</span></td>
                    </tr>

                    <!--
                        <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="dbvar_date_data_nasc"><strong>Nascimento:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_data_nasc" id="var_data_nasc" value="" type="text" size="10" maxlength="10" readonly="true" title="data nascimento">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_data_nasc);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a><span class="comment_med">&nbsp;</span></td>
                    </tr>

                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_sexo"><strong>Sexo:</strong></label></td>
                        <td nowrap align="left" width="99%" >
                            <select name="var_sexo" id="var_sexo" style="width:120px" size="1" title="Sexo">
                                <option value="m">MASCULINO</option>
                                <option value="f">FEMINININO</option>
                            </select><span class="comment_med">&nbsp;</span>
                        </td>
                    </tr>
                    

                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_foto"><strong>Foto:</strong></label></td>
                        <td nowrap align="left" width="99%" >
                                <input type="text" name="var_foto" id="var_foto" value="" size="50" readonly="true" title="Foto">
                                <input type="button" name="btn_uploader" value="Upload" class="inputclean" onClick="callUploader('formeditor','var_foto','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/fotospf/','','');"><span class="comment_med">&nbsp;</span>
                        </td>
                    </tr>-->

                  
                    
                   <!-- <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_estado_civil"><strong>Estado Civil:</strong></label></td>
                        <td nowrap align="left" width="99%" >
                            <select name="var_estado_civil" id="var_estado_civil" style="width:100px" size="1" title="Estado Civil">
                                <option value="" selected></option>
                                <option value="CASADO"><i>Casado(a)</i></option>
                                <option value="SEPARADO"><i>Separado(a)</i></option>
                                <option value="SOLTEIRO"><i>Solteiro(a)</i></option>
                                <option value="VIUVO"><i>Viúvo(a)</i></option>
                                <option value="DIVORCIADO"><i>Divorciado(a)</i></option>
                                <option value="DESQUITADO"><i>Desquitado(a)</i></option>
                                <option value="AMASIADO"><i>Amasiado(a)</i></option>
                            </select><span class="comment_med">&nbsp;</span>
                        </td>
                    </tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_nacionalidade"><strong>Nacionalidade:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_nacionalidade" id="var_nacionalidade" value="" type="text" size="35" maxlength="250"   title="Nacionalidade"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_naturalidade"><strong>Naturalidade:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_naturalidade" id="var_naturalidade" value="" type="text" size="35" maxlength="250"   title="Naturalidade"><span class="comment_med">&nbsp;</span></td>
                    </tr>-->

                    <?php //if ( (stripos($str,"COD_CARGO")<=-1) || (stripos($str,"COD_CARGO:S")>-1) ) {
						if (1==2) {
					?>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cod_cargo"><strong>Cargo:</strong></label></td>
                        <td nowrap align="left" width="99%">
							<select name="var_cod_cargo" id="var_cod_cargo" class="edtext" style="width:200px;" tabindex="6">
								<option value="" selected></option>
								<?php echo(montaCombo($objConn,"SELECT cod_cargo, nome FROM cad_cargo ORDER BY 2","cod_cargo","nome",$strCargo)); ?> 
							</select>									
                        </td>
                    </tr>
                    <?php } ?>

                    <?php //if ( (stripos($str,"FUNCAO")<=-1) || (stripos($str,"FUNCAO:S")>-1) ) {
					if (1==1) {
					?>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_funcao"><strong>Função:</strong></label></td>
                        <td nowrap align="left" width="99%">
           	                <select name="var_trab_funcao" id="var_trab_funcao_combo" class="edtext" style="display:block; float:left; width:200px;">
								< option value="" selected></option>>
								<?php echo(montaCombo($objConn,"SELECT DISTINCT trim(funcao), funcao FROM relac_pj_pf ORDER BY 1","funcao","funcao","")); ?>
							</select>
							<input type="text" name="var_trab_funcao" id="var_trab_funcao_input" size="60" style="display:none; float:left; width:200px;"  disabled="disabled" />
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
							                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_trab_funcao_combo','var_trab_funcao_input'); return false;"></span>
                        </td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"COD_NIVEL_HIERARQUICO")<=-1) || (stripos($str,"COD_NIVEL_HIERARQUICO:S")>-1) ) {?>
                   <!-- <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cod_nivel"><strong>Nível:</strong></label></td>									
                        <td nowrap>
							<select name="var_cod_nivel" id="var_cod_nivel" class="edtext" style="width:200px;" tabindex="7">
								<option value="" selected></option>
								<?php echo(montaCombo($objConn,"SELECT cod_nivel_hierarquico, nome FROM cad_nivel_hierarquico ORDER BY 2","cod_nivel_hierarquico","nome",$strNivel)); ?>
							</select>(nivel hierarquico)									
                        </td>
                    </tr> -->
                    <?php } ?>

                    <?php //if ( (stripos($str,"TIPO")<=-1) || (stripos($str,"TIPO:S")>-1) ) {
                        if (1==2){?>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_tipo"><strong>Tipo:</strong></label></td>
                        <td nowrap>
           	                <select name="var_trab_tipo" id="var_trab_tipo_combo" class="edtext" style="display:block; float:left; width:120px;">
								<!-- option value="" selected></option //-->
								<?php 
								    $strSQL  = "/* SELECT DISTINCT tipo, tipo as valor FROM relac_pj_pf WHERE tipo NOT IN ('AM - Principal','AM - Outros Contatos','Atendimento Abramge','Jurídico', 'Financeiro', 'AM e AO - Principal','AO - Principal', 'AO - Outros Contatos', 'Comunicação') ";
								    $strSQL .= " UNION  */ SELECT 'AM - Principal' as tipo    , 'AM - Principal' as valor  ";
								    $strSQL .= " UNION   SELECT 'AM - Outros Contatos'      , 'AM - Outros Contatos'     ";
								    $strSQL .= " UNION   SELECT 'Atendimento Abramge'  , 'Atendimento Abramge' ";
								    $strSQL .= " UNION   SELECT 'Jurídico'   , 'Jurídico'  ";
                                    $strSQL .= " UNION   SELECT 'Financeiro'     , 'Financeiro'    ";
                                    $strSQL .= " UNION   SELECT 'AM e AO - Principal'     , 'AM e AO - Principal'    ";
                                    $strSQL .= " UNION   SELECT 'AO - Principal'     , 'AO - Principal'    ";
                                    $strSQL .= " UNION   SELECT 'AO - Outros Contatos'     , 'AO - Outros Contatos'    ";
                                    $strSQL .= " UNION   SELECT 'Comunicação'     , 'Comunicação'    ";
                                    $strSQL .= " UNION SELECT 'Presidência','PRESIDENCIA'";
                                    $strSQL .= " UNION SELECT 'Recursos Humanos','RECURSOS_HUMANOS'";
								    $strSQL .= " ORDER BY 1";
									echo(montaCombo($objConn,$strSQL ,"valor","tipo","")); 
								?>
							</select>
							<input type="text" name="var_trab_tipo" id="var_trab_tipo_input" size="60" style="display:none; float:left; width:200px;" disabled="disabled" />
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
						                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_trab_tipo_combo','var_trab_tipo_input'); return false;"></span>
                        </td>
                    </tr>
                    <?php } ?>


                    <?php //if ( (stripos($str,"DEPARTAMENTO")<=-1) || (stripos($str,"DEPARTAMENTO:S")>-1) ) {
						if (1==1) {
					?>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_departamento"><strong>Departamento:</strong></label></td>									
                        <td nowrap>
           	                <select name="var_trab_departamento" id="var_trab_departamento_combo" class="edtext" style="display:block; float:left; width:200px;">
								<option value="" selected></option>
								<?php echo(montaCombo($objConn,"SELECT DISTINCT trim(departamento), departamento FROM relac_pj_pf ORDER BY 1","departamento","departamento","")); ?>
							</select>
                           
                            <input name="var_trab_departamento" id="var_trab_departamento_input" type="text" size="50" style="display:none; float:left; width:200px;" maxlength="100" title="Departamento" disabled="disabled">
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0"
						                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_trab_departamento_combo','var_trab_departamento_input'); return false;"></span>
                        </td>
                    </tr>
                    <?php } ?>

                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_obs"><strong>Obs:</strong></label></td>
                        <td nowrap align="left" width="99%"><textarea name="var_obs" id="var_obs" cols="60" rows="5"   title="Obs"></textarea><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    

                    <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                    <tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>ENDEREÇO PRINCIPAL</strong></td></tr>
                    <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
                    <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr><tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="dbvar_num_endprin_cep"><strong>CEP:</strong></label></td>
                        <td nowrap align="left" width="99%">
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr><td width="1%">
                                    <input name="var_endprin_cep" id="var_endprin_cep" value="" type="text" size="12" maxlength="8"  onkeypress="Javascript:return validateNumKey(event);" title="CEP"></td>
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
                        <td nowrap align="left" width="99%" ><input name="var_endprin_logradouro" id="var_endprin_logradouro" value="" type="text" size="50" maxlength="255"   title="Logradouro"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_numero"><strong>Num. / Compl.:</strong></label></td>
                        <td nowrap>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr>	
                                    <td nowrap align="left"><input name="var_endprin_numero" id="var_endprin_numero" value="" type="text" size="5" maxlength="20" title="Num. / Compl."><span class="comment_med">&nbsp;</span></td>
                                    <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_complemento"><strong></strong></label></td>
                                    <td nowrap align="left" width="99%" ><input name="var_endprin_complemento" id="var_endprin_complemento" value="" type="text" size="12" maxlength="50"   title="Complemento"><span class="comment_med">&nbsp;</span></td>
                                </tr>
                            </table>									
                        </td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_bairro"><strong>Bairro:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_endprin_bairro" id="var_endprin_bairro" value="" type="text" size="20" maxlength="30"   title="Bairro"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_cidade"><strong>Cidade:</strong></label></td>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr>	
                                    <td nowrap align="left"><input name="var_endprin_cidade" id="var_endprin_cidade" value="" type="text" size="20" maxlength="30" title="Cidade"><span class="comment_med">&nbsp;</span></td>
                                    <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_estado"><strong>Estado:</strong></label></td>
                                    <td nowrap align="left" width="99%" >
                                            <select name="var_endprin_estado" id="var_endprin_estado"  style="width:45px" size="1" title="Estado">
                                            <option value=''></option>
                                            <option value='AC'>AC</option>
                                            <option value='AL'>AL</option>
                                            <option value='AM' >AM</option>
                                            <option value='AP' >AP</option>
                                            <option value='BA' >BA</option>
                                            <option value='CE' >CE</option>
                                            <option value='DF' >DF</option>
                                            <option value='ES' >ES</option>
                                            <option value='GO' >GO</option>
                                            <option value='MA' >MA</option>
                                            <option value='MG' >MG</option>
                                            <option value='MS' >MS</option>
                                            <option value='MT' >MT</option>
                                            <option value='PA' >PA</option>
                                            <option value='PB' >PB</option>
                                            <option value='PE' >PE</option>
                                            <option value='PI' >PI</option>
                                            <option value='PR' >PR</option>
                                            <option value='RJ' >RJ</option>
                                            <option value='RN' >RN</option>
                                            <option value='RO' >RO</option>
                                            <option value='RR' >RR</option>
                                            <option value='RS' >RS</option>
                                            <option value='SC' >SC</option>
                                            <option value='SE' >SE</option>
                                            <option value='SP' >SP</option>
                                            <option value='TO' >TO</option>
                                            </select><span class="comment_med">&nbsp;</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr bgcolor="#FFFFFF"> 
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_fone1"><strong>*Telefone 1:</strong></label></td>
                        <td nowrap align="left" width="99%" ><input name="var_endprin_fone1" id="var_endprin_fone1ô" value="" type="text" size="27" maxlength="27"   title="Telefone 1"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_fone2"><strong>Telefone 2:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_endprin_fone2" id="var_endprin_fone2" value="" type="text" size="27" maxlength="27"   title="Telefone 2"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_fone3"><strong>Telefone 3:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_endprin_fone3" id="var_endprin_fone3" value="" type="text" size="27" maxlength="27"   title="Telefone 2"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_fone4"><strong>Telefone 4:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_endprin_fone4" id="var_endprin_fone4" value="" type="text" size="27" maxlength="27"   title="Telefone 2"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_endprin_fone5"><strong>Telefone 5:</strong></label></td>
                        <td nowrap align="left" width="99%"><input name="var_endprin_fone5" id="var_endprin_fone5" value="" type="text" size="27" maxlength="27"   title="Telefone 2"><span class="comment_med">&nbsp;</span></td>
                    </tr>
                   

                   <!--<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                    <tr>
                        <td></td>
                        <td align="left" valign="top" class="destaque_gde"><strong>DADOS DA VAGA</strong>&nbsp;(relação PJ x PF)</td>
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
						
								<?php 
								    $strSQL  = "SELECT DISTINCT categoria, categoria FROM relac_pj_pf WHERE categoria NOT IN ('GERAL','ESPECIAL','PLENO') ";
								    $strSQL .= " UNION   SELECT 'GERAL'    , 'GERAL'  ";
								    $strSQL .= " UNION   SELECT 'ESPECIAL' , 'ESPECIAL'  ";
								    $strSQL .= " UNION   SELECT 'PLENO'    , 'PLENO'  ";
								    $strSQL .= " ORDER BY 1";
									echo(montaCombo($objConn,$strSQL ,"categoria","categoria","")); 
								?>
							</select>
							<input type="text" name="var_categoria" id="var_categoria_input" size="60" style="display:none; float:left; width:200px;" disabled="disabled" />
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
                                                             alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_categoria_combo','var_categoria_input'); return false;"></span>
                        </td>
                    </tr> -->
                    <?php } ?>

                   

                   
                    <?php //if ( (stripos($str,"CLASSIFICACAO_VIP")<=-1) || (stripos($str,"CLASSIFICACAO_VIP:S")>-1) ) {
                        if (1 == 2) {
                    ?>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cod_nivel"><strong>Classificação:</strong></label></td>									
                        <td nowrap>
           	                <select name="var_classificacao_vip" id="var_classificacao_vip_combo" class="edtext" style="display:block; float:left; width:120px;">
								<!--<option value="" selected></option //-->
								<?php 
								    $strSQL  = "SELECT DISTINCT classificacao_vip, classificacao_vip FROM relac_pj_pf WHERE classificacao_vip is NOT NULL";
								    $strSQL .= " ORDER BY 1";
									echo(montaCombo($objConn,$strSQL ,"classificacao_vip","classificacao_vip","")); 
								?>
							</select>
							<input type="text" name="var_classificacao_vip" id="var_classificacao_vip_inpuit" size="60" style="display:none; float:left; width:200px;" disabled="disabled" />
	                        <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
						                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_classificacao_vip_combo','var_classificacao_vip_inpuit'); return false;"></span>

                        </td>
                    </tr>
                    <?php } ?>
                    <!--<tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_obs"><strong>Obs / <br />Representado por:</strong></label></td>
                        <td nowrap align="left" width="99%"><textarea name="var_trab_obs" cols="60" rows="2" title="Obs"></textarea><span class="comment_med"><br />
                        Campo livre para preenchimento de livre observações como<br />
                        "autorizado pelo Rodrigo","representa a Proevento",<br />
                        "através da Ubrafe","Presidente do Sindiprom",...</span></td>
                    </tr>

                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_dt_admissao"><strong>Admissão:</strong></label></td>
                        <td nowrap><input name="var_trab_dt_admissao" id="var_trab_dt_admissao" value="" type="text" size="10" maxlength="10" title="Data Admissão" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_trab_dt_admissao);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap><strong>Data Demissão:</strong></td>
                        <td nowrap><input name="var_trab_dt_demissao" id="var_trab_dt_demissao" value="" type="text" size="10" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" title="data de demissão">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_trab_dt_demissao);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a>
                        <br /><span class="comment_med">Preenchendo este campo, o colaborador que está sendo inserido não <br />aparecerá na listagem de colaboradores, do painel da Afiliada, somente <br />na listagem completa de colaboradores.</span></td>
                    </tr>
                    <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                        
                    <tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>
                    <tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>	-->
                 																			
				</table>

			</td>
		</tr>
		<tr>
			<td align="right" colspan="3" style="padding:10px 30px 10px 10px;">
				<button onClick="verifica('');return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
				<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
				<button onClick="verifica('../modulo_CadPJ/STupdcolabSinog.php?var_chavereg=<?php echo($intCodPF);?>&var_cod_pj=<?php echo($intCodPJ);?>&var_cpf=<?php echo($strCPF);?>');return false;"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
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
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
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
