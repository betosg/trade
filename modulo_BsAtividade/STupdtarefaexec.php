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
	$intCodDado		= request("var_todo_cod_todolist");
	$strTitulo		= request("var_todo_titulo");
	$strDescricao	= request("var_todo_descricao");
	$intCategoria	= request("var_todo_categoria");
	$strPrioridade	= request("var_todo_prioridade");
	$strSituacao    = request("var_todo_situacao");
	$strIDResp	    = request("var_todo_id_responsavel");
	$strIDExec		= request("var_todo_id_ult_executor");
	$strPrevDtIni	= request("var_todo_prev_dt_ini");
	$strPrevHrIni1	= request("var_todo_prev_hr_ini_1");
	$strPrevHrIni2	= request("var_todo_prev_hr_ini_2");
	$strPrevHoras1	= request("var_todo_prev_horas_1");
	$strPrevHoras2	= request("var_todo_prev_horas_2");
	$strArqAnexo	= request("var_todo_arquivo_anexo");
	$strLocation 	= request("DEFAULT_LOCATION");
	$boolMail		= TRUE;
	
	// ABRE OBJETO PARA MANIPULAÇÃO NO BANCO
	$objConn = abreDBConn(CFG_DB);
		
	// INSERT DE TAREFA
	$objConn->beginTransaction();
	try{
		$strSQL = "
			UPDATE tl_todolist SET 
				  titulo = '".prepStr($strTitulo)."'
				, descricao = '".prepStr($strDescricao)."'
				, id_responsavel = '".prepStr($strIDResp)."'
				, id_ult_executor = '".prepStr($strIDExec)."'
				, situacao = '".prepStr($strSituacao)."'
				, cod_categoria = ".$intCategoria."
				, prioridade = '".prepStr($strPrioridade)."'
				, prev_dt_ini = '".cDate(CFG_LANG,$strPrevDtIni,false)."'
				, prev_hr_ini = '".$strPrevHrIni1.":".$strPrevHrIni2."'
				, prev_horas = '".$strPrevHoras1.":".$strPrevHoras2."'
				, arquivo_anexo = '".prepStr($strArqAnexo)."'
				, sys_usr_upd = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'
				, sys_dtt_upd = CURRENT_TIMESTAMP
			WHERE cod_todolist = ".$intCodDado;
		// die($strSQL);
		$objConn->query($strSQL);
		
		// LOCALIZA TAREFA RECÉM INSERIDA e NOME CATEGORIA
		$strSQL = "SELECT tl_categoria.cod_categoria||' - '||tl_categoria.nome AS categoria FROM tl_categoria WHERE cod_categoria = ".$intCategoria;
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
		// INSERT DE TAREFA
		$objConn->beginTransaction();
		try{
			$strSQL = "SELECT email FROM sys_usuario WHERE id_usuario = '".$strIDResp."' OR id_usuario = '".$strIDExec."'";
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
								<td><strong>'.getTText("alteracao_de_tarefa",C_NONE).'</strong></td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td class="td_label">'.getTText("cod_todolist",C_NONE).':</td>
								<td>'.$intCodDado.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("titulo",C_NONE).':</td>
								<td>'.$strTitulo.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("situacao",C_NONE).':</td>
								<td>'.strtoupper($strSituacao).'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("prioridade",C_NONE).':</td>
								<td>'.strtoupper($strPrioridade).'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("responsavel",C_NONE).':</td>
								<td>'.$strIDResp.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("id_ult_executor",C_NONE).':</td>
								<td>'.$strIDExec.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("prev_dt_ini",C_NONE).':</td>
								<td>'.$strPrevDtIni.' '.getTText("as_crase",C_NONE).' '.$strPrevHrIni1.':'.$strPrevHrIni2.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("prev_horas",C_NONE).':</td>
								<td>'.$strPrevHoras1.':'.$strPrevHoras2.'</td>
							</tr>
							<tr>
								<td class="td_label">'.getTText("descricao",C_NONE).':</td>
								<td>'.$strDescricao.'</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
						</table>
					</td>
				</tr>
			</table>';
		
		// CONFIGURA LINHA DE DESTINATÁRIOS
		$strEmailLINE  = "";
		foreach($objResult as $objRS){
			$strEmailLINE .= (getValue($objRS,"email") == "") ? "" : getValue($objRS,"email").",";
			$strEmailLINE  = trim($strEmailLINE,",");
		}
		// echo($strEmailLINE);
		
		// CONFIGURA TÍTULO DO EMAIL / SUBJECT
		$strSUBJECT    = ucwords(CFG_SYSTEM_NAME)." - ".getTText("alteracao_de_tarefa",C_NONE);
		
		// Encaminha o email somente se estiver ONLINE
		if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
			emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
		}
	}
	
    // REDIRECT para a pagina
	redirect($strLocation);
?>