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
	$intCodDado		= request("var_chavereg");
	$intCodigo 		= request("var_bs_codigo");
	$strTipo 		= request("var_bs_tipo");
	$strTitulo		= request("var_bs_titulo");
	$strDescricao	= request("var_bs_descricao");
	$intCategoria	= request("var_bs_categoria");
	$strPrioridade	= request("var_bs_prioridade");
	$strSituacao    = request("var_bs_situacao");
	$strIDResp	    = request("var_bs_id_responsavel");
	$strEquipe		= request("var_bs_equipe");
	$chrModelo		= request("var_bs_modelo");
	$strLocation	= request("DEFAULT_LOCATION");
	$boolMail		= false;
	
	// TRATAMENTO PARA BOOLEANO
	$boolModelo = ($chrModelo == "S") ? "TRUE" : "FALSE";
	$strEquipe  = (($strEquipe != "") && (!stristr($strEquipe,";"))) ? ";".$strEquipe : $strEquipe;
	
	// ABRE OBJETO PARA MANIPULAÇÃO NO BANCO
	$objConn = abreDBConn(CFG_DB);
		
	// UPDATE DE ATIVIDADE
	$objConn->beginTransaction();
	try{
		$strSQL = "UPDATE bs_atividade SET codigo = ".$intCodigo.", tipo = '".$strTipo."', titulo = '".prepStr($strTitulo)."', descricao = '".prepStr($strDescricao)."', id_responsavel = '".prepStr($strIDResp)."', situacao = '".prepStr($strSituacao)."', cod_categoria = ".$intCategoria.", prioridade = '".prepStr($strPrioridade)."', modelo = ".$boolModelo.", sys_usr_upd = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."', sys_dtt_upd = CURRENT_TIMESTAMP WHERE cod_atividade = ".$intCodDado;
		// echo($strSQL);
		$objConn->query($strSQL);
		
		// $objResultA = $objConn->query("SELECT MAX(cod_atividade) AS cod_atividade FROM bs_atividade");
		// $objRSA		= $objResultA->fetch();
		
		// VERIFICA SE EXISTEM USUARIOS DE EQUIPE A SER INSERIDO
		// EXPLODE O ARRAY PARA INSERIR CADA UM DOS USUARIOS
		if($strEquipe != ""){
			if(stristr($strEquipe,";")){
				// DELETA TODA A GALERA ANTES DE REINSERIR
				$strSQL = "DELETE FROM bs_equipe WHERE cod_atividade = ".$intCodDado;
				$objConn->query($strSQL);
				
				$arrEquipe = explode(";",$strEquipe);
				for($auxCounter = 0; $auxCounter <= count($arrEquipe); $auxCounter++){
					// INSERE EQUIPE
					if($arrEquipe[$auxCounter] != ""){
						$strSQL = "
							INSERT INTO bs_equipe( 
								  cod_atividade
								, id_usuario
								, sys_usr_ins
								, sys_dtt_ins
							) VALUES (
								  ".$intCodDado."
								, '".prepStr($arrEquipe[$auxCounter])."'
								, '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."'
								, CURRENT_TIMESTAMP)";
						$objConn->query($strSQL);
					}
				}
			}
		}
					
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
								<td><strong>'.getTText("insercao_de_tarefa",C_NONE).'</strong></td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td class="td_label">'.getTText("cod_todolist",C_NONE).':</td>
								<td>'.getValue($objRS,"cod_todolist").'</td>
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
		$strSUBJECT    = ucwords(CFG_SYSTEM_NAME)." - ".getTText("insercao_de_tarefa",C_NONE);
		
		// Encaminha o email somente se estiver ONLINE
		if (($_SERVER["SERVER_NAME"] == "www." . CFG_SYSTEM_NAME . ".com.br") || ($_SERVER["SERVER_NAME"] == CFG_SYSTEM_NAME . ".proevento.com.br")){
			emailNotify($strBodyEmail,$strSUBJECT,$strEmailLINE,CFG_EMAIL_SENDER);
		}
	}
	
    // REDIRECT para a pagina
	redirect($strLocation);
?>