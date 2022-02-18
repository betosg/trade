<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		return($prColor);
	}
	
	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");

	// REQUESTS
	$intCodAgenda	= request("var_chavereg");		// cod_agenda para o qual resposta irá ser relacionada
	$strUsrCriador  = request("var_sys_usr_ins");   // id do usuario criador da resposta
	$strUsrResposta = request("var_id_usuario"); 	// id do usuario que fez a resposta
	$dtInsResposta	= cDate(CFG_LANG,request("var_dtt_ins_resposta"),true);	// dtt resposta | != da data de inserção [sys_dtt_ins]
	$strTextoResp	= request("var_resposta"); 		// texto de resposta
	$strLocation	= request("DEFAULT_LOCATION");
		
	// cod_agenda nao pode vir vazio
	if($intCodAgenda == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("agenda_cod_null",C_NONE),'STinsresposta.php?var_chavereg='.$intCodAgenda,'erro','1');
		die();
	}
	
	// consistencia para campos vazios
	$strErrMsg = "";
	$strErrMsg .= ($strUsrCriador  == "") ? getTText("sys_usr_ins_vazio",C_NONE)."<br />" 		: "";
	$strErrMsg .= ($strUsrResposta == "") ? getTText("id_usuario_vazio",C_NONE)."<br />" 		: "";
	$strErrMsg .= ($dtInsResposta  == "") ? getTText("dtt_ins_resposta_vazio",C_NONE)."<br />"  : "";
	$strErrMsg .= ($strTextoResp   == "") ? getTText("texto_vazio",C_NONE)."<br />"  			: "";
	$strErrMsg  = ($strErrMsg != "") ? getTText("campos_nao_informados",C_NONE)."<br /><br />".$strErrMsg : $strErrMsg;
	
	if($strErrMsg != ""){
		mensagem("err_dados_titulo","err_dados_submit_desc",$strErrMsg,'STinsresposta.php?var_chavereg='.$intCodAgenda,'aviso','1','no');
		die();
	}
	
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
		
	// faz inserção de resposta no DB e redirect
	// para página que lista RESPOSTAS TABLESORT
	try{
		$strSQL = "INSERT INTO ag_resposta(
						  cod_agenda
						, id_usuario
						, resposta
						, dtt_resposta
						, sys_usr_ins
						, sys_dtt_ins)
				   VALUES(
				   		   ".$intCodAgenda."
						, '".prepStr($strUsrResposta)."'
						, '".prepStr($strTextoResp)."'
						, '".$dtInsResposta."'
						, '".prepStr($strUsrCriador)."'
						, CURRENT_TIMESTAMP);";
		$objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"STinsresposta.php?var_chavereg=".$intCodAgenda,"erro",1);
		die();
	}
	
	// LOCALIZA AGENDA
	try{
		$strSQL = "
			SELECT
			  	  ag_agenda.id_responsavel
				, ag_agenda.categoria
				, ag_agenda.prioridade
				, ag_agenda.titulo
				, ag_agenda.descricao
				, ag_agenda.prev_dtt_ini
				, ag_agenda.prev_dtt_fim
			FROM
				  ag_agenda
			WHERE ag_agenda.cod_agenda = ".$intCodAgenda;
		$objResult = $objConn->query($strSQL);
		$objRS     = $objResult->fetch();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage()."sdafasdfasdf","STinsresposta.php?var_chavereg=".$intCodAgenda,"erro",1);
		die();
	}
	
	// LOCALIZA TODAS AS RESPOSTAS INSERIDAS, INCLUSIVE ESTA
	try{
		$strSQL = "
			SELECT
			  	  ag_resposta.id_usuario
				, ag_resposta.dtt_resposta
				, ag_resposta.resposta
			FROM
				  ag_resposta
			WHERE ag_resposta.cod_agenda = ".$intCodAgenda."
			ORDER BY ag_resposta.sys_dtt_ins DESC";
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage()."sdafasdfasdf","STinsresposta.php?var_chavereg=".$intCodAgenda,"erro",1);
		die();
	}
	
	// LOCALIZA EMAILS DE CITADOS, LIGANDO USUARIO CITADO
	// COM PJ, PARA COLETAR EMAIL DA PJ EM SI
	try{
		$strSQL = "
			SELECT 
				cad_pj.email 
			FROM cad_pj 
			INNER JOIN sys_usuario ON (cad_pj.cod_pj = sys_usuario.codigo) 	
			INNER JOIN ag_agenda_citado ON (ag_agenda_citado.id_usuario = sys_usuario.id_usuario)
			WHERE ag_agenda_citado.cod_agenda = ".$intCodAgenda."
			ORDER BY cad_pj.email DESC";
		$objResultE = $objConn->query($strSQL);
		// die($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage()."sdafasdfasdf","STinsresposta.php?var_chavereg=".$intCodAgenda,"erro",1);
		die();
	}
	
	// ENVIA EMAIL CONTENDO A AGENDA ATUAL, RESPOSTA ATUAL e TODAS AS RESPOSTAS
	$strBodyEmail  = "";
	$strBodyEmail .= '
		<table cellpadding="0" cellspacing="0" border="0" width="100%" style="text-align:left;padding-bottom:0px;margin-bottom:15px;border-bottom: 1px solid #EEE;" class="general">
		<tr>
			<td colspan="2">
			<table cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align:left;">
				<tr>
					<td class="td_label">&nbsp;</td>
					<td><strong>'.getTText("dados_da_agenda",C_NONE).'</strong></td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td class="td_label">'.getTText("titulo_agenda",C_NONE).':</td>
					<td>'.getValue($objRS,"titulo").'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td class="td_label">'.getTText("categoria_agenda",C_NONE).':</td>
					<td>'.strtoupper(getValue($objRS,"categoria")).'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td class="td_label">'.getTText("prioridade_agenda",C_NONE).':</td>
					<td>'.getValue($objRS,"prioridade").'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td class="td_label">'.getTText("responsavel_agenda",C_NONE).':</td>
					<td>'.getValue($objRS,"id_responsavel").'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td class="td_label">'.getTText("citados_agenda",C_NONE).':</td>
					<td>'.$strCITADOS.'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td class="td_label">'.getTText("prev_ini_agenda",C_NONE).':</td>
					<td>'.dDate(CFG_LANG,getValue($objRS,"prev_dtt_ini"),true).'  até  '.dDate(CFG_LANG,getValue($objRS,"prev_dtt_fim"),true).'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td class="td_label">'.getTText("descricao_agenda",C_NONE).':</td>
					<td>'.getValue($objRS,"descricao").'</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
			</table>
			</td>
		</tr>
		</table>';
	
	if($objResult->rowCount() > 0){
		$strBodyEmail .= '
		<table cellpadding="0" cellspacing="0" border="0" width="100%" style="text-align:left;" class="general">
		<tr>
			<td colspan="2">
			<table cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align:left;">
				<tr>
					<td class="td_label">&nbsp;</td>
					<td><strong>'.getTText("respostas",C_TOUPPER).'</strong></td>
				</tr>';
		foreach($objResult as $objRS){
			$strBodyEmail .= '
				<tr bgcolor="'.getLineColor($strColor).'">
					<td class="td_label">'.getTText("dtt_resposta",C_NONE).':</td>
					<td>'.getValue($objRS,"dtt_resposta").'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td class="td_label">'.getTText("id_usuario",C_NONE).':</td>
					<td>'.strtoupper(getValue($objRS,"id_usuario")).'</td>
				</tr>
				<tr bgcolor="'.getLineColor($strColor).'">
					<td class="td_label">'.getTText("resposta",C_NONE).':</td>
					<td>'.getValue($objRS,"resposta").'</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>';
		}
		$strBodyEmail .= '
			</table>
			</td>
		</tr>
		</table>';
	}	
	
	// CONFIGURA LINHA DE DESTINATÁRIOS
	$strEmailLINE  = "";
	if($objResultE->rowCount() > 0){
		foreach($objResultE as $objRS){
			$strEmailLINE .= (getValue($objRS,"email") == "") ? "" : getValue($objRS,"email").",";
		}
		$strEmailLINE = trim($strEmailLINE,",");
	}

	// CONFIGURA TÍTULO DO EMAIL / SUBJECT
	$strSUBJECT    = ucwords(CFG_SYSTEM_NAME)." - ".getTText("nova_resposta_de_agenda",C_NONE);
		
	// Encaminha o email somente se estiver ONLINE
	if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
		emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
	}
	
	// redirect da página após inserção
	// se comporta corretamente mesmo
	// em OK ou APLICAR
	redirect($strLocation); 
?>