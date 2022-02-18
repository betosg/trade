<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athtranslate.php");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");

	// REQUESTS
	$intCodDado  	= request("var_todo_cod_todo_list");
	$strIDFrom		= request("var_todo_id_from");
	$strIDTo		= request("var_todo_id_to");
	$strPrioridade 	= request("var_todo_prioridade");
	$strResposta	= request("var_todo_resposta");
	$strSigiloso	= request("var_todo_sigiloso");
	$strHoras1		= request("var_todo_horas_1");
	$strHoras2		= request("var_todo_horas_2");
	$strArqAnexo	= request("var_todo_arquivo_anexo");
	$strLocation	= request("DEFAULT_LOCATION");
	$boolMail		= TRUE;
	
	// ABRE OBJETO PARA MANIPULAÇÃO NO BANCO
	$objConn = abreDBConn(CFG_DB);
		
	// INSERT DE TAREFA
	$objConn->beginTransaction();
	try{
		$strSQL = "
			INSERT INTO tl_resposta( 
				  cod_todolist
				, id_from
				, id_to
				, resposta
				, sigiloso
				, horas
				, arquivo_anexo
				, sys_usr_ins
				, dtt_resposta
			) VALUES (
				  ".$intCodDado."
				, '".prepStr($strIDFrom)."'
				, '".prepStr($strIDTo)."'
				, '".prepStr($strResposta)."'
				, '".prepStr($strSigiloso)."'
				, '".$strHoras1.":".$strHoras2."'
				, '".prepStr($strArqAnexo)."'
				, '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."'
				, CURRENT_TIMESTAMP)";
		$objConn->query($strSQL);
		
		// SELECIONA TODAS AS RESPOSTAS DA TAREFA, PARA RÉPLICA NO EMAIL
		$strSQL = "SELECT cod_resposta, dtt_resposta, id_from, id_to, resposta, horas FROM tl_resposta WHERE cod_todolist = ".$intCodDado;
		$objResultR = $objConn->query($strSQL);
		
		// VERIFICA A SITUAÇÃO DA PRIORIDADE
		$strSQL = "SELECT prioridade FROM tl_todolist WHERE cod_todolist = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
		$objRS = $objResult->fetch();
		
		// ATUALIZA TAREFA, CASO ALTERADO
		if(getValue($objRS,"prioridade") != $strPrioridade){
			$strSQL = "UPDATE tl_todolist SET prioridade = '".prepStr($strPrioridade)."' WHERE cod_todolist = ".$intCodDado;
			$objConn->query($strSQL);
		}
		
		// LOCALIZA RESPOSTA RECÉM INSERIDA e NOME CATEGORIA
		$strSQL = "SELECT cod_todolist, tl_categoria.cod_categoria||' - '||tl_categoria.nome AS categoria, titulo, tl_todolist.descricao, prev_dt_ini, prev_hr_ini, prev_horas, id_responsavel, id_ult_executor, situacao, prioridade FROM tl_todolist LEFT JOIN tl_categoria ON (tl_categoria.cod_categoria = tl_todolist.cod_categoria) WHERE cod_todolist = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
		$objRS = $objResult->fetch();
			
		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	
	// ENVIO DE EMAIL
	if($boolMail){
		// LOCALIZA O EMAIL DO USUÁRIO RESPONSÁVEL E EXECUTOR
		$objConn->beginTransaction();
		try{
			$strSQL = "SELECT email FROM sys_usuario WHERE id_usuario = '".$strIDFrom."' OR id_usuario = '".$strIDTo."'";
			$objResult = $objConn->query($strSQL);
			$objConn->commit();
		}catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			$objConn->rollBack();
			die();
		}
		
		// MONTA O CORPO DO EMAIL
		$strBodyEmail  = '';
		$strBodyEmail .= '
			<table cellpadding="0" cellspacing="0" border="0" width="100%" style="text-align:left;" class="general">
				<tr>
					<td colspan="2">
						<table cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align:left;">
							<tr>
								<td class="td_label">&nbsp;</td>
								<td><strong>'.getTText("insercao_de_resposta",C_NONE).'</strong></td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td class="td_label">'.getTText("cod_tarefa",C_NONE).':</td>
								<td>'.getValue($objRS,"cod_todolist").'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("titulo",C_NONE).':</td>
								<td>'.getValue($objRS,"titulo").'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("situacao",C_NONE).':</td>
								<td>'.strtoupper(getValue($objRS,"situacao")).'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("prioridade",C_NONE).':</td>
								<td>'.strtoupper(getValue($objRS,"prioridade")).'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("responsavel",C_NONE).':</td>
								<td>'.getValue($objRS,"id_responsavel").'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("id_ult_executor",C_NONE).':</td>
								<td>'.getValue($objRS,"id_ult_executor").'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("prev_dt_ini",C_NONE).':</td>
								<td>'.dDate(CFG_LANG,getValue($objRS,"prev_dt_ini"),false).' '.getTText("as_crase",C_NONE).' '.getValue($objRS,"prev_hr_ini").'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("prev_horas",C_NONE).':</td>
								<td>'.getValue($objRS,"prev_horas").'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("descricao",C_NONE).':</td>
								<td>'.getValue($objRS,"descricao").'</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
						</table>
					</td>
				</tr>
			</table>';
		
		// FOREACH DAS RESPOSTAS PARA ADICIONAR AO EMAIL
		// dtt_resposta, id_from, id_to, resposta, horas
		if($objResultR->rowCount() > 0){
			$strBodyEmail .= '
				<table cellpadding="0" cellspacing="0" border="0" width="100%" style="text-align:left;margin-top:20px;" class="general">
				<tr><td><strong>'.getTText("respostas",C_NONE).'</strong></td></tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="0" width="100%" style="text-align:left;" class="general">
					<thead style="height:25px;">
						<th width="10%" nowrap style="font-size:10px;border-bottom:1px solid #AAA;border-top:1px solid #AAA;">'.getTText("dtt_resposta",C_NONE).'</th>
						<th width="10%" nowrap style="font-size:10px;border-bottom:1px solid #AAA;border-top:1px solid #AAA;">'.getTText("id_from",C_NONE).'</th>
						<th width="10%" nowrap style="font-size:10px;border-bottom:1px solid #AAA;border-top:1px solid #AAA;">'.getTText("id_to",C_NONE).'</th>
						<th width="10%" nowrap style="font-size:10px;border-bottom:1px solid #AAA;border-top:1px solid #AAA;">'.getTText("horas",C_NONE).'</th>
						<th width="60%" nowrap style="font-size:10px;border-bottom:1px solid #AAA;border-top:1px solid #AAA;">'.getTText("resposta",C_NONE).'</th>
					</thead>
					<tbody>';
			foreach($objResultR as $objRSR){
				$strBodyEmail .= '
					<tr>
						<td nowrap style="border-bottom:1px solid #AAA;height:20px;padding:4px 0px 0px 4px;vertical-align:top;">'.dDate(CFG_LANG,getValue($objRSR,"dtt_resposta"),true).'</td>
						<td nowrap style="border-bottom:1px solid #AAA;height:20px;padding:4px 0px 0px 4px;vertical-align:top;">'.getValue($objRSR,"id_from").'</td>
						<td nowrap style="border-bottom:1px solid #AAA;height:20px;padding:4px 0px 0px 4px;vertical-align:top;">'.getValue($objRSR,"id_to").'</td>
						<td nowrap style="border-bottom:1px solid #AAA;height:20px;padding:4px 0px 0px 4px;vertical-align:top;">'.getValue($objRSR,"horas").'</td>
						<td nowrap style="border-bottom:1px solid #AAA;height:20px;padding:4px 0px 0px 4px;vertical-align:top;">'.getValue($objRSR,"resposta").'</td>
					</tr>';
			}
			$strBodyEmail .= '
					</tbody>
				</table>';
		}
		
		
		// CONFIGURA LINHA DE DESTINATÁRIOS
		$strEmailLINE  = "";
		foreach($objResult as $objRS){
			$strEmailLINE .= (getValue($objRS,"email") == "") ? "" : getValue($objRS,"email").",";
			$strEmailLINE  = trim($strEmailLINE,",");
		}
		// die($strEmailLINE);
		
		// CONFIGURA TÍTULO DO EMAIL / SUBJECT
		$strSUBJECT    = ucwords(CFG_SYSTEM_NAME)." - ".getTText("todolist",C_NONE). " (".getTText("insercao_de_resposta",C_NONE)." ".getTText("id_from",C_NONE)." ".$strIDFrom." ".getTText("id_to",C_NONE)." ".$strIDTo.")";
		
		// Encaminha o email somente se estiver ONLINE
		if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
			emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
		}
	}
	
    // REDIRECT para a pagina
	redirect($strLocation);
?>