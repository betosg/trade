<?php
	header("content-type: text/xml");
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// abertura de conexão com o BD
	$objConn    = abreDBConn(CFG_DB);	
	// carrega o nome do modulo 'corrente'
	$strSesPfx  = strtolower(str_replace("modulo_","",basename(getcwd())));
	// com o nome do modulo, busca o select da seção
	$strSQL     = getsession($strSesPfx . "_select");
	
	// die($strSQL);
	
	// efetua busca dos eventos e das agendas
	// com base nos parametros da sessao [SQL]
	// - do modulo corrente
	try{
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	/*echo("<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>");
	echo("<data>");
	foreach($objResult as $objRS){
		echo("<event id=\"".getValue($objRS,"cod_agenda")."\">
			  	<start_date>".getValue($objRS,"prev_dtt_ini")."</start_date>
				<end_date>".substr(getValue($objRS,"prev_dtt_fim"),0,19)."</end_date>
				<text>".getValue($objRS,"titulo")."</text>
				<ag_categoria>".getValue($objRS,"categoria")."</ag_categoria>
				<ag_prioridade>".getValue($objRS,"prioridade")."</ag_prioridade>
				<details>".getValue($objRS,"descricao")."</details>
			  </event>");
	}
	echo("</data>");
	$objResult->closeCursor();
	$objConn = NULL;
	*/
	
  	echo("<data>");
	foreach($objResult as $objRS){
		echo("<event id=\"".returnCodigo(getValue($objRS,"cod_agenda"))."\">
			  	<start_date>".returnCodigo(getValue($objRS,"prev_dtt_ini"))."</start_date>
				<end_date>".returnCodigo(substr(getValue($objRS,"prev_dtt_fim"),0,19))."</end_date>
				<text><![CDATA[".returnCodigo(getValue($objRS,"titulo"))."]]></text>
				<ag_categoria><![CDATA[".returnCodigo(getValue($objRS,"categoria"))."]]></ag_categoria>
				<ag_prioridade><![CDATA[".returnCodigo(getValue($objRS,"prioridade"))."]]></ag_prioridade>
				<details><![CDATA[".returnCodigo(getValue($objRS,"descricao"))."]]></details>
			  </event>");
	}
	echo("</data>");
	$objResult->closeCursor();
	$objConn = NULL;
?>