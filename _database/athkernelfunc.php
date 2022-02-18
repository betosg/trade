<?php

/************************************* Modulo Functions ******************************************/
function initModuloParams($prModulo) {
    //Se por algum motivo n�o consegue buscar o nome do sistema na SESSION ent�o esta deve ter expirado
    if (getsession(CFG_SYSTEM_NAME . "_db_name")=="") { mensagem("err_session_expired_titulo", "err_session_expired_desc","","","erro","1"); die(); }
    
	$objConnLocal = abreDBConn(CFG_DB);

	try{
		$strSQL = "SELECT cod_app, dir_app, tabela_app, descritor_grp, grid_default, grid_query, num_per_page, rotulo_app
						, descricao_app, descricao_dialog, titulo_app, subtitulo_app
						, menucombo_rotulo, menucombo_valores, menucombo_tipo, menucombo_images, js_onload
				     FROM sys_app 
					WHERE dir_app = '" . $prModulo . "'";
		$objResult = $objConnLocal->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
			
	$strSessionPfx = strtolower(str_replace("modulo_","",$prModulo));
	setsession($strSessionPfx . "_field_detail", "");
	setsession($strSessionPfx . "_value_detail", "");
		
	if($objResult->rowCount() > 0) {
		foreach($objResult as $objRS){
			setsession($strSessionPfx . "_dir_modulo"   	,getValue($objRS,"dir_app"));
			setsession($strSessionPfx . "_tabela_modulo"	,getValue($objRS,"tabela_app"));
			setsession($strSessionPfx . "_descritor_grp"	,getValue($objRS,"descritor_grp"));
			setsession($strSessionPfx . "_chave_app"    	,getValue($objRS,"cod_app"));
			setsession($strSessionPfx . "_num_per_page" 	,getValue($objRS,"num_per_page"));
			$strSQL = getValue($objRS,"grid_query");
			$strSQL = str_replace("&quot;","\"",$strSQL);
			$strSQL = str_replace(";","",$strSQL);
			$strSQL = replaceParametersSession($strSQL);
			setsession($strSessionPfx . "_select_orig"      ,$strSQL);
			setsession($strSessionPfx . "_select"           ,$strSQL);
			setsession($strSessionPfx . "_titulo"	       	,getValue($objRS,"rotulo_app"));
			setsession($strSessionPfx . "_titulo_app"		,replaceParametersSession(getValue($objRS,"titulo_app")));
			setsession($strSessionPfx . "_subtitulo_app"	,replaceParametersSession(getValue($objRS,"subtitulo_app")));
			setsession($strSessionPfx . "_descricao_app"	,replaceParametersSession(getValue($objRS,"descricao_app")));
			setsession($strSessionPfx . "_descricao_dialog"	,replaceParametersSession(getValue($objRS,"descricao_dialog")));
			setsession($strSessionPfx . "_menucombo_rotulo" ,getValue($objRS,"menucombo_rotulo"));
			setsession($strSessionPfx . "_menucombo_valores",getValue($objRS,"menucombo_valores"));
			setsession($strSessionPfx . "_menucombo_tipo"   ,getValue($objRS,"menucombo_tipo"));
			setsession($strSessionPfx . "_menucombo_images" ,getValue($objRS,"menucombo_images"));
			setsession($strSessionPfx . "_js_onload"        ,getValue($objRS,"js_onload"));
			
			setsession(CFG_SYSTEM_NAME . "_modulo_atual"    ,$prModulo);
			
			if(getValue($objRS,"grid_default") == ""){
				setsession($strSessionPfx . "_grid_default" ,"data.php");
			}
			else{
				setsession($strSessionPfx . "_grid_default" ,getValue($objRS,"grid_default"));
			}
			
			setsession($strSessionPfx . "_select_orig",str_replace("\r\n","",getsession($strSessionPfx . "_select_orig")));
			
			$strRegExp  = "/select(.|\n)*from|\bas\b( +[[:alnum:]_]+ +)|(,*)|\bon\b( *)|((\(*)([[:alnum:]_]+(\.[[:alnum:]_]+)?( *= *)[[:alnum:]_]+(\.[[:alnum:]_]+)?)(\)*))|((\bleft\b|\bright\b|\binner\b|\bcross\b)(( *)\bouter\b)? \bjoin\b( *)|( *)$)|(\bwhere\b|\border by\b|\bgroup by\b|\blimit\b|\bunion\b)(.*)/i";
			$strTabelas = trim(preg_replace($strRegExp,"",getsession($strSessionPfx . "_select_orig")));
			
			setsession($strSessionPfx . "_tabelas" ,preg_replace("/ +/", ",", $strTabelas));
			
			try{
				$objRSNumFilhos = $objConnLocal->query(" SELECT count(DISTINCT dlg_grp) AS num_filhos FROM sys_descritor_campos_edicao WHERE cod_app = " . getsession($strSessionPfx . "_chave_app"))->fetch();
				setsession($strSessionPfx . "_num_filhos", getValue($objRSNumFilhos,"num_filhos"));
			}
			catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				die();
			}
		}
	}
	else{
		mensagem("err_dados_titulo","err_dados_obj_desc",getTText("modulo_invalido",C_NONE),"","erro",1);
		die();
	}
	
	// Testa se a aplica��o tem algum direito,
	// mas n�o testa UM DIREITO em espec�fico
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSessionPfx . "_chave_app"));

	$objResult->closeCursor();
	$objConn = NULL;
	$objLang = NULL;
}

