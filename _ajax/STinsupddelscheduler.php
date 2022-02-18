<?php
	header("Content-Type:text/html; charset=iso-8859-1");
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// include dbfiles
	include_once("../_database/athdbconn.php");
	
	// request do SQL
	$strSQL  = request("var_sql");
	
	// request do tipo de operaчуo  // INS // UPD // DEL
	$strOper = strtoupper(request("var_oper"));
	
	// request do cod_evento para del
	// dos citados na table ag_agenda
	// _citado
	$intCodEvento = request("var_event_id");
	
	// abertura de conexуo com o BD
	$objConn  = abreDBConn(CFG_DB);
	
	// executa SQL encaminhado para esta pag
	try {
		$objConn->beginTransaction();
		$objResult = $objConn->query($strSQL);
		
		if($strOper == 'INS'){
			// faz coleta do ultimo cod_agenda
			// inserido para ser reenviado para
			// a pagina que chamou este ajax, p
			// poder atualizar a ID do evento
			// recщm inserido
			$strSQL    = "SELECT currval('ag_agenda_cod_agenda_seq') as cod_agenda_seq;";
			$objResult = $objConn->query($strSQL);
			$objRS	   = $objResult->fetch();
			$intCodAgenda = getValue($objRS,"cod_agenda_seq");
			
			// como um return para a pagina
			// que esta processando este ajax
			echo($intCodAgenda);
		}
		if($strOper == 'DEL'){
			// deleta os citados pertencentes
			// a esta agenda na tabela ag_
			// agenda_citado|cod_evento = agenda
			$strSQL = "DELETE FROM ag_agenda_citado WHERE cod_agenda = ".$intCodEvento;
			$objConn->query($strSQL);
		}
		$objConn->commit();
	} catch(PDOException $e) {
		header("HTTP/1.0 500 Server internal error");
		echo($e->getMessage());
		$objConn->rollback();
		die();
	}
	
	// fecha cursor para manipulaчуo	
	$objResult->closeCursor();
?>