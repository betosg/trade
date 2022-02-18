<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intCodDado    = request("var_chavereg");   //Código chave
$strExec       = request("var_exec");       //Executor externo (fora do kernel)
$intTabActived = request("var_tabactived"); //Código da Tab ativada

//if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

setsession($strSesPfx . "_cod_projeto", $intCodDado); 

//verficarAcesso(getsession("sys_cod_usuario"), getsession($strSesPfx . "_chave_app"), $strOperacao);

$objConn  = abreDBConn(CFG_DB);

try {
	$strSQL = "SELECT cod_projeto, cod_estab, aplicacaonome, pecaobranome, produtofinalnome FROM jms_projeto WHERE cod_projeto =" . $intCodDado ;
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e){ 	
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
 $objRS = $objResult->fetch();
 
?>
<html>
<head>
	 <title>FPS Project - Athenas Software And Systems</title>
	 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	  <link href="../_css/fpsproject.css" rel="stylesheet" type="text/css"> 

	<link rel="stylesheet" href="../_css/tabview.css" type="text/css" media="screen">
	<script type="text/javascript" src="../_scripts/ajax.js"></script>
	<script type="text/javascript" src="../_scripts/tabview.js"></script>

<script language="javascript" type="text/javascript">
<!--
//********************************************************************************************************

//******************* Funções de ação dos botões [formeditor_000 - prinicpal] - Início *******************

//********************************************************************************************************
function VerificaCampos(prCampo1, prCampo2, prCaso, prChBox, prChecked, prCampoJX, prMsg) {
	var var_msg = '';
	
	if (prChBox == true) {
		if (prChecked == true) {
			if (prCampoJX == '') var_msg = prMsg;
		}
		else {
			if (prCaso == 'A') { if (prCampo1 == '') var_msg = prMsg; }
			if (prCaso == 'B') { if ((prCampo1 == '') && (prCampo2 == '')) var_msg = prMsg; }
			if (prCaso == 'C') { if ((prCampo1 == '') || (prCampo2 == '')) var_msg = prMsg; }
		}
	}
	else {
		if (prCampo1 == '') var_msg = prMsg;
	}
	
	return var_msg;
}

function Verifica() {
	var var_msg_material = '';
	var var_msg_requisitos = '';
	var var_msg_processo = '';
	var var_msg_equipamento = '';
	var var_msg_ferramenta_uso = '';
	var var_msg_teste = '';
	var var_msg_resultado = '';
	
	/************************************************/
	/* material                                     */
	/************************************************/
	var_msg_material += VerificaCampos( ifr_material.document.formeditor.dbvar_str_matlgrupo.value
							 , ''
							 , 'A'
							 , true
							 , ifr_material.document.formeditor.var_chbox_matlgrupo.checked
							 , ifr_material.document.formeditor.dbvar_str_matlgrupo_jx.value
							 , '\nMaterial - Grupo');
	
	var_msg_material += VerificaCampos( ifr_material.document.formeditor.dbvar_str_matltipo.value
							 , ''
							 , 'A'
							 , true
							 , ifr_material.document.formeditor.var_chbox_matltipo.checked
							 , ifr_material.document.formeditor.dbvar_str_matltipo_jx.value
							 , '\nMaterial - Tipo');
	
	var_msg_material += VerificaCampos( ifr_material.document.formeditor.dbvar_str_matlconform.value
							 , ''
							 , 'A'
							 , true
							 , ifr_material.document.formeditor.var_chbox_matlconform.checked
							 , ifr_material.document.formeditor.dbvar_str_matlconform_jx.value
							 , '\nMaterial - Conformação');
	
	var_msg_material += VerificaCampos( ifr_material.document.formeditor.dbvar_str_matlttermico.value
							 , ''
							 , 'A'
							 , true
							 , ifr_material.document.formeditor.var_chbox_matlttermico.checked
							 , ifr_material.document.formeditor.dbvar_str_matlttermico_jx.value
							 , '\nMaterial - Tratamento Térmico');
	
	var_msg_material += VerificaCampos( ifr_material.document.formeditor.dbvar_num_matldurezavalor.value
							 , ifr_material.document.formeditor.dbvar_str_matldurezaudm.value
							 , 'C'
							 , true
							 , ifr_material.document.formeditor.var_chbox_matldurezavalor.checked
							 , ifr_material.document.formeditor.dbvar_str_matldureza_jx.value
							 , '\nMaterial - Dureza');
	
	var_msg_material += VerificaCampos( ifr_material.document.formeditor.dbvar_str_matlnomecoml.value
							 , ''
							 , 'A'
							 , true
							 , ifr_material.document.formeditor.var_chbox_matlnomecoml.checked
							 , ifr_material.document.formeditor.dbvar_str_matlnomecoml_jx.value
							 , '\nMaterial - Nome Comercial');
	
	var_msg_material += VerificaCampos( ifr_material.document.formeditor.dbvar_str_matlnorma.value
							 , ''
							 , 'A'
							 , true
							 , ifr_material.document.formeditor.var_chbox_matlnorma.checked
							 , ifr_material.document.formeditor.dbvar_str_matlnorma_jx.value
							 , '\nMaterial - Norma');
	
	var_msg_material += VerificaCampos( ifr_material.document.formeditor.dbvar_str_matlobs.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nMaterial - Observações');
	
	/************************************************/
	/* requisitos                                   */
	/************************************************/
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requistipofuro.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Tipo de Furo');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_moeda_requisdiamfuro.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Diâmetro');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_moeda_requisproffuro.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Frofundidade');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_moeda_requisangulopen.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Ângulo de Penetração');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requistoldiaminfo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Diâmetro');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requistolcircinfo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Circularidade');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requistolconceninfo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Concentricidade');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requistolparalinfo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Paralelismo');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requistolrugoinfo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Rugosidade');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requisotherprefuroinfo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Pré-Furo');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requisotheralarginfo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Alargamento');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requisotherrosqinfo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Rosqueamento');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requisotherescarinfo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Escariamento');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requisotherescalinfo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Escalonamento');
	
	var_msg_requisitos += VerificaCampos( ifr_requisitos.document.formeditor.dbvar_str_requis_obs.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nRequisitos - Observações');
	
	/************************************************/
	/* processo                                     */
	/************************************************/
	var_msg_processo += VerificaCampos( ifr_processo.document.formeditor.dbvar_str_processtipo.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nProcesso - Tipo de Processo');
	
	var_msg_processo += VerificaCampos( ifr_processo.document.formeditor.dbvar_moeda_processfurospeca.value
							 , ifr_processo.document.formeditor.dbvar_str_processfurospeca_cf.value
							 , 'C'
							 , true
							 , ifr_processo.document.formeditor.var_chbox_processfurospeca.checked
							 , ifr_processo.document.formeditor.dbvar_str_processfurospeca_jx.value
							 , '\nProcesso - Número de Furos/Peça');
	
	var_msg_processo += VerificaCampos( ifr_processo.document.formeditor.dbvar_moeda_processpecasmes.value
							 , ifr_processo.document.formeditor.dbvar_str_processpecasmes_cf.value
							 , 'C'
							 , true
							 , ifr_processo.document.formeditor.var_chbox_processpecasmes.checked
							 , ifr_processo.document.formeditor.dbvar_str_processpecasmes_jx.value
							 , '\nProcesso - Número de Furos/Mês');
	
	var_msg_processo += VerificaCampos( ifr_processo.document.formeditor.dbvar_moeda_processcustohora.value
							 , ifr_processo.document.formeditor.dbvar_str_processcustohora_cf.value
							 , 'C'
							 , true
							 , ifr_processo.document.formeditor.var_chbox_processcustohora.checked
							 , ifr_processo.document.formeditor.dbvar_str_processcustohora_jx.value
							 , '\nProcesso - Custo Hora/Operação');
	
	var_msg_processo += VerificaCampos( ifr_processo.document.formeditor.dbvar_moeda_processpecasperd.value
							 , ifr_processo.document.formeditor.dbvar_str_processpecasperd_cf.value
							 , 'C'
							 , true
							 , ifr_processo.document.formeditor.var_chbox_processpecasperd.checked
							 , ifr_processo.document.formeditor.dbvar_str_processpecasperd_jx.value
							 , '\nProcesso - Peças Perdidas/Ajuste');
	
	var_msg_processo += VerificaCampos( ifr_processo.document.formeditor.dbvar_str_processgargalo.value
							 , ifr_processo.document.formeditor.dbvar_str_processgargalo_cf.value
							 , 'C'
							 , true
							 , ifr_processo.document.formeditor.var_chbox_processgargalo.checked
							 , ifr_processo.document.formeditor.dbvar_str_processgargalo_jx.value
							 , '\nProcesso - Representa Gargalo');
	
	var_msg_processo += VerificaCampos( ifr_processo.document.formeditor.dbvar_str_processobs.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nProcesso - Observações');
	
	/************************************************/
	/* ficha de consumo                             */
	/************************************************/
	// sem campos a validar
	
	/************************************************/
	/* amostras                                     */
	/************************************************/
	// sem campos a validar
	
	/************************************************/
	/* equipamento                                  */
	/************************************************/
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_num_equiptipo.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equiptipo.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equiptipo_jx.value
							 , '\nEquipamento - Tipo');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_num_equipmovim.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipmovim.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipmovim_jx.value
							 , '\nEquipamento - Movimento Usinagem');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_num_equippos.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equippos.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equippos_jx.value
							 , '\nEquipamento - Posição Operação');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_moeda_equipfurossimult.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipfurossimult.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipfurossimult_jx.value
							 , '\nEquipamento - Furos Simultâneos');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_moeda_equippotencia.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equippotencia.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equippotencia_jx.value
							 , '\nEquipamento - Potência');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_moeda_equiprpmmin.value
							 , ifr_equipamento.document.formeditor.dbvar_moeda_equiprpmmax.value
							 , 'C'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equiprpm.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equiprpm_jx.value
							 , '\nEquipamento - Faixa de RPM');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_moeda_equipavanmin.value
							 , ifr_equipamento.document.formeditor.dbvar_moeda_equipavanmax.value
							 , 'C'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipavan.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipavan_jx.value
							 , '\nEquipamento - Faixa de Avanço');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_num_equiprefr.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equiprefr.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equiprefr_jx.value
							 , '\nEquipamento - Refrigeração');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_num_equipfluido.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipfluido.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipfluido_jx.value
							 , '\nEquipamento - Tipo de Fluido');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_moeda_equipfluidopressmin.value
							 , ifr_equipamento.document.formeditor.dbvar_moeda_equipfluidopressmax.value
							 , 'C'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipfluidopress.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipfluidopress_jx.value
							 , '\nEquipamento - Pressão do Fluido');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_moeda_equipfluidovazmin.value
							 , ifr_equipamento.document.formeditor.dbvar_moeda_equipfluidovazmax.value
							 , 'C'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipfluidovaz.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipfluidovaz_jx.value
							 , '\nEquipamento - Vazão do Fluido');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_num_equipfixacao.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipfixacao.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipfixacao_jx.value
							 , '\nEquipamento - Tipo de Fixação');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_moeda_equipfixacaofaixamin.value
							 , ifr_equipamento.document.formeditor.dbvar_moeda_equipfixacaofaixamax.value
							 , 'C'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipfixacaofaixa.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipfixacaofaixa_jx.value
							 , '\nEquipamento - Faixa de Fixação');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_num_equipfixacaoquali.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipfixacaoquali.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipfixacaoquali_jx.value
							 , '\nEquipamento - Qualidade da Fixação');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_moeda_equipbatim.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipbatim.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipbatim_jx.value
							 , '\nEquipamento - Batimento');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_str_equipfabr.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipfabr.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipfabr_jx.value
							 , '\nEquipamento - Fabricante');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_str_equipmodelo.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipmodelo.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipmodelo_jx.value
							 , '\nEquipamento - Modelo');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_num_equipano.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipano.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipano_jx.value
							 , '\nEquipamento - Ano de Fabricação');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_str_equipnoserie.value
							 , ''
							 , 'A'
							 , true
							 , ifr_equipamento.document.formeditor.var_chbox_equipnoserie.checked
							 , ifr_equipamento.document.formeditor.dbvar_str_equipnoserie_jx.value
							 , '\nEquipamento - Número de Série');
	
	var_msg_equipamento += VerificaCampos( ifr_equipamento.document.formeditor.dbvar_str_equipobs.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nEquipamento - Observações');
	
	/************************************************/
	/* ferramenta em uso                            */
	/************************************************/
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_str_ferrammarca.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferrammarca.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferrammarca_jx.value
							 , '\nFerramenta em Uso - Marca');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_str_ferrammodelo.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferrammodelo.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferrammodelo_jx.value
							 , '\nFerramenta em Uso - Modelo/Norma');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramsubstrato.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramsubstrato.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramsubstrato_jx.value
							 , '\nFerramenta em Uso - Substrato');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramcanal.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramcanal.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramcanal_jx.value
							 , '\nFerramenta em Uso - Geometria Canal');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramrefr.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramrefr.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramrefr_jx.value
							 , '\nFerramenta em Uso - Refrigeração');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramrevest.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramrevest.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramrevest_jx.value
							 , '\nFerramenta em Uso - Revestimento');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_moeda_ferramdiamcorte.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramdiamcorte.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramdiamcorte_jx.value
							 , '\nFerramenta em Uso - Diâmetro do Corte');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_moeda_ferramdiamhaste.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramdiamhaste.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramdiamhaste_jx.value
							 , '\nFerramenta em Uso - Diâmetro da Haste');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramhastetipo.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramhastetipo.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramhastetipo_jx.value
							 , '\nFerramenta em Uso - Tipo de Haste');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_moeda_ferramcomprtotal.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramcomprtotal.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramcomprtotal_jx.value
							 , '\nFerramenta em Uso - Comprimento Total');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_moeda_ferramcomprcanal.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramcomprcanal.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramcomprcanal_jx.value
							 , '\nFerramenta em Uso - Comprimento do Canal');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramafiatipo.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramafiatipo.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramafiatipo_jx.value
							 , '\nFerramenta em Uso - Tipo de Afiação');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_moeda_ferramafiaang.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramafiaang.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramafiaang_jx.value
							 , '\nFerramenta em Uso - Ângulo de Afiação');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_moeda_ferramvc.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramvc.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramvc_jx.value
							 , '\nFerramenta em Uso - Vc');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_moeda_ferramavan.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramavan.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramavan_jx.value
							 , '\nFerramenta em Uso - Avanço');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_num_ferramfurosafianova.value
							 , ifr_ferramenta_uso.document.formeditor.dbvar_num_ferramfurosafiareaf.value
							 , 'C'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramfurosafia.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramfurosafia_jx.value
							 , '\nFerramenta em Uso - Furos por Afiação');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_num_ferramafiabroca.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramafiabroca.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramafiabroca_jx.value
							 , '\nFerramenta em Uso - Afiações por Broca');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramreafiaforma.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramreafiaforma.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramreafiaforma_jx.value
							 , '\nFerramenta em Uso - Forma Reafiação');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_moeda_ferramcustobroca.value
							 , ''
							 , 'A'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramcustobroca.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramcustobroca_jx.value
							 , '\nFerramenta em Uso - Custo por Broca');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_num_ferramtempociclomin.value
							 , ifr_ferramenta_uso.document.formeditor.dbvar_num_ferramtempocicloseg.value
							 , 'B'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramtempociclo.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramtempociclo_jx.value
							 , '\nFerramenta em Uso - Tempo de Ciclo');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_num_ferramtempotrocamin.value
							 , ifr_ferramenta_uso.document.formeditor.dbvar_num_ferramtempotrocaseg.value
							 , 'B'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramtempotroca.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramtempotroca_jx.value
							 , '\nFerramenta em Uso - Tempo de Troca');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_num_ferramtempoajustemin.value
							 , ifr_ferramenta_uso.document.formeditor.dbvar_num_ferramtempoajusteseg.value
							 , 'B'
							 , true
							 , ifr_ferramenta_uso.document.formeditor.var_chbox_ferramtempoajuste.checked
							 , ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramtempoajuste_jx.value
							 , '\nFerramenta em Uso - Tempo de Ajuste');
	
	var_msg_ferramenta_uso += VerificaCampos( ifr_ferramenta_uso.document.formeditor.dbvar_str_ferramobs.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nFerramenta em Uso - Observações');
	
	/************************************************/
	/* teste                                        */
	/************************************************/
	var_msg_teste += VerificaCampos( ifr_teste.document.formeditor.dbvar_moeda_resultafiabroca.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nTeste - Afiações por Ferramenta');
	
	var_msg_teste += VerificaCampos( ifr_teste.document.formeditor.dbvar_num_resulttempotrocamin.value
							 , ifr_teste.document.formeditor.dbvar_num_resulttempotrocaseg.value
						 	 , 'B'
							 , true
							 , ifr_teste.document.formeditor.var_chbox_resulttempotroca.checked
							 , ifr_teste.document.formeditor.dbvar_str_resulttempotroca_jx.value
							 , '\nTeste - Tempo de Troca');
	
	var_msg_teste += VerificaCampos( ifr_teste.document.formeditor.dbvar_num_resulttempoajustemin.value
							 , ifr_teste.document.formeditor.dbvar_num_resulttempoajusteseg.value
							 , 'B'
							 , true
							 , ifr_teste.document.formeditor.var_chbox_resulttempoajuste.checked
							 , ifr_teste.document.formeditor.dbvar_str_resulttempoajuste_jx.value
							 , '\nTeste - Tempo de Ajuste');
	
	var_msg_teste += VerificaCampos( ifr_teste.document.formeditor.dbvar_num_resulttempociclomin.value
							 , ifr_teste.document.formeditor.dbvar_num_resulttempocicloseg.value
							 , 'B'
							 , true
							 , ifr_teste.document.formeditor.var_chbox_resulttempociclo.checked
							 , ifr_teste.document.formeditor.dbvar_str_resulttempociclo_jx.value
							 , '\nTeste - Tempo de Ciclo');
	
	/************************************************/
	/* resultado                                    */
	/************************************************/
	/*
	// Sem verificações por enquanto
	var_msg_resultado += VerificaCampos( ifr_resultado.document.formeditor.dbvar_str_resultaprovado.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nResultado - Aprovado pelo Cliente');
	
	var_msg_resultado += VerificaCampos( ifr_resultado.document.formeditor.dbvar_date_resultdatahomol.value
							 , ''
						 	 , 'A'
							 , false
							 , false
							 , ''
							 , '\nResultado - Data Aprovação');
	
	var_msg_resultado += VerificaCampos( ifr_resultado.document.formeditor.dbvar_str_resultmotivoreprov.value
							 , ''
						 	 , 'A'
							 , false
							 , false
							 , ''
							 , '\nResultado - Motivo Não-Aprovação');
	
	var_msg_resultado += VerificaCampos( ifr_resultado.document.formeditor.dbvar_str_resultobs.value
							 , ''
							 , 'A'
							 , false
							 , false
							 , ''
							 , '\nResultado - Observações');
	*/
	
	if ((var_msg_material != '') || (var_msg_requisitos != '') || (var_msg_processo != '') || (var_msg_equipamento != '') || (var_msg_ferramenta_uso != '') || (var_msg_teste != '') || (var_msg_resultado != '')) {
		if (var_msg_material != '')       alert('Verificar seguintes campos:\n' + var_msg_material);
		if (var_msg_requisitos != '')     alert('Verificar seguintes campos:\n' + var_msg_requisitos);
		if (var_msg_processo != '')       alert('Verificar seguintes campos:\n' + var_msg_processo);
		if (var_msg_equipamento != '')    alert('Verificar seguintes campos:\n' + var_msg_equipamento);
		if (var_msg_ferramenta_uso != '') alert('Verificar seguintes campos:\n' + var_msg_ferramenta_uso);
		if (var_msg_teste != '')          alert('Verificar seguintes campos:\n' + var_msg_teste);
		if (var_msg_resultado != '')      alert('Verificar seguintes campos:\n' + var_msg_resultado);
		
		return false;
	}
	else {
		return true;
	}
}


function ok(){
  AplicaIFrames();
  document.formeditor.DEFAULT_LOCATION.value = "../modulo_JmsProjeto/data.php";
  document.formeditor.submit();
}

function cancelar() {
  document.location = "data.php";
}


function aplicar() {
  if (Verifica()) {
    AplicaIFrames();
    document.formeditor.DEFAULT_LOCATION.value = "../modulo_JmsProjeto/update.php?var_chavereg=<?php echo($intCodDado); ?>&var_tabactived=" + myTabActived;
    document.formeditor.submit();
  }
}

function setUpCaseValue(frmOBJ) {
  if (frmOBJ.type != "hidden") {
    frmOBJ.value = frmOBJ.value.toUpperCase();	
  }
}

function AplicaIFrames() {
  var i;

  for(i=0; i<(document.formeditor.elements.length); i++)                    { setUpCaseValue(document.formeditor.elements[i]);	} 
  for(i=0; i<(ifr_material.document.formeditor.elements.length); i++)	    { setUpCaseValue(ifr_material.document.formeditor.elements[i]);	} 
  for(i=0; i<(ifr_requisitos.document.formeditor.elements.length); i++)	    { setUpCaseValue(ifr_requisitos.document.formeditor.elements[i]); } 
  for(i=0; i<(ifr_processo.document.formeditor.elements.length); i++)	    { setUpCaseValue(ifr_processo.document.formeditor.elements[i]);	} 
  for(i=0; i<(ifr_ficha_consumo.document.formeditor.elements.length); i++)  { setUpCaseValue(ifr_ficha_consumo.document.formeditor.elements[i]); } 
  for(i=0; i<(ifr_amostras.document.formeditor.elements.length); i++)	    { setUpCaseValue(ifr_amostras.document.formeditor.elements[i]);	} 
  for(i=0; i<(ifr_equipamento.document.formeditor.elements.length); i++)    { setUpCaseValue(ifr_equipamento.document.formeditor.elements[i]); } 
  for(i=0; i<(ifr_ferramenta_uso.document.formeditor.elements.length); i++) { setUpCaseValue(ifr_ferramenta_uso.document.formeditor.elements[i]); } 
  for(i=0; i<(ifr_teste.document.formeditor.elements.length); i++)	    { setUpCaseValue(ifr_teste.document.formeditor.elements[i]); } 
  for(i=0; i<(ifr_resultado.document.formeditor.elements.length); i++)	    { setUpCaseValue(ifr_resultado.document.formeditor.elements[i]); } 

  ifr_material.document.formeditor.submit();
  ifr_requisitos.document.formeditor.submit();
  ifr_processo.document.formeditor.submit();
  // ifr_ficha_consumo.document.formeditor.submit();
  // ifr_amostras.document.formeditor.submit();
  ifr_equipamento.document.formeditor.submit();
  ifr_ferramenta_uso.document.formeditor.submit();
  
  ifr_teste.document.formeditor.DEFAULT_LOCATION.value = "../modulo_JmsProjeto/updtab_teste.php?var_chavereg=<?php echo($intCodDado); ?>";
  ifr_teste.document.formeditor.submit();

  ifr_resultado.document.formeditor.DEFAULT_LOCATION.value = "../modulo_JmsProjeto/updtab_resultado.php?var_chavereg=<?php echo($intCodDado); ?>";
  ifr_resultado.document.formeditor.submit();
}
//********************************************************************************************************
//******************** Funções de ação dos botões [formeditor_000 - prinicpal] - Fim *********************
//********************************************************************************************************
//-->

</script>
</head>
<body bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(getsession("sys_theme")); ?>_main.jpg">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
 <tr>
   <td align="center" valign="top">
	  <?php athBeginFloatingBox("900","none","PROJETO (Edição)",CL_CORBAR_GLASS_1); ?>
		  <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #A6A6A6;">
		   <form name="formeditor" action="../_database/athupdatetodb.php" method="post">
			<input type="hidden" name="DEFAULT_TABLE"          value="jms_projeto">
			<input type="hidden" name="FIELD_PREFIX"           value="dbvar_">
			<input type="hidden" name="RECORD_KEY_NAME"        value="cod_projeto">
			<input type="hidden" name="RECORD_KEY_VALUE"       value="<?php echo($intCodDado); ?>">
			<input type="hidden" name="DEFAULT_LOCATION"       value="">
			<input type="hidden" name="dbvar_str_sys_usr_upd"  value="<?php echo(getsession("sys_id_usuario")); ?>">
			<input type="hidden" name="dbvar_datetime_sys_dtt_upd" value="<?php echo(dDate(CFG_LANG,now(),true)); ?>">	

			<tr><td height="10" style="padding:10px"></td></tr>
			<tr> 
			  <td align="center" valign="top">
				<table width="850" border="0" cellspacing="0" cellpadding="4">
					<tr>
						<td width="188" align="right"><b>Cód. Projeto: </b></td>
						<td width="596"><?php echo(getValue($objRS,"cod_projeto")); ?></td>
					</tr>
					<tr bgcolor="#FAFAFA">
						<td width="188" align="right"><b>Cliente: </b></td>
						<td>
							<select style="width:180px;" name="dbvar_str_cod_estab" >
								<option value=""> selecione...</option>
								<?php echo(montaCombo($objConn,"SELECT cod_estab, nome FROM cad_estabelecimento WHERE (('" . getsession("sys_grp_user") . "' = 'NORMAL') AND (sys_usr_ins='" . getsession("sys_id_usuario") . "')) OR ('" . getsession("sys_grp_user") . "' <> 'NORMAL') ORDER BY nome ", "cod_estab", "nome", getValue($objRS,"cod_estab"))); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td width="188" align="right"><b>Nome da Aplicação: </b></td>
						<td><input type="text" name="dbvar_str_aplicacaonome" size="80" value="<?php echo(getValue($objRS,"aplicacaonome")); ?>"></td>
					</tr>
					<tr bgcolor="#FAFAFA">
						<td width="188" align="right"><b>Nome da  Peça/Obra: </b></td>
						<td><input type="text" name="dbvar_str_pecaobranome" size="80" value="<?php echo(getValue($objRS,"pecaobranome")); ?>"></td>
					</tr>
					<tr>
						<td width="188" align="right" ><b>Nome do Produto Final: </b></td>
						<td><input type="text" name="dbvar_str_produtofinalnome" size="80" value="<?php echo(getValue($objRS,"produtofinalnome")); ?>"></td>
					</tr>
					<tr align="left">
				  	  <td height="10" colspan="2">
						<div id="dhtmlgoodies_tabView1">
							<div class="dhtmlgoodies_aTab"><iframe name="ifr_material"       id="ifr_material"       src="updtab_material.php"       frameborder="0" scrolling="no" style="width:826; height:480; display:block;"></iframe></div>	
							<div class="dhtmlgoodies_aTab"><iframe name="ifr_requisitos"     id="ifr_requisitos"     src="updtab_requisitos.php"     frameborder="0" scrolling="no" style="width:826; height:500; display:block;"></iframe></div>	
							<div class="dhtmlgoodies_aTab"><iframe name="ifr_processo"       id="ifr_processo"       src="updtab_processo.php"       frameborder="0" scrolling="no" style="width:826; height:440; display:block;"></iframe></div>	
							<div class="dhtmlgoodies_aTab"><iframe name="ifr_ficha_consumo"  id="ifr_ficha_consumo"  src="updtab_ficha_consumo.php"  frameborder="0" scrolling="no" style="width:826; height:780; display:block;"></iframe></div>	
							<div class="dhtmlgoodies_aTab"><iframe name="ifr_amostras"       id="ifr_amostras"       src="updtab_amostras.php"       frameborder="0" scrolling="no" style="width:826; height:770; display:block;"></iframe></div>	
							<div class="dhtmlgoodies_aTab"><iframe name="ifr_equipamento"    id="ifr_equipamento"    src="updtab_equipamento.php"    frameborder="0" scrolling="no" style="width:826; height:720; display:block;"></iframe></div>	
							<div class="dhtmlgoodies_aTab"><iframe name="ifr_ferramenta_uso" id="ifr_ferramenta_uso" src="updtab_ferramenta_uso.php" frameborder="0" scrolling="no" style="width:826; height:780; display:block;"></iframe></div>	
							<div class="dhtmlgoodies_aTab"><iframe name="ifr_teste"          id="ifr_teste"          src="updtab_teste.php"          frameborder="0" scrolling="no" style="width:826; height:820; display:block;"></iframe></div>	
							<div class="dhtmlgoodies_aTab"><iframe name="ifr_resultado"      id="ifr_resultado"      src="updtab_resultado.php"      frameborder="0" scrolling="no" style="width:826; height:550; display:block;"></iframe></div>	
						</div>	
					  </td>
					</tr>
					<tr align="left"><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>
					<tr><td height="1" colspan="2" class="linedialog"></td></tr>
					<tr>
					  <td colspan="2">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" >
							<tr>
								<td width="1%" align="right" style="padding:10px 0px 10px 10px;" nowrap>
									<button onClick="ok(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
					   				<button onClick="cancelar(); return false;"><?php echo( getTText("cancelar",C_UCWORDS)); ?></button>
					   				<button onClick="aplicar(); return false;"><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
								</td>
							</tr>
						</table>
					  </td>
					 </tr>
				</table>
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
 $objResult->closeCursor();
?>