/************************************* Dialog Functions ******************************************/
function initDialogParams($prConnLocal, $prOperacao, $prExec, $prCodDado){
	global $strSesPfx;
	$retValue = array();
	
	
	//************ Inicializa os param�tros da dialog
	switch(strtoupper($prOperacao)){
		case "INS": 
			$retValue["dlg_titulo"] = "insercao";
			$retValue["dlg_action"] = ($prExec != "") ? $prExec : "../_database/athinserttodb.php";
			$retValue["dlg_aviso1"] = getTText("rotulo_dialog",C_NONE);
			$retValue["dlg_aviso2"] = "";
			$retValue["dlg_location_aplicar"] = "../_fontes/insupddelmastereditor.php?var_oper=INS&var_exec=" . $prExec . "&var_basename=" . getsession($strSesPfx . "_dir_modulo");
			break;
		case "UPD":
			$retValue["dlg_titulo"] = "edicao";
			$retValue["dlg_action"] = ($prExec != "") ? $prExec : "../_database/athupdatetodb.php";
			$retValue["dlg_aviso1"] = getTText("rotulo_dialog",C_NONE);
			$retValue["dlg_aviso2"] = "";
			$retValue["dlg_location_aplicar"] = "../_fontes/insupddelmastereditor.php?var_oper=UPD&var_chavereg=" . $prCodDado . "&var_exec=" . $prExec . "&var_basename=" . getsession($strSesPfx . "_dir_modulo");
			break;
		case "VIE":
			$retValue["dlg_titulo"] = "visualizacao";
			$retValue["dlg_action"] = ($prExec != "") ? $prExec : "../_fontes/insupddelmastereditor.php?var_oper=UPD&var_chavereg=" . $prCodDado . "&var_basename=" . getsession($strSesPfx . "_dir_modulo");
			$retValue["dlg_aviso1"] = "";
			$retValue["dlg_aviso2"] = "";
			$retValue["dlg_location_aplicar"] = "../_fontes/insupddelmastereditor.php?var_oper=UPD&var_chavereg=" . $prCodDado . "&var_exec=" . $prExec . "&var_basename=" . getsession($strSesPfx . "_dir_modulo");
			break;
		case "DEL":
			$retValue["dlg_titulo"] = "delecao";
			$retValue["dlg_action"] = ($prExec != "") ? $prExec : "../_database/athdeletetodb.php";
			$retValue["dlg_aviso1"] = "";
			$retValue["dlg_aviso2"] = getTText("aviso_delete_txt",C_NONE);
			$retValue["dlg_location_aplicar"] = "";
			break;
		default:
			mensagem("err_dados_titulo","err_dados_obj_desc","","","erro",1);
			die();
	}
	
	// Est� aqui porque � igual para todos
	$retValue["dlg_location_default"] = "../_fontes/" . getsession($strSesPfx . "_grid_default") . "?var_basename=" . getsession($strSesPfx . "_dir_modulo");
	
	
	//************ Recupera a o campo chave e a tabela principal da dialog
	try{
		$strSQL = " SELECT nome, nome_tabela 
					FROM sys_descritor_campos_edicao 
					WHERE cod_app = " . getsession($strSesPfx . "_chave_app") . " 
					AND (descritor_grp = '" . getsession($strSesPfx . "_descritor_grp") . "' OR descritor_grp IS NULL)
					AND classe = 'CHAVE' ";
		$objResult = $prConnLocal->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
		
	if($objRS = $objResult->fetch()){	
		$retValue["dlg_campo_chave"] = getValue($objRS,"nome");
		$retValue["dlg_nome_tabela"] = getValue($objRS,"nome_tabela");
	}
	else{
		mensagem("err_dados_titulo","err_dados_obj_desc","","","erro",1);
		die();
	}
		
	$objResult->closeCursor();
	
	return($retValue);
}


function showButtonsArea($prDialogGrp, $prOperacao, $prCodDialogGrid, $prNumFilhos, $prAviso){
	$retValue = "";
	$strAux = "";

	if($prNumFilhos == 1 || ($prDialogGrp == "000" && $prOperacao != "INS")){
		if($prOperacao == "INS" || $prOperacao == "UPD"){
			$strAux = "<button onClick=\"ok('" . $prOperacao . "'); return false;\" class='inputcleanActionOk'>Enviar</button>
					   <button onClick=\"cancelar(); return false;\" class='inputcleanActionCancelar'>" . getTText("cancelar",C_UCWORDS) . "</button>
					   <!--button onClick=\"aplicar('" . $prOperacao . "'); return false;\">" . getTText("aplicar",C_UCWORDS) . "</button-->";
		}
		elseif($prOperacao == "DEL" || $prOperacao == "VIE"){
			$strAux = "<button onClick=\"ok('" . $prOperacao . "'); return false;\" class='inputcleanActionOk'>Enviar</button>
					   <button onClick=\"cancelar(); return false;\" class='inputcleanActionCancelar'>" . getTText("cancelar",C_UCWORDS) . "</button>";
		}
	}
	else{
		if($prOperacao == "INS"){
			$strAux = "
					   <button onClick=\"ok('" . $prOperacao . "'); return false;\" class='inputcleanActionOk'>Enviar</button>
			           <button onClick=\"cancelar(); return false;\" class='inputcleanActionCancelar'>" . getTText("cancelar",C_UCWORDS) . "</button>
					   <!--button onClick=\"aplicar('UPD'); return false;\" class='inputcleanActionOk'>" . getTText("aplicar",C_UCWORDS) . "</button-->";
		}
		elseif($prOperacao == "UPD"){
			$strAux = "<button onClick=\"submeterFormRel('" . $prDialogGrp . "','" . $prCodDialogGrid . "'); return false;\" class='inputcleanActionOk'>" . getTText("aplicar",C_UCWORDS) . "</button>";
		}
	}
	
	
	
	if($strAux != ""){
		$retValue = "
				<tr>
				  <td colspan=\"2\" class=\"destaque_med\" style=\"padding-top:5px; padding-right:25px\" align=\"left\">" . getTText("campos_obrig",C_NONE) . "</td>
				</tr>
				<tr><td colspan=\"2\" class=\"linedialog\"></td></tr>
				<tr>
				  <td colspan=\"2\">
					<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
						<tr>
						   <td style=\"padding:10px 0px 10px 10px;\">";

						if($prAviso != "" && $prDialogGrp == "000"){
							$retValue .= "
								  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
									<tr>
										<td width=\"1%\"><img src=\"../img/mensagem_aviso.gif\" border=\"0\" hspace=\"10\"></td>
										<td align=\"left\">" . $prAviso . "</td>
									</tr>
								  </table>";
						}
					
		$retValue .= "
						   </td>
						   <td width=\"1%\" align=\"right\" style=\"padding:10px 0px 10px 10px;\" nowrap>" . $strAux . "</td>
						</tr>
					</table>
				  </td>
				</tr> 
		";
		
		if($prDialogGrp != "000") { $retValue .= "<tr><td height=\"20\" colspan=\"2\"></td></tr>"; } 
	
	}
	
	return($retValue);
}


function initGridParams($prAcao, $prOrderCol, $prOrderDir, $prNumCurPage){
	// Se for do tipo single pega as variaveis necess�rias para seu processamento
	if(getsession($strSesPfx . "_acao") == "single"){
		$strAcao = getsession($strSesPfx . "_acao");
		$strFieldName = getsession($strSesPfx . "_aux_fieldname");
		$strFormName  = getsession($strSesPfx . "_aux_formname");
	}

	//Recupera a string SQL do session, tirando ponto e v�rgula, que mais tarde pode atrapalhar na manipula��o da consulta.
	$strSQLGrid = str_replace(";","",getsession($strSesPfx . "_select"));

	//Cria um array sendo o ORDER BY como o separador
	$arrSQLGrid = explode("ORDER BY", str_replace(";","",getsession($strSesPfx . "_select"))); 

	//Define uma vari�vel booleana afim de verificar se � um tipo de exporta��o ou n�o
	$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc");

	//Exporta��o para excel, word e adobe reader
	if($boolIsExportation){
		//Coloca o cabe�alho de download do arquivo no formato especificado de exporta��o
		header("Content-type: application/force-download"); 
		header("Content-Disposition: attachment; filename=Modulo_" . getTText(getsession($strSesPfx . "_titulo"),C_UCWORDS) . "_" . time() . $strAcao);
		
		$strLimitOffSet = "";
	}   
	else{
		/**************************************************************************************************** /
	      Esta parte do condicional � para deixar a ordena��o na exporta��o e deixar incluir os scripts de js 
		  e retira a pagina��o dos resultados caso for requisitada qualquer tipo de exporta��o 
		/******************************************************************************************************/
		
		include_once("../_scripts/scripts.js");
		include_once("../_scripts/STscripts.js");
		
		//Prepara��o dos par�metros necess�rios para a pagina��o da grade
		if(empty($intNumCurPage) || $intNumCurPage < 1) {
			$intNumCurPage   = 1;
			$intTotalPaginas = 1;
		}
		
		if(!empty($strOrderCol) && !empty($strOrderDir)){
			//Coloca a ordena��o solicitada
			$strSQLGrid = $arrSQLGrid[0] . " ORDER BY " . $strOrderCol . " " . $strOrderDir;
		}
		else{
			//Coloca o ORDER BY 1, ou seja, ordena pela primeira coluna as consultas que n�o tem ordena��o
			$strSQLGrid = (!isset($arrSQLGrid[1])) ? $arrSQLGrid[0] . " ORDER BY 1 ASC " : implode(" ORDER BY ", $arrSQLGrid);
		}
		
	}
	try{
		$strLimitOffSet = "";
		if(getsession($strSesPfx . "_num_per_page") != ""){
			//Recupera��o do numero de registros inseridos na tabela do m�dulo
			$strSQLCount = "SELECT COUNT(*) AS total FROM " . preg_replace("/select(.*)from/i","",preg_replace("/\r\n*/i","",$arrSQLGrid[0]));
			$objRSCount  = $objConn->query($strSQLCount)->fetch();
			
			$intTotalRegistros = getValue($objRSCount,"total");
			$intTotalPaginas   = $intTotalRegistros/getsession($strSesPfx . "_num_per_page");
			
			//Aqui ele formata o resultado para valor inteiro
			$intTotalPaginas = ($intTotalPaginas > round($intTotalPaginas)) ? round($intTotalPaginas) + 1 : round($intTotalPaginas); 
			
			//Formata��o da pagina��o dentro da consulta
			$strLimitOffSet = " LIMIT " . getsession($strSesPfx . "_num_per_page") . " OFFSET " . strval(getsession($strSesPfx . "_num_per_page") * ($intNumCurPage - 1));
		}
	} 
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

	try{
		//Formata��o final da consulta e execu��o
		$strSQLGrid = removeTagSQL($strSQLGrid);
		$objResult  = $objConn->query($strSQLGrid . $strLimitOffSet);
		
		//Armazena a string SQL b�sica para que possa ser recuperada em outra inst�ncia
		setsession($strSesPfx . "_select", $strSQLGrid);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
}

function VerificaModoExportacao($prAcao, $prFileName){
	//Define uma vari�vel booleana afim de verificar se � um tipo de exporta��o ou n�o
	$boolIsExportation = ($prAcao == ".xls") || ($prAcao == ".doc");
	//Exporta��o para excel, word e adobe reader
	if($boolIsExportation){
		//Coloca o cabe�alho de download do arquivo no formato especificado de exporta��o
		header("Content-type: application/force-download"); 
		header("Content-Disposition: attachment; filename=Modulo_" . $prFileName . "_". time() . $prAcao);
		
		return(true);
	}
	else return(false);
}

?>