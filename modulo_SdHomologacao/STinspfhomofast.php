<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// Verificação de ACESSO
	// Carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// Verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_FAST");
	
	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	// Calcula a DATA DE VENCIMENTO
	$strTIPO = "homo";
	$intQtdeDiasVctoPadrao = getVarEntidade($objConn,"pedido_qtde_dias_vcto_padrao");
	$intQtdeDiasVctoPadrao = ($intQtdeDiasVctoPadrao == "") ? 0 : $intQtdeDiasVctoPadrao;
	$intQtdeDiasVctoPadrao = (($strTIPO == "homo") || ($strTIPO == "card")) ? "2" : $intQtdeDiasVctoPadrao;
	$dtVcto = dateAdd("d", $intQtdeDiasVctoPadrao, date("Y-m-d"), false);
	if(getWeekDay($dtVcto) == "sabado"){
		$intQtdeDiasVctoPadrao = $intQtdeDiasVctoPadrao + 3;
		$dtVcto = dateAdd("d",$intQtdeDiasVctoPadrao, date("Y-m-d"), false);
	}elseif(getWeekDay($dtVcto) == "domingo"){
		$intQtdeDiasVctoPadrao = $intQtdeDiasVctoPadrao + 2;
		$dtVcto = dateAdd("d",$intQtdeDiasVctoPadrao, date("Y-m-d"), false);
	}
	$dtVcto = dDate(CFG_LANG, $dtVcto, false);
?>
<html>
<head>
<title><?php echo(CFG_SYSTEM_TITLE);?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../_scripts/tablesort.js"></script>
<script type="text/javascript">
var strLocation = null;

function changeDisplay(prIDOne, prIDTwo){
    document.getElementById(prIDOne).style.display = 'none';
    document.getElementById(prIDTwo).style.display = 'block';
}

function ajaxBuscaPF(prCPF){
    // VERIFICA SE PF JÁ EXISTE
    var objAjax;
    var strReturnValue;
    var strDB;
    var strSQL;
    
	// Tratamento BREVE, caso o nome de usuário 
	// esteja vazio ou nulo, retorno nulo
	if(prCPF == null || prCPF == ""){
		return(null);
	}
	// Seta o SQL, cria o AJAX
	strSQL  = "SELECT cpf FROM cad_pf WHERE cpf = '"+ prCPF +"';";
	objAjax = createAjax();
	// Coloca LOADER
	document.getElementById('loader_ajax_cpf').innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' width='13' />";
	objAjax.onreadystatechange = function(){
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
				// alert(strReturnValue);
				// alert(prSQL);
				// imgstatus_fechado.gif
				// icon_wrong.gif
				// verifica se retornou dados
				if(strReturnValue.indexOf('|') != -1){
					document.getElementById('loader_ajax_cpf').innerHTML = "<span style='color:red;'>(CPF <em><b>"+ prCPF +"</b></em>&nbsp; JÁ ESTÁ CADASTRADO)</span>";
					document.getElementById('var_pf_cpf').value = "";
				}
				setTimeout("document.getElementById('loader_ajax_cpf').innerHTML = ''",3000);
			}
			else {
				alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
			}
		}
	}
	objAjax.open("GET", "../_ajax/returndados.php?var_sql=" + strSQL,true); 
	objAjax.send(null); 
}

