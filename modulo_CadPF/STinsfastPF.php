<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));

$objConn   = abreDBConn(CFG_DB);
		
?> 
<html>
<head>
<title>proEVENTO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript">
<!--

function submeterForm(){
	document.forminsfastPF.var_location.value = "STinsfastPF.php";
	document.forminsfastPF.submit();
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
	
	prObject = eval('document.forminsfastPF.' + prCampo);
	
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
//-->
</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center">
 <tr>
   <td align="center" valign="top">
	<?php athBeginFloatingBox("630","none","PESSOA FÍSICA (Inserção Rápida)",CL_CORBAR_GLASS_1); ?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;" cellspacing="0" cellpadding="4">
	   		<form name="forminsfastPF" action="STinsfastPFexec.php" method="post">
				<input type="hidden" name="var_location" value="">
				<input type="hidden" name="dbvar_int_cod_pf" id="dbvar_int_cod_pf_000" value="">
				<input type="hidden" name="dbvar_int_cod_endereco_pf" id="dbvar_int_cod_endereco_pf_000" value="">
				<tr><td height="22" style="padding:10px"><b>Preencha os campos abaixo</b></td></tr>
				<tr>
					<td align="center" valign="top">
						<table width="500" border="0" cellspacing="0" cellpadding="4">
							<tr bgcolor="#FAFAFA"> 
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_nome_doc_000">
										*<strong>CPF:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_valor" id="dbvar_str_valor_000" value="" type="text" size="20" maxlength="50"   title="número" tabindex="1" onBlur="buscaPorCPF(this.value)"></td>
							</tr>							
							<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_nome_000">
										*<strong>Nome:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_nome" id="dbvar_str_nome_000" value="" type="text" size="50" maxlength="100"   title="Nome" tabindex="2"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_date_data_nasc_000">
										<strong>Data Nascimento:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_date_data_nasc" id="dbvar_date_data_nasc_000" value="" type="text" size="10" maxlength="10"  onkeyup="Javascript:FormataInputData(this.name);" onKeyPress="Javascript:validateNumKey();"  title="Data Nascimento" tabindex="3"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_date_data_nasc_000">
										<strong>Sexo:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" >
									<select name="dbvar_str_sexo" id="dbvar_str_sexo_000" tabindex="4">
										<option value=""></option>
										<option value="M">Masculino</option>
										<option value="F">Feminino</option>
									</select>
								<span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_cod_pj_000">
											*<strong>Empresa:</strong>
										</label>
								</td>
								<td nowrap align="left" width="99%" >
									<select name="dbvar_cod_pj" class="edtext" style="width:300px;" tabindex="5">
										<option value=""selected></option>
										<?php echo(montaCombo($objConn,"SELECT cod_pj, nome_fantasia FROM cad_pj WHERE nome_fantasia <> '' AND ((cad_pj.sys_usr_ins = '{tradeunion_id_usuario}') OR (('{tradeunion_grp_user}' = 'SU') OR ('{tradeunion_grp_user}' = 'ADMIN' ))) ORDER BY 2","cod_pj","nome_fantasia","")); ?>
									</select>									
								</td>
							</tr>									
							<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_str_cargo_000">
											<strong>Cargo:</strong>
										</label>
								</td>
								<td nowrap align="left" width="99%" >
									<select id="dbvar_str_cargo_000" tabindex="6" class="edtext" name="dbvar_str_cargo" style="width: 135px;">
										<option value=""></option>
										<option value="ACOUGUEIRO">ACOUGUEIRO</option>
										<option value="ADMINISTRADOR">ADMINISTRADOR</option>
										<option value="ADVOGADO">ADVOGADO</option>
										<option value="ANALISTA">ANALISTA</option>
										<option value="ARQUITETO">ARQUITETO</option>
										<option value="ASSESSOR">ASSESSOR</option>
										<option value="ASSISTENTE">ASSISTENTE</option>
										<option value="ATENDENTE">ATENDENTE</option>
										<option value="AUDITOR">AUDITOR</option>
										<option value="AUTORIDADE MILITAR">AUTORIDADE MILITAR</option>
										<option value="AUXILIAR">AUXILIAR</option>
										<option value="BIBLIOTECARIO">BIBLIOTECARIO</option>
										<option value="CAIXA">CAIXA</option>
										<option value="COMPRADOR">COMPRADOR</option>
										<option value="CONSULTOR">CONSULTOR</option>
										<option value="CONTADOR">CONTADOR</option>
										<option value="CONTROLLER">CONTROLLER</option>
										<option value="COORDENADOR">COORDENADOR</option>
										<option value="CORRETOR">CORRETOR</option>
										<option value="DIRETOR">DIRETOR</option>
										<option value="EDITOR">EDITOR</option>
										<option value="ENCARREGADO">ENCARREGADO</option>
										<option value="ENGENHEIRO">ENGENHEIRO</option>
										<option value="FISCAL">FISCAL</option>
										<option value="FOTOGRAFO">FOTOGRAFO</option>
										<option value="GERENTE">GERENTE</option>
										<option value="GOVERNADOR">GOVERNADOR</option>
										<option value="JORNALISTA">JORNALISTA</option>
										<option value="MIDIA">MIDIA</option>
										<option value="MOTORISTA">MOTORISTA</option>
										<option value="NUTRICIONISTA">NUTRICIONISTA</option>
										<option value="OUVIDOR">OUVIDOR</option>
										<option value="PRESIDENTE">PRESIDENTE</option>
										<option value="PROCURADOR">PROCURADOR</option>
										<option value="PRODUTOR">PRODUTOR</option>
										<option value="PROFESSOR">PROFESSOR</option>
										<option value="PSICOLOGO">PSICOLOGO</option>
										<option value="PUBLICITARIO">PUBLICITARIO</option>
										<option value="REDATOR">REDATOR</option>
										<option value="REPORTER">REPORTER</option>
										<option value="REPOSITOR">REPOSITOR</option>
										<option value="REPRESENTANTE">REPRESENTANTE</option>
										<option value="SECRETARIA">SECRETARIA</option>
										<option value="SECRETARIO">SECRETARIO</option>
										<option value="SUB EDITOR">SUB EDITOR</option>
										<option value="SUB GERENTE">SUB GERENTE</option>
										<option value="SUPERINTENDENTE">SUPERINTENDENTE</option>
										<option value="SUPERVISOR">SUPERVISOR</option>
										<option value="TESOUREIRO">TESOUREIRO</option>
										<option value="TRADE">TRADE</option>
										<option value="VENDEDOR">VENDEDOR</option>
										<option value="VICE DIRETOR">VICE DIRETOR</option>
										<option value="VICE GOVERNADOR">VICE GOVERNADOR</option>
										<option value="VICE PRESIDENTE">VICE PRESIDENTE</option>
									</select>									
								</td>
							</tr>
							<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_str_departamento_000">
											<strong>Departamento:</strong>
										</label>
								</td>
								<td nowrap align="left" width="99%" >
									<select id="dbvar_str_departamento_000" class="edtext" name="dbvar_str_departamento" style="width: 151px;" tabindex="7">
										<option value=""></option>
										<option value="ABASTECIMENTO">ABASTECIMENTO</option>
										<option value="ACOUGUE">ACOUGUE</option>
										<option value="ADMINISTRATIVA">ADMINISTRATIVA</option>
										<option value="ATENDIMENTO">ATENDIMENTO</option>
										<option value="BAZAR">BAZAR</option>
										<option value="CAIXA">CAIXA</option>
										<option value="CATEGORIA">CATEGORIA</option>
										<option value="COMERCIAL">COMERCIAL</option>
										<option value="COMPRAS">COMPRAS</option>
										<option value="COMUNICACAO">COMUNICACAO</option>
										<option value="CONSELHO">CONSELHO</option>
										<option value="CONTABIL E CONTROLAD">CONTABIL E CONTROLAD</option>
										<option value="ECONOMIA">ECONOMIA</option>
										<option value="ENGENHARIA">ENGENHARIA</option>
										<option value="ESTUDOS E PESQUISAS">ESTUDOS E PESQUISAS</option>
										<option value="FINANCEIRO">FINANCEIRO</option>
										<option value="FLV">FLV</option>
										<option value="HIGIENE E LIMPEZA">HIGIENE E LIMPEZA</option>
										<option value="IMPORT. E EXPORT.">IMPORT. E EXPORT.</option>
										<option value="INFORMATICA">INFORMATICA</option>
										<option value="JURIDICO">JURIDICO</option>
										<option value="LOGISTICA">LOGISTICA</option>
										<option value="LOJA">LOJA</option>
										<option value="MANUTENCAO">MANUTENCAO</option>
										<option value="MARKETING">MARKETING</option>
										<option value="MERCEARIA">MERCEARIA</option>
										<option value="MIDIA">MIDIA</option>
										<option value="NEGOCIOS">NEGOCIOS</option>
										<option value="OPERACOES">OPERACOES</option>
										<option value="ORGAO PUBLICO">ORGAO PUBLICO</option>
										<option value="PADARIA">PADARIA</option>
										<option value="PEIXARIA">PEIXARIA</option>
										<option value="PERECIVEIS">PERECIVEIS</option>
										<option value="PRODUÇAO">PRODUÇAO</option>
										<option value="PRODUTO">PRODUTO</option>
										<option value="PROM EVENTOS MERCHAN">PROM EVENTOS MERCHAN</option>
										<option value="QUALIDADE">QUALIDADE</option>
										<option value="RECURSOS HUMANOS">RECURSOS HUMANOS</option>
										<option value="SEG. DO TRABALHO">SEG. DO TRABALHO</option>
										<option value="SEGURANCA ALIMENTAR">SEGURANCA ALIMENTAR</option>
										<option value="SERVICOS">SERVICOS</option>
										<option value="SOCIAL">SOCIAL</option>
										<option value="VENDAS">VENDAS</option>
									</select>									
								</td>
							</tr>
								<tr>
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_str_atividade_000">
											<strong>Data de Admissão:</strong>
										</label>
								</td>
								<td nowrap align="left" width="99%" >
									<input name="dbvar_date_data_admissao" id="dbvar_date_data_admissao_000" value="" type="text" size="10" maxlength="10"  onkeyup="Javascript:FormataInputData(this.name);" onKeyPress="Javascript:validateNumKey();"  title="Data Nascimento" tabindex="8">									
								</td>
							</tr>	
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>ENDEREÇO</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg" s></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>								
							<tr bgcolor="#FFFFFF">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_cep_000">
										<strong>CEP:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_cep" id="dbvar_str_cep_000" value="" type="" size="" maxlength="" title="CEP" tabindex="9"></td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="dbvar_str_logradouro_000">
											<strong>Logradouro:</strong>
										</label>
								</td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left"><input name="dbvar_str_logradouro" id="dbvar_str_logradouro_000" value="" type="text" size="50" maxlength="255"   title="logradouro" tabindex="10" onChange="dbvar_str_endereco.value = dbvar_str_endereco.value + this.value;"></td>
											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_numero_000">
													<strong>&nbsp;Número:</strong>
												</label>
											</td>
											<td nowrap> <input name="dbvar_str_numero" id="dbvar_str_numero_000" value="" type="text" size="10" maxlength="10" title="número" tabindex="11" onChange="dbvar_str_endereco.value = dbvar_str_endereco.value + ', ' + this.value;"></td>
											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_complemento_000">
													<strong>&nbsp; Compl.:</strong>
												</label>
											</td>
											<td nowrap align="left" width="99%" ><input name="dbvar_str_complemento" id="dbvar_str_complemento_000" value="" type="text" size="10" maxlength="12" title="compl." tabindex="11" onChange="dbvar_str_endereco.value = dbvar_str_endereco.value + ', ' + this.value;"></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_endereco_000">
										<strong>Endereço:</strong>									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_endereco" id="dbvar_str_endereco_000" value="" type="text" size="50" maxlength="255" title="endereço" tabindex="13"></td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_bairro_000">
										<strong>Bairro:</strong>									</label>
								</td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left"><input name="dbvar_str_bairro" id="dbvar_str_bairro_000" value="" type="text" size="20" maxlength="30"   title="bairro" tabindex="14"></td>
  											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_cidade_000">
													<strong>&nbsp; Cidade:</strong>
												</label>
											</td>
											<td nowrap align="left" width="99%" ><input name="dbvar_str_cidade" id="dbvar_str_cidade_000" value="" type="text" size="20" maxlength="30" title="cidade" tabindex="15"></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="#FFFFFF">
								
                  <td width="1%" height="32" align="right" valign="top" nowrap style="padding-right:5px;"> 
                    <label for="dbvar_str_estado_000">
										<strong>UF:</strong>
									</label>
								</td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left">	
												<select name="dbvar_str_estado" id="dbvar_str_estado_000" style="width:40px" size="1" title="UF" tabindex="16">
													<option value="" selected></option>
													<option value="AC"><i>AC</i></option>
													<option value="AL"><i>AL</i></option>
													<option value="AP"><i>AP</i></option>
													<option value="AM"><i>AM</i></option>
													<option value="BA"><i>BA</i></option>
													<option value="CE"><i>CE</i></option>
													<option value="DF"><i>DF</i></option>
													<option value="ES"><i>ES</i></option>
													<option value="GO"><i>GO</i></option>
													<option value="MA"><i>MA</i></option>
													<option value="MT"><i>MT</i></option>
													<option value="MS"><i>MS</i></option>
													<option value="MG"><i>MG</i></option>
													<option value="PA"><i>PA</i></option>
													<option value="PB"><i>PB</i></option>
													<option value="PR"><i>PR</i></option>
													<option value="PE"><i>PE</i></option>
													<option value="PI"><i>PI</i></option>
													<option value="RJ"><i>RJ</i></option>
													<option value="RN"><i>RN</i></option>
													<option value="RS"><i>RS</i></option>
													<option value="RO"><i>RO</i></option>
													<option value="RR"><i>RR</i></option>
													<option value="SC"><i>SC</i></option>
													<option value="SP"><i>SP</i></option>
													<option value="SE"><i>SE</i></option>
													<option value="TO"><i>TO</i></option>
												</select>
												
											</td>
  											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_pais_000"><strong>&nbsp;País:</strong></label>
											</td>
											<td nowrap align="left" width="99%" >
												<select name="dbvar_str_pais" id="dbvar_str_pais_000" style="width:210px" size="1" title="país" tabindex="17">
												<option value="" selected></option>
												<?php 
												echo(montaCombo($objConn, " SELECT nome FROM lc_pais ORDER BY nome ", "nome", "nome", "", "")); 
												?>
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_email_000">
										<strong>e-mail:</strong>
									</label>
								</td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left"><input name="dbvar_str_email" id="dbvar_str_email_000" value="" type="text" size="35" maxlength="255" title="e-mail" tabindex="18"></td>
  											<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
												<label for="dbvar_str_email_extra_000">
													<strong>&nbsp; e-mail extra:</strong>
												</label>
											</td>
											<td nowrap align="left" width="99%" ><input name="dbvar_str_email_extra" id="dbvar_str_email_extra_000" value="" type="text" size="35" maxlength="255" title="e-mail extra" tabindex="19"></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_homepage_000">
										<strong>Homepage:</strong>									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_homepage" id="dbvar_str_homepage_000" value="" type="text" size="35" maxlength="255"   title="homepage" tabindex="20"></td>
							</tr>
							<tr bgcolor="#FAFAFA">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_fone_000">
										<strong>Fone 1:</strong>									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_fone" id="dbvar_str_fone_000" value="" type="" size="" maxlength=""   title="fone 1" tabindex="21"></td>
							</tr>
							<tr bgcolor="#FFFFFF">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_fone_extra1_000">
										<strong>Fone 2:</strong>									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_fone_extra1" id="dbvar_str_fone_extra1_000" value="" type="" size="" maxlength=""   title="fone 2" tabindex="22"></td>
							</tr>
							<tr bgcolor="#FAFAFA">  
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_fone_extra2_000">
										<strong>Fone 3:</strong>									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_fone_extra2" id="dbvar_str_fone_extra2_000" value="" type="" size="" maxlength=""   title="fone 3" tabindex="23"></td>
							</tr>
							<tr bgcolor="#FFFFFF">  
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
									<label for="dbvar_str_fone_extra3_000">
										<strong>Fone 4:</strong>
									</label>
								</td>
								<td nowrap align="left" width="99%" ><input name="dbvar_str_fone_extra3" id="dbvar_str_fone_extra3_000" value="" type="" size="" maxlength=""   title="fone 4" tabindex="24"></td>
							</tr>	
							<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>		
							<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
						</table>
					</td>
				</tr>
				<tr>
					<td align="right" colspan="3" style="padding:10px 0px 10px 10px;">
						<button onClick="document.forminsfastPF.submit();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
						<button onClick="history.back();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
						<button onClick="submeterForm();return false;"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
					</td>
				</tr>					
		  		</form>
			</table>
			<?php athEndFloatingBox(); ?>
		</td>
	</tr>
</table>
</body></html>