function validateFormFields(){
	// Verifica se campos obrigatórios estão em branco
	var strErrMSG;
	var auxBool;
	strErrMSG = "";
	// DEBUG
	// alert(getCheckedValue(document.form_insert.var_tipo_pj));
	// Consistência de Dados da Pessoa Jurídica
	if(getCheckedValue(document.form_insert.var_tipo_pj) == "NEW_PJ"){
		strErrMSG += (document.form_insert.var_pj_cnpj.value == "") ? "\nCNPJ da Empresa" : "";
		if(document.form_insert.var_pj_cnpj.value != ""){
			if (!checkCNPJ(document.form_insert.var_pj_cnpj.value,false)) strErrMSG += "\nCNPJ Inválido"; 
		}
		strErrMSG += (document.form_insert.var_pj_razao.value == "") ? "\nRazão Social da Empresa" : "";
		strErrMSG += (document.form_insert.var_pj_cnae_grupo.value == "") ? "\nCNAE da Empresa" : "";
	} else{
		strErrMSG += (document.form_insert.var_pj_codigo.value == "") ? "\nCódigo da PJ" : "";
	}
	// Consistência de Dados do Colaborador [PF]
	if(getCheckedValue(document.form_insert.var_tipo_pf) == ""){
		strErrMSG += "\nOpção PF";
	}
	if(getCheckedValue(document.form_insert.var_tipo_pf) == "NEW_PF"){
		strErrMSG += (document.form_insert.var_pf_cpf.value == "") ? "\nCPF do Colaborador" : "";
		if(document.form_insert.var_pf_cpf.value != ""){
			//alert(document.form_insert.var_pf_cpf.value);
			//alert(checkCPF(document.form_insert.var_pf_cpf.value,false));
			strErrMSG += (!checkCPF(document.form_insert.var_pf_cpf.value,false)) ? "\nCPF Inválido" : ""; 
			// strErrMSG += (document.form_insert.var_pf_rg.value == "") ? "\nRG do Colaborador" : "";
			strErrMSG += (document.form_insert.var_pf_nome.value == "") ? "\nNome do Colaborador" : "";
			strErrMSG += (document.form_insert.var_pf_sexo.value == "") ? "\nSexo do Colaborador" : "";
		}
	}
	if(getCheckedValue(document.form_insert.var_tipo_pf) == "PJ_PF"){
		strErrMSG += (document.form_insert.var_pf.value == "") ? "\nSelecione Colaborador" : "";
	}
	if(getCheckedValue(document.form_insert.var_tipo_pf) == "OLD_PF"){
		strErrMSG += (document.form_insert.var_pf_codigo.value == "") ? "\nCódigo Colaborador" : "";
	}
	// Consistência de Dados da Vaga do Colaborador
	// strErrMSG += (document.form_insert.var_vaga_funcao.value == "") ? "\nFunção do Colaborador" : "";
	if(getCheckedValue(document.form_insert.var_tipo_pf) != "PJ_PF"){
	strErrMSG += (document.form_insert.var_vaga_admissao.value == "") ? "\nData de Admissão" : "";
	}
	
	// Consistência de Dados da HOMOLOGAÇÃO
	strErrMSG += (document.form_insert.var_homo_data.value == "") ? "\nData da Homologação" : "";
	
	if(document.form_insert.var_tit_valor.value != ""){
		// alert(MoedaToFloat(document.form_insert.var_tit_valor.value));
		// alert(getCheckedValue(document.form_insert.var_tit_opcao_gerar));
		strErrMSG += ((getCheckedValue(document.form_insert.var_tit_opcao_gerar) == "TIT_NEW") && (MoedaToFloat(document.form_insert.var_tit_valor.value) == 0)) ? "\nTítulo não pode ter valor zerado, caso queira emití-lo já quitado" : "";
	}
	strErrMSG += (document.form_insert.var_tit_valor.value == "") ? "\nValor do Título" : "";
	strErrMSG += (document.form_insert.var_tit_dt_vcto.value == "") ? "\nData de Vencimento" : "";
	strErrMSG += (document.form_insert.var_tit_centro_custo.value == "") ? "\nCentro de Custo" : "";
	strErrMSG += (document.form_insert.var_tit_conta.value == "") ? "\nConta Banco" : "";
	strErrMSG += (document.form_insert.var_tit_historico.value == "") ? "\nHistórico do Título" : "";
	strErrMSG += (document.form_insert.var_tit_boleto.value == "") ? "\nModelo de Boleto" : "";
	
	if(strErrMSG != ""){
		strErrMSG = "Verifique os campos abaixo:\n" + strErrMSG;
		alert(strErrMSG);
		return(null);
	} else{
		submeterForm();
	}
}
function ok(){ 
	strLocation = "";
	validateFormFields();
}
function cancelar(){ 
	document.location.href = "../modulo_SdHomologacao/";
}
function submeterForm(){ 
	document.form_insert.DEFAULT_LOCATION.value = strLocation;
	document.form_insert.submit();	
}
</script>
</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("725","",getTText("homologacao",C_TOUPPER)." - (".getTText("inserir_fast",C_NONE).")",CL_CORBAR_GLASS_1); ?>
<form name="form_insert" action="STinspfhomofastexec.php" method="post">
<input type="hidden" name="DEFAULT_LOCATION" value="" />
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="705" bgcolor="#FFFFFF" class="table_master" style="border:1px solid #BBB;">
		<tr><td align="left" valign="top" style="padding:15px 0px 0px 15px;"><strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong></td></tr>
		<tr>
			<td align="left" valign="top" style="padding:10px 75px 10px 75px;">
				<table cellspacing="2" cellpadding="4" border="0" width="100%">
					<!-- DIALOG INSERT -->
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_pj",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("opcao_pj",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<input type="radio" name="var_tipo_pj" value="NEW_PJ" checked='checked' class="inputclean" onClick="changeDisplay('pj_old','pj_new');" /><?php echo(getTText("new_pj",C_NONE));?><br />
							<input type="radio" name="var_tipo_pj" value="OLD_PJ" class="inputclean" onClick="changeDisplay('pj_new','pj_old');" /><?php echo(getTText("old_pj",C_NONE));?>
						</td>
					</tr>
				</table>
				
				<!-- BLOCO DE DADOS PARA PRIMEIRO RADIO -->
				<table id="pj_new" cellspacing="2" cellpadding="4" border="0" width="100%">	
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("cnpj",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top"><input type="text" name="var_pj_cnpj" size="20" maxlength="14" onKeyPress="return validateNumKey(event);" /><span class="comment_peq"><?php echo(getTText("somente_numeros",C_NONE));?></span></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("razao_social",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_pj_razao" size="50" maxlength="200" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("cnae_grupo",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<select name="var_pj_cnae_grupo" style="width:220px;">
							<?php echo(montaCombo($objConn,"SELECT cod_cnae_grupo, cod_digi_grupo||' - '||nome AS rotulo FROM cad_cnae_grupo","cod_cnae_grupo","rotulo","2278"));?>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("cep",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<input type="text" name="var_pj_ender_cep" id="var_pj_ender_cep" size="10" maxlength="8" onKeyPress="return validateNumKey(event);" />&nbsp;<img src="../img/icon_zoom_disabled.gif" style="cursor:pointer;border:none" onClick="javascript:ajaxBuscaCEP('var_pj_ender_cep','var_pj_ender_logradouro','var_pj_ender_bairro','var_pj_ender_cidade','var_pj_ender_estado','var_pj_ender_numero','loader_cep')">
							<span class="comment_peq"><?php echo(getTText("somente_numeros",C_NONE));?></span>&nbsp;&nbsp;<span id="loader_cep"></span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("logradouro",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_pj_ender_logradouro" id="var_pj_ender_logradouro" size="30" maxlength="100" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("numero",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<input type="text" name="var_pj_ender_numero" id="var_pj_ender_numero" size="5" maxlength="10" />
							<strong><?php echo(getTText("complemento",C_UCWORDS));?>:</strong>
							<input type="text" name="var_pj_ender_complemento" id="var_pj_ender_complemento" size="10" maxlength="20" />
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("cidade",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_pj_ender_cidade" id="var_pj_ender_cidade" size="30" maxlength="30" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("bairro",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<input type="text" name="var_pj_ender_bairro" id="var_pj_ender_bairro" size="20" maxlength="30" />
							<strong><?php echo(getTText("estado",C_UCWORDS));?>:</strong>
							<select name="var_pj_ender_estado" id="var_pj_ender_estado" style="width:50px;">
							<?php echo(montaCombo($objConn,"SELECT sigla_estado FROM lc_estado ORDER BY sigla_estado","sigla_estado","sigla_estado","SP"));?>
							</select>
							
							<input type="hidden" name="var_pj_ender_pais" value="BRASIL" />
						</td>
					</tr>
				</table>
				
				<!-- BLOCO DE CÓDIGO PARA O SEGUNDO RADIO -->
				<table id="pj_old" cellspacing="2" cellpadding="4" border="0" width="100%" style="display:none;">
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top">*<strong><?php echo(getTText("codigo_pj",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"  valign="top">
						<!--[limpaSelect('dbvar_str_cod_cnae_n3ô_000');limpaSelect('dbvar_str_cod_cnae_n4_000');limpaSelect('dbvar_str_cod_cnae_n5_000');ajaxDetailData((this.value != '') ? 'SELECT cod_cnae_divisao, cod_digi_divisao||\' - \'||nome as nome FROM cad_cnae_divisao WHERE cod_cnae_secao = ' + this.value : '','ajaxMontaCombo','dbvar_str_cod_cnae_n2ô_000','','')]-->
							<input type="text" name="var_pj_codigo" id="var_pj_codigo" size="5" maxlength="10" onKeyPress="return validateNumKey(event);" style="margin-bottom:4px;" onBlur="limpaSelect('var_pf');ajaxDetailData((this.value != '') ? 'SELECT cad_pf.cod_pf, cad_pf.nome||\', \'||relac_pj_pf.funcao  FROM relac_pj_pf INNER JOIN cad_pf ON (cad_pf.cod_pf = relac_pj_pf.cod_pf) WHERE dt_demissao IS NULL AND cod_pj = ' + this.value : '','ajaxMontaComboNotNull','var_pf','');" />
							<span style="cursor:pointer;">
									<img src="../img/icon_view_wizard.gif" title="<?php echo(getTText("localizar_pessoa_juridica",C_TOUPPER));?>" onClick="javascript:abreJanelaPageLocal('../modulo_CadPJ/?var_acao=single&var_fieldname=var_pj_codigo&var_formname=form_insert','');" />
							</span>
						</td>
					</tr>
				</table>
				
				<table cellspacing="2" cellpadding="4" border="0" width="100%">
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_pf_e_vaga",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("opcao_pf",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<input type="radio" name="var_tipo_pf" value="NEW_PF" class="inputclean" onClick="changeDisplay('old_pf','new_pf');changeDisplay('pj_pf','new_pf');changeDisplay('pj_pf','pf_vaga');" /><?php echo(getTText("new_pf",C_NONE));?><br />
							<input type="radio" name="var_tipo_pf" value="PJ_PF"  class="inputclean" onClick="changeDisplay('new_pf','pj_pf');changeDisplay('old_pf','pj_pf');changeDisplay('pf_vaga','pj_pf');" /><?php echo(getTText("pj_pf",C_NONE));?><br />
							<input type="radio" name="var_tipo_pf" value="OLD_PF" class="inputclean" onClick="changeDisplay('new_pf','old_pf');changeDisplay('pj_pf','old_pf');changeDisplay('pj_pf','pf_vaga');" /><?php echo(getTText("old_pf",C_NONE));?><br />
						</td>
					</tr>
				</table>
				
				<table cellspacing="2" cellpadding="4" border="0" id="old_pf" width="100%" style="display:none;">
					<tr>	
						<td width="23%" align="right" valign="top">*<strong><?php echo(getTText("codigo_pf",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"  valign="top">
							<input type="text" name="var_pf_codigo" id="var_pf_codigo" size="5" maxlength="10" onKeyPress="return validateNumKey(event);" style="margin-bottom:4px;" />
							<span style="cursor:pointer;"><img src="../img/icon_view_wizard.gif" title="<?php echo(getTText("localizar_pessoa_fisica",C_TOUPPER));?>" onClick="javascript:abreJanelaPageLocal('../modulo_CadPF/?var_acao=single&var_fieldname=var_pf_codigo&var_formname=form_insert','');" /></span>
						</td>
					</tr>
				</table>				
				
				
				<table cellspacing="2" cellpadding="4" border="0" id="pj_pf" width="100%" style="display:none;">
					<tr>	
						<td width="23%" align="right" valign="top">*<strong><?php echo(getTText("pf",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"  valign="top">
							<select name="var_pf" id="var_pf" style="width:200px;">
								<option value="" selected="selected"></option>
							</select>
						</td>
					</tr>
				</table>
				
				<table cellspacing="2" cellpadding="4" border="0" id="new_pf" width="100%" style="display:none;">
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top">*<strong><?php echo(getTText("cpf",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"  valign="top"><input type="text" name="var_pf_cpf" size="20" maxlength="11" onKeyPress="return validateNumKey(event);" onBlur="ajaxBuscaPF(this.value);" /><span class="comment_peq"><?php echo(getTText("somente_numeros",C_NONE));?></span><span id="loader_ajax_cpf"></span></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("rg",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_pf_rg" size="20" maxlength="10" onKeyPress="return validateNumKey(event);" /><span class="comment_peq"><?php echo(getTText("somente_numeros",C_NONE));?></span></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("nome",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<input type="text" name="var_pf_nome" size="42" maxlength="100" />
							*<strong><?php echo(getTText("sexo",C_UCWORDS));?>:</strong>
							<select name="var_pf_sexo" style="width:40px;" >
								<option value="M"><?php echo(getTText("M",C_TOUPPER))?></option>
								<option value="F"><?php echo(getTText("F",C_TOUPPER))?></option>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("obs",C_UCWORDS));?>:</strong></td>
						<td align="left"><textarea name="var_pf_obs" rows="5" cols="60"></textarea></td>
					</tr>
				</table>
				
				<table cellspacing="2" cellpadding="4" border="0" id="pf_vaga" width="100%" style="display:none;">
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("categoria",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<select name="var_vaga_categoria" style="width:120px;" >
								<option value=""></option>
								<option value="GERAL"   ><?php echo(getTText("GERAL",C_TOUPPER))?></option>
								<option value="ESPECIAL" selected="selected"><?php echo(getTText("ESPECIAL",C_TOUPPER))?></option>
								<option value="PLENO"   ><?php echo(getTText("PLENO",C_TOUPPER))?></option>
							</select>	
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("tipo",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<select name="var_vaga_tipo" style="width:120px;" >
								<option value=""></option>
								<option value="AUTONOMO"    ><?php echo(getTText("AUTONOMO",C_TOUPPER))?></option>
								<option value="AVULSO"      ><?php echo(getTText("AVULSO",C_TOUPPER))?></option>
								<option value="TEMPORARIO"  ><?php echo(getTText("TEMPORÁRIO",C_TOUPPER))?></option>
								<option value="EMPREGADO" selected="selected"><?php echo(getTText("EMPREGADO",C_TOUPPER))?></option>
								<option value="ESTAGIO"     ><?php echo(getTText("ESTAGIÁRIO",C_TOUPPER))?></option>
							</select>	
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("funcao",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_vaga_funcao" size="50" maxlength="100" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("departamento",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_vaga_departamento" size="50" maxlength="100" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("admissao",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_vaga_admissao" size="10" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" /><span class="comment_peq"><?php echo(getTText("formato_data",C_NONE));?></span></td>					
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("obs_vaga",C_UCWORDS));?>:</strong></td>
						<td align="left"><textarea name="var_vaga_obs" rows="5" cols="60"></textarea></td>
					</tr>
				</table>
					
				<table cellspacing="2" cellpadding="4" border="0" width="100%">
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_homologacao",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("obs_homo",C_UCWORDS));?>:</strong></td>
						<td align="left"><textarea name="var_homo_obs" rows="5" cols="60"></textarea></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("data_homo",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_homo_data" size="14" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" /><span class="comment_peq"><?php echo(getTText("formato_data",C_NONE));?></span></td>					
					</tr>
					
					
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_titulo",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("opcao_tit",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">
							<input type="radio" name="var_tit_opcao_gerar" value="TIT_NEW" class="inputclean" checked="checked" />
							<?php echo(getTText("opcao_gerar_tit_quitado",C_NONE));?><br />
							<input type="radio" name="var_tit_opcao_gerar" value="TIT_OLD" class="inputclean" />
							<?php echo(getTText("opcao_n_gerar_tit_quitado",C_NONE));?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("tipo_documento",C_NONE)); ?>:</strong></td>
						<td align="left"  valign="top"><?php echo(getTText("boleto",C_NONE));?></td>
						<input type="hidden" name="var_tit_tipo_documento" value="BOLETO" />
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("valor",C_NONE)); ?>:</strong></td>
						<td align="left"  valign="top"><input name="var_tit_valor" value="0,00" size="10" maxlength="10" onKeyPress="javascript:return validateFloatKeyNew(this,event);" dir="rtl" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("dt_vcto",C_NONE));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_tit_dt_vcto" value="<?php echo($dtVcto);?>" size="14" maxlength="10" onKeyDown="FormataInputData(this,event);" onKeyPress="javascript:return validateNumKey(event);" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("centro_custo",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">
							<select name="var_tit_centro_custo" style="width:180px;">
							<?php echo(montaCombo($objConn,"SELECT cod_centro_custo, nome FROM fin_centro_custo WHERE dtt_inativo IS NULL ORDER BY ordem, nome","cod_centro_custo","nome","","")); ?>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("conta",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">
							<select name="var_tit_conta" style="width:180px;">
							<?php echo(montaCombo($objConn, " SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY ordem, nome","cod_conta","nome","","")); ?>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("plano_conta",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">
							<select name="var_tit_plano_conta" size="1" style="width:240px;">
							<?php echo(montaCombo($objConn,"SELECT cod_plano_conta, cod_reduzido ||' '|| nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ", "cod_plano_conta", "rotulo", 420, "")); ?>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("historico",C_NONE));?>:</strong></td>
						<td align="left"  valign="top"><input name="var_tit_historico" value="HOMOLOGAÇÃO RÁPIDA" size="55" maxlength="100" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("num_lcto",C_NONE));?>:</strong></td>
						<td align="left"  valign="top"><input name="var_tit_numero_lcto" value="" size="15" maxlength="30" /><span class="comment_med"><?php echo(getTText("obs_numlcto",C_NONE));?></span></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("num_documento",C_NONE));?>:</strong></td>
						<td align="left"  valign="top"><input name="var_tit_numero_documento" value="<?php echo(str_replace(" ","",(str_replace(":","",(str_replace("-","",now()))))));?>" size="15" maxlength="30" /><span class="comment_med"><?php echo(getTText("obs_numlcto",C_NONE));?></span></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("obs",C_NONE));?>:</strong></td>
						<td align="left"  valign="top"><textarea name="var_tit_obs" cols="60" rows="5"></textarea></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("boleto",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">
							<select name="var_tit_boleto" size="1" style="width:160px;">
							<?php echo(montaCombo($objConn, " SELECT cod_cfg_boleto, descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY descricao ", "cod_cfg_boleto", "descricao", $intCodCFGBoleto, "")); ?>
							</select>&nbsp;<!--<input type="checkbox" name="var_exibir_boleto" id="var_exibir_boleto" value="T" checked="checked" style="border:none;background:none;">Exibir boleto após gerar o título-->
						</td>
					</tr>
					
					<!-- DIALOG INSERT -->
					
					<tr><td colspan="2">&nbsp;</td></tr>
					
					<tr><td colspan="2" style="border-bottom:1px solid #CCC;text-align:left"><span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span></td></tr>
					<tr>
						<td colspan="2">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<td width="10%" align="right"><img src="../img/mensagem_aviso.gif" /></td>
									<td width="55%" align="left" style="padding-left:10px;"><?php echo(getTText("aviso_gerar_fast",C_NONE));?></td>
									<td width="35%" align="right">
										<button onClick="ok();return false;"><?php echo(getTText("ok",C_NONE));?></button>
										<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>			
			</td>
		</tr>
	</table>
</form>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_rv")